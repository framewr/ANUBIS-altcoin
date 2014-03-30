<?php
session_start();
require("config.inc.php");

$dbh = anubis_db_connect();





// hash 'em both cause fuck injections?
$myusername= hash('sha256', $_POST['myusername']); 
$mypassword= hash('sha256', $_POST['mypassword']);




$login_q = $dbh->query("SELECT COUNT(*) FROM `users` WHERE `username_hash` = '$myusername' AND `password` = '$mypassword' LIMIT 1");

	db_error();
	if ($login_q->fetchColumn() > 0)
	  {
			$login_datas = "SELECT * FROM `users` WHERE `username_hash` = '$myusername' AND `password` = '$mypassword' LIMIT 1";
			foreach ($dbh->query($login_datas) as $row)
			{
				$_SESSION['user_id'] = $row['id'];
				$_SESSION['user_name'] = $row['username'];
				header('Location: index.php');
			}

	  }else{header('Location: login.php');}

	
	?>