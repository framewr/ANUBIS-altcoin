<?php
error_reporting('E_ALL');
ini_set('display_errors','On'); 
//include('bitstamp.php');


// get info about the coin/exchange
function get_coin_info($dbh, $group)
{
	$infos_q = $dbh->query("SELECT * FROM `exchanges` WHERE `group` = '$group' LIMIT 1");
	db_error();
	if ($infos_q)
	  {
	  while ($infos_q_data = $infos_q->fetch(PDO::FETCH_ASSOC))
	    {
	   	  $coin_info['id'] = $infos_q_data['id'];
	   	  $coin_info['group'] = $infos_q_data['group'];
	      $coin_info['name'] = $infos_q_data['name'];
		  $coin_info['value'] = $infos_q_data['value'];
		  $coin_info['updated'] = $infos_q_data['updated'];
		  return $coin_info;
		}
	  }
	  
}


function get_print_wallets($group)
{
	global $dbh;
	$infos_q = $dbh->query("SELECT * FROM `accounts` WHERE `group` = '$group' ORDER BY id ASC");
	db_error();
	if ($infos_q)
	  {
	  while ($infos_q_data = $infos_q->fetch(PDO::FETCH_ASSOC))
	    {
		  $account_info_id = $infos_q_data['id'];
		  $account_info_group = $infos_q_data['group'];
		  $account_info_name = $infos_q_data['name'];
		  $account_info_address = $infos_q_data['address'];
		  $account_info_received = $infos_q_data['received'];
		  $account_info_sent = $infos_q_data['sent'];
		  $account_info_balance = $infos_q_data['balance'];
		  $account_info_value = $infos_q_data['value'];
		  $account_info_updated = $infos_q_data['updated'];
		  if ($group == '1')
		  $a_url = "http://www.blockchain.info/address/";
		  if ($group == '2')
		  $a_url = "http://www.dogechiain.info/address/";
		  $line = "
		  		<tr>
		  			<td>
		  				<input type='checkbox' name='del_acc[]' value = '$account_info_id'>
		  			</td>
		  			<td>
		  				$account_info_name
		  			</td>
		  			<td>
		  				<a target=\"_new\" href=\"$a_url$account_info_address\">$account_info_address</a>
		  			</td>
		  			<td>
		  				$account_info_received
		  			</td>
		  			<td>
		  				$account_info_sent
		  			</td>
		  			<td>
		  				$account_info_balance
		  			</td>
		  			<td>
		  				\$$account_info_value
		  			</td>
		  		</tr>
		  			";
			echo $line;
		}
	  }
}
		  

function get_coin_totals($group)
{
	global $dbh;
	$infos_q = $dbh->query("select SUM(received) AS totalreceived FROM accounts where `group` = '$group'");
	db_error();
	if ($infos_q)
	  {
	  while ($infos_q_data = $infos_q->fetch(PDO::FETCH_ASSOC))
	    {
	    	$total_coin_received = $infos_q_data['totalreceived'];
		}
	  }
	$infos_q = $dbh->query("select SUM(sent) AS totalsent FROM accounts where `group` = '$group'");
	db_error();
	if ($infos_q)
	  {
	  while ($infos_q_data = $infos_q->fetch(PDO::FETCH_ASSOC))
	    {
	    	$total_coin_sent = $infos_q_data['totalsent'];
		}
	  }
	$infos_q = $dbh->query("select SUM(balance) AS totalbalance FROM accounts where `group` = '$group'");
	db_error();
	if ($infos_q)
	  {
	  while ($infos_q_data = $infos_q->fetch(PDO::FETCH_ASSOC))
	    {
	    	$total_coin_balance = $infos_q_data['totalbalance'];
		}
	  }
	$infos_q = $dbh->query("select SUM(value) AS totalvalue FROM accounts where `group` = '$group'");
	db_error();
	if ($infos_q)
	  {
	  while ($infos_q_data = $infos_q->fetch(PDO::FETCH_ASSOC))
	    {
	    	$total_coin_value = $infos_q_data['totalvalue'];
		}
	  }
	  $line ="
    <th colspan='2'><div style='text-align:right'>
     Totals:</div>
    </th>
    <th>$total_coin_received</th>
    <th>$total_coin_sent</th>
    <th>$total_coin_balance</th>
    <th>\$$total_coin_value</th>
  ";
 echo $line;
}






?>
