<?php
require("config.inc.php");
require("acc.inc.php");
//require("doge.acc.inc.php");

$dbh = anubis_db_connect();

session_start();
if ( !is_logged_in() )
	{
		header('Location: login.php');
	}


if (isset($_POST['addgroup']))
{
  $grp_name = $dbh->quote(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
  $grp_curr = $dbh->quote(filter_input(INPUT_POST, 'currency', FILTER_SANITIZE_STRING));

  $updq = "INSERT INTO accgroups (name, currency) VALUES ($grp_name, $grp_curr)";
  $updr = $dbh->exec($updq);
  db_error();
}

if (isset($_POST['addacc']))
{
    $acc_name = $dbh->quote(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $acc_addr = $dbh->quote(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING));

    $updq = "INSERT INTO accounts (name, address, `group`) VALUES (".$acc_name.", ".$acc_addr.", ".$_POST['groupid'].");";
    $updr = $dbh->exec($updq);
    db_error();
}

if (isset($_POST['delete']))
{
    foreach ($_POST['del_acc'] as $acc_id)
    {
      $updq = "DELETE FROM accounts WHERE id = ".$acc_id.";";
      $updr = $dbh->exec($updq);
      db_error();
    }
    
    if(isset($_POST['deletegrp']))
    {
      $updq = "DELETE FROM accounts WHERE `group` = ".$_POST['deletegrp'].";";
      $updr = $dbh->exec($updq);
      db_error();
      
      $updq = "DELETE FROM accgroups WHERE id = ".$_POST['deletegrp'].";";
      $updr = $dbh->exec($updq);
      db_error();    
    }
}

// get some vars set for displaying later on...
$btc_info = get_coin_info($dbh, $group = '1');
$btc_exchange = $btc_info['name'];
$btc_value = $btc_info['value'];
$btc_updated = $btc_info['updated'];

$doge_info = get_coin_info($dbh, $group = '2');
$doge_exchange = $doge_info['name'];
$doge_value = $doge_info['value'];
$doge_updated = $doge_info['updated'];

$pot_info = get_coin_info($dbh, $group = '3');
$pot_exchange = $pot_info['name'];
$pot_value = $pot_info['value'];
$pot_updated = $pot_info['updated'];

$vtc_info = get_coin_info($dbh, $group = '4');
$vtc_exchange = $vtc_info['name'];
$vtc_value = $vtc_info['value'];
$vtc_updated = $vtc_info['updated'];


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
            	<h2>Accounts</h2>
                <div class="cleaner h20"></div>


<form name=add action='accounts.php' method='post'>
	<table id='rounded-corner' summary='GroupSummary'>
		<tr>
			<th colspan='7'>฿TC <em>as of <? echo $btc_updated; ?></em></th>
		</tr>
		<tr>
			<th>
				&nbsp;
			</th>
			<th>
				Account Name
			</th>
			<th>
				Account Address
			</th>
			<th>
				Received
			</th>
			<th>
				Sent
			</th>
			<th>
				Balance
			</th>
			<th>
				1฿ = USD $<? echo $btc_value; ?>
			</th>
		</tr>
			<? get_print_wallets($group = '1'); ?>
		<tr>
			<th>
				<input type='checkbox' name='deletegrp' value='1'>
			</th>
		
			<? get_coin_totals($group = '1'); ?>
		</tr>
		<tr>
    		<th colspan='7'>
      			Name: <input type='text' name='name'>&nbsp;
      			Address: <input type='text' name='address'>&nbsp;
      			<input type='submit' value='Add Account' name='addacc'>
      			<input type='hidden' name='groupid' value='1'>
      			&nbsp; &nbsp;
      			<input type='submit' value='Delete selected' name='delete'>
    		</th>
  		</tr>
  </table>
</form>
<!-- do the DOGE -->
<form name=add action='accounts.php' method='post'>
	<table id='rounded-corner' summary='GroupSummary'>
		<tr>
			<th colspan='7'>ÐOGE <em>as of <? echo $doge_updated; ?></em></th>
		</tr>
		<tr>
			<th>
				&nbsp;
			</th>
			<th>
				Account Name
			</th>
			<th>
				Account Address
			</th>
			<th>
				Received
			</th>
			<th>
				Sent
			</th>
			<th>
				Balance
			</th>
			<th>
				1Ð = USD $<? echo $doge_value; ?>
			</th>
		</tr>
			<? get_print_wallets($group = '2'); ?>
		<tr>
			<th>
				<input type='checkbox' name='deletegrp' value='1'>
			</th>
		
			<? get_coin_totals($group = '2'); ?>
		</tr>
		<tr>
    		<th colspan='7'>
      			Name: <input type='text' name='name'>&nbsp;
      			Address: <input type='text' name='address'>&nbsp;
      			<input type='submit' value='Add Account' name='addacc'>
      			<input type='hidden' name='groupid' value='2'>
      			&nbsp; &nbsp;
      			<input type='submit' value='Delete selected' name='delete'>
    		</th>
  		</tr>
  </table>
</form>
<!-- do the POT -->
<form name=add action='accounts.php' method='post'>
	<table id='rounded-corner' summary='GroupSummary'>
		<tr>
			<th colspan='7'>POT <em>as of <? echo $pot_updated; ?></em></th>
		</tr>
		<tr>
			<th>
				&nbsp;
			</th>
			<th>
				Account Name
			</th>
			<th>
				Account Address
			</th>
			<th>
				Received
			</th>
			<th>
				Sent
			</th>
			<th>
				Balance
			</th>
			<th>
				1POT = ฿TC <? echo $pot_value; ?> 
			</th>
		</tr>
			<? get_print_wallets($group = '3'); ?>
		<tr>
			<th>
				<input type='checkbox' name='deletegrp' value='1'>
			</th>
		
			<? get_coin_totals($group = '3'); ?>
		</tr>
		<tr>
    		<th colspan='7'>
      			Name: <input type='text' name='name'>&nbsp;
      			Address: <input type='text' name='address'>&nbsp;
      			<input type='submit' value='Add Account' name='addacc'>
      			<input type='hidden' name='groupid' value='3'>
      			&nbsp; &nbsp;
      			<input type='submit' value='Delete selected' name='delete'>
    		</th>
  		</tr>
  </table>
</form>
<!-- do the VTC -->
<form name=add action='accounts.php' method='post'>
	<table id='rounded-corner' summary='GroupSummary'>
		<tr>
			<th colspan='7'>VTC <em>as of <? echo $vtc_updated; ?></em></th>
		</tr>
		<tr>
			<th>
				&nbsp;
			</th>
			<th>
				Account Name
			</th>
			<th>
				Account Address
			</th>
			<th>
				Received
			</th>
			<th>
				Sent
			</th>
			<th>
				Balance
			</th>
			<th>
				1VTC = $<? echo $vtc_value; ?> 
			</th>
		</tr>
			<? get_print_wallets($group = '4'); ?>
		<tr>
			<th>
				<input type='checkbox' name='deletegrp' value='1'>
			</th>
		
			<? get_coin_totals($group = '4'); ?>
		</tr>
		<tr>
    		<th colspan='7'>
      			Name: <input type='text' name='name'>&nbsp;
      			Address: <input type='text' name='address'>&nbsp;
      			<input type='submit' value='Add Account' name='addacc'>
      			<input type='hidden' name='groupid' value='4'>
      			&nbsp; &nbsp;
      			<input type='submit' value='Delete selected' name='delete'>
    		</th>
  		</tr>
  </table>
</form>
<form name=save action="accounts.php" method="post">
<table id="savetable" align=center>
    <thead>
    	<tr>
        	<th scope="col" class="rounded-company">Name</th>
            <th scope="col" class="rounded-company">Currency</th>
            <th>&nbsp;</th>
        </tr>
        <tr>
          <td align=center><input type="text" name="name" value=""></td>
          <td align=center><select name="currency"><option>USD</option><option>GBP</option><option>EUR</option><option>AUD</option><option>CAD</option><option>CHF</option><option>CNY</option><option>DKK</option><option>HKD</option><option>JPY</option><option>NZD</option><option>PLN</option><option>RUB</option><option>SEK</option><option>SGD</option><option>THB</option><select></td>
          <td colspan=2 align=center><input type="submit" value="Add new group" name="addgroup"></td>
        </tr>
    </thead>
</table>

</form>
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