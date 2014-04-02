<?php
require("config.inc.php");
//require("mk_tables.php");
//require("acc.inc.php");
//require("doge.acc.inc.php");

$dbh = anubis_db_connect();

$result = $dbh->query($show_tables);
db_error();

while ($row = $result->fetch(PDO::FETCH_NUM))
{
  if ($row[0] == "accounts")
    $gotaccountstbl = 1;
  if ($row[0] == "accgroups")
    $gotgroupstbl = 1;
  if ($row[0] == "exchanges")
  	$gotexchangestbl = 1;
  if ($row[0] == "users")
    $gotuserstbl = 1;
  if ($row[0] == "configuration")
    $gotconfigtbl = 1;
  if ($row[0] == "hosts")
    $gothoststbl = 1;   
}

if (!isset($gotaccountstbl))
	create_accounts_table();

if (!isset($gotgroupstbl))
	create_accgroups_table();

if (!isset($gotexchangestbl))
	create_exchanges_table();

if (!isset($gotuserstbl))
	create_users_table();

if (!isset($gotconfigtbl))
	include("configtbl.sql.php");

if (!isset($gothoststbl))
	include("hoststbl.sql.php");

db_error();




function create_accounts_table()
{
  global $dbh;
  global $primary_key, $table_props;

    $tblstr = "
  CREATE TABLE IF NOT EXISTS `accounts` (
    `id` ".$primary_key.",
    `group` mediumint(6) DEFAULT '0',
    `name` varchar(255) NOT NULL,
    `address` varchar(34) NOT NULL,
    `received` decimal(65,8) NOT NULL,
    `sent` decimal(65,8) NOT NULL,
    `balance` decimal(65,8) NOT NULL,
    `value` decimal(65,2) NOT NULL,
    `updated` datetime NOT NULL
  )".$table_props.";";

  $dbh->query($tblstr);
}

function create_accgroups_table()
{
  global $dbh;
  global $primary_key, $table_props;

    $tblstr = "
  CREATE TABLE IF NOT EXISTS `accgroups` (
    `id` ".$primary_key. ",
    `name` varchar(255) NOT NULL,
    `currency` varchar(3) NOT NULL DEFAULT 'USD',
    `enabled` int(1) NOT NULL DEFAULT '1'
  )".$table_props.";";

  $dbh->query($tblstr);
  $instblstr = "INSERT INTO `accgroups` (`name`, `currency`, `enabled`) VALUES
				('BTC', 'USD', '1'),
				('DOGE', 'USD', '1'),
				('POT', 'BTC', '1'),
				('VTC', 'USD');";
  $cri = $dbh->exec($instblstr);
db_error();
}
function create_exchanges_table()
{
  global $dbh;
  global $primary_key, $table_props;

    $tblstr = "
  CREATE TABLE IF NOT EXISTS `exchanges` (
    `id` ".$primary_key.",
    `group` mediumint(6) DEFAULT '0',
    `name` varchar(255) NOT NULL,
    `value` decimal(65,8) NOT NULL,
    `updated` datetime NOT NULL
  )".$table_props.";";

  $dbh->query($tblstr);
  
  $instblstr = "INSERT INTO `exchanges` (`id`, `group`, `name`, `value`, `updated`) VALUES
				(1, 1, 'BitStamp', '459.04000000', '2014-03-30 21:27:02'),
				(2, 2, 'ANXPRO', '0.00050300', '2014-03-30 21:27:03'),
				(3, 3, 'POT', '0.00001025', '2014-03-31 17:39:03'),
				(4, 4, 'VTC', '1.50000000', '2014-03-31 21:06:06'),
				(5, 5, 'PRELUDE-DOGE', '.00057500', '2014-04-03 01:04:05');";

$cri = $dbh->exec($instblstr);
db_error();
  
  
}

function create_users_table()
{
  global $dbh;
  global $primary_key, $table_props;

    $tblstr = "
  CREATE TABLE IF NOT EXISTS `users` (
    `id` ".$primary_key. ",
    `username` varchar(65) NOT NULL DEFAULT '',
    `username_hash` varchar(65) NOT NULL,
    `password` varchar(255) NOT NULL DEFAULT ''
  )".$table_props.";";

  $dbh->query($tblstr);
}

if (isset($_POST['Submit']))
{
  $username = $_POST['username'];
  $username_hash = hash('sha256', $_POST['username']); 
  $password = hash('sha256', $_POST['password']);
  
  $updq = "INSERT INTO users (`username`, `username_hash`, `password`) VALUES ('$username', '$username_hash', '$password')";
  $updr = $dbh->exec($updq);
  db_error();
  $para_title = 'User Created!  Login!';
}




$login_q = $dbh->query("SELECT COUNT(*) FROM `users` LIMIT 1");

	db_error();
	if ($login_q->fetchColumn() == 0)
	  {
	  	$para_title = 'Create a User';
		$form_to_display = '
		<table id="rounded-corner">
  <tr>
	<form name="form1" method="post" action="login.php">

		<td>Username: <input name="username" type="text" id="username"></td>
  </tr>
  <tr>
		<td>Password: <input name="password" type="password" id="password"></td>
  </tr>
  <tr>
		<td><input type="submit" name="Submit" value="Submit"></td>
  </tr>
	</form>

</table>
		';
	  }

else {
$para_title = 'Login';
$form_to_display = '<table id="rounded-corner">
  <tr>
	<form name="form1" method="post" action="checklogin.php">
		<td>Username: <input name="myusername" type="text" id="myusername"></td>
  </tr>
  <tr>
		<td>Password: <input name="mypassword" type="password" id="mypassword"></td>
  </tr>
  <tr>

		<td><input type="submit" name="Submit" value="Login"></td>
  </tr>
	</form>

</table>
';
}

db_error();


session_start();




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Anubis - a cgminer web frontend</title>

<link href="templatemo_style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/ddsmoothmenu.css" />

<script type="text/javascript" src="scripts/jquery.min.js"></script>
<script type="text/javascript" src="scripts/ddsmoothmenu.js">


/***********************************************
* Smooth Navigational Menu- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

</script>


<script type="text/javascript">

ddsmoothmenu.init({
	mainmenuid: "templatemo_menu", //menu DIV id
	orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
	classname: 'ddsmoothmenu', //class added to menu's outer DIV
	//customtheme: ["#1c5a80", "#18374a"],
	contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
})

</script>

</head>
<body>
<div id="templatemo_wrapper">

<?php include ('header.inc.php'); ?>

    <div id="templatemo_main">
    	<div class="col_fw">
        	<div class="templatemo_megacontent">
            	<h2><?php echo $para_title; ?></h2>
                <div class="cleaner h20"></div>









<?php
echo "$form_to_display";

?>



                <div class="cleaner h20"></div>
<!--                 <a href="#" class="more float_r"></a> -->
            </div>

            <div class="cleaner"></div>
		</div>

        <div class="cleaner"></div>
        </div>
    </div>
    
    <div class="cleaner"></div>

<div id="templatemo_footer_wrapper">
    <div id="templatemo_footer">
        <?php include("footer.inc.php"); ?>
        <div class="cleaner"></div>
    </div>
</div> 
  
</body>
</html>