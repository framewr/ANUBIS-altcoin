<?php


if (is_logged_in())
{
	$log_page = "logout.php";
	$log_desc = "Logout";
}
else 	{
		$log_page = "login.php";
		$log_desc = "Login";
		}

$pages = array("Home" => "index.php",
             "Accounts" => "accounts.php",
             "Configuration" => "config.php",
             "FAQ" => "faq.php",
             "Contact/Donate" => "contact.php",
			 "$log_desc" => "$log_page");

$page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
?>
<div id="templatemo_header">

    <div id="site_title"><h1><a href="index.php">Main</a></h1></div>

    <div id="templatemo_menu" class="ddsmoothmenu">
      <ul>
<?php
      foreach ($pages as $key => $value)
      {
        if  ($value == $page)
          $selected = "class='selected'";
        else
          $selected = "";

        echo "<li><a href='".$value."' ".$selected.">".$key."</a></li>";

		
      }
?>


		
    </ul>
    <br style="clear: left" />
  </div> <!-- end of templatemo_menu -->
        
</div> <!-- end of header -->

