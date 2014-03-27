<?php

require("config.inc.php");
require("acc.inc.php");
// set up db connection
$dbh = anubis_db_connect();

$result = $dbh->query($show_tables);
db_error();

// check 'em
while ($row = $result->fetch(PDO::FETCH_NUM))
{
  if ($row[0] == "accounts")
    $gotaccountstbl = 1;
  if ($row[0] == "accgroups")
    $gotgroupstbl = 1;
}

// create db if initial run.
if (!isset($gotaccountstbl))
  create_accounts_table();

if (!isset($gotgroupstbl))
  create_accgroups_table();

db_error();

// set some values for block chains
// BTC
$blockchain_url = 'http://www.blockchain.info/';
$blockchain_addr_options = '?format=json&limit=0&filter=5';
$blockchain_addr_path = 'address/';
// DOGE <--why the fuck do we need a whole request for each value...  ugh.
$blockchain_url_doge = 'http://www.dogechain.info/';
$blockchain_addr_options_doge = '';
$blockchain_addr_path_balance = 'chain/Dogecoin/q/addressbalance/';
$blockchain_addr_path_received = 'chain/Dogecoin/q/getreceivedbyaddress/';
$blockchain_addr_path_sent = 'chain/Dogecoin/q/getsentbyaddress/';
$blockchain_addr_path_doge = 'address/';






// update main BTC value
$grp_result = $dbh->query("SELECT * FROM accgroups WHERE name = 'BTC' ORDER BY name ASC");
  db_error();

if ($grp_result)
{

$opts = array(
    'http' => array(
    'method'=>"GET",
    'user_agent'=> 'hashcash',
    'header'=>"Accept-language: en\r\n",
    'timeout' => 5
  )
);
  GLOBAL $opts;
  $bitstamp_url = 'https://bitstamp.net/api/';
  $bitstamp_exchange_path = 'ticker/';
  $url = $bitstamp_url . $bitstamp_exchange_path;
  $context  = stream_context_create($opts);
  $url_data = file_get_contents($url,false,$context);
  $bitstamp_arr = json_decode($url_data, true);

  $exchange_rate = $bitstamp_arr['last'];
  $BTC_exchange_rate = round($exchange_rate,2);
  GLOBAL $BTC_exchange_rate;
      $updq = "UPDATE exchanges SET value = '$BTC_exchange_rate', updated =now() WHERE name = 'BitStamp'";
      $updr = $dbh->exec($updq);
      db_error();

}

// update main DOGE value
$grp_result = $dbh->query("SELECT * FROM accgroups WHERE name = 'DOGE' ORDER BY name ASC");
  db_error();

if ($grp_result)
{
  $anxpro_url = 'https://anxpro.com/api/2/';
  $anxpro_exchange_path = 'dogeusd/money/ticker/';
  $url = $anxpro_url . $anxpro_exchange_path;
  $context  = stream_context_create($opts);
  $url_data = file_get_contents($url,false,$context);
  $anxpro_arr = json_decode($url_data, true);

  $DOGE_exchange_rate = $anxpro_arr['data']['last']['value'];
  GLOBAL $DOGE_exchange_rate;
      $updq = "UPDATE exchanges SET value = '$DOGE_exchange_rate', updated =now() WHERE name = 'ANXPRO'";
      $updr = $dbh->exec($updq);
      db_error();

}

// seems i forgot to implement this function....
function wallet_update($address, $received, $sent, $balance, $value)
{
	$updq = "UPDATE accounts SET received = '$received', sent = '$sent', balance = '$balance' WHERE address = '$address'";
	$updr = $dbh->exec($updq);
	db_error();
}


// get wallet data in order to poll blockchains

$account_result = $dbh->query("SELECT * FROM accounts");
  db_error();

if ($account_result)
{
	while ($account_data = $account_result->fetch(PDO::FETCH_ASSOC))
	{
		$id = $account_data['id'];
		$group = $account_data['group'];
		$name = $account_data['name'];
		$address = $account_data['address'];
		
		// see if it's a BTC wallet and react
		if ($group == '1')
		{

			GLOBAL $opts;
			$url = $blockchain_url . $blockchain_addr_path . $address . $blockchain_addr_options;
			$context  = stream_context_create($opts);
			$url_data = file_get_contents($url,false,$context);
			$acc_arr = json_decode($url_data, true);
			$btc_received = $acc_arr['total_received'];
			$btc_sent = $acc_arr['total_sent'];
			$btc_balance = $acc_arr['final_balance'];
			$btc_received /= 100000000;
			$btc_sent /= 100000000;
			$btc_balance /= 100000000;
			$btc_info = get_coin_info($dbh, $group = '1');
			$btc_value = round($btc_info['value'] * $btc_balance, 2);
			$updq = "UPDATE accounts SET received = '$btc_received', sent = '$btc_sent', balance = '$btc_balance', value = '$btc_value', updated = now() WHERE address = '$address'";
			$updr = $dbh->exec($updq);
			db_error();
		}
		// see if it's a DOGE wallet and react
		if ($group == '2')
		{
			GLOBAL $opts;
			$url = $blockchain_url_doge . $blockchain_addr_path_received . $address;
			$context  = stream_context_create($opts);
			$doge_received = file_get_contents($url,false,$context);
			$url = $blockchain_url_doge . $blockchain_addr_path_sent . $address;
			$context  = stream_context_create($opts);
			$doge_sent = file_get_contents($url,false,$context);
			$url = $blockchain_url_doge . $blockchain_addr_path_balance . $address;
			$context  = stream_context_create($opts);
			$doge_balance= file_get_contents($url,false,$context);
			$doge_info = get_coin_info($dbh, $group = '2');
			$doge_value = $doge_info['value'] * $doge_balance;
			$updq = "UPDATE accounts SET received = '$doge_received', sent = '$doge_sent', balance = '$doge_balance', value = '$doge_value', updated = now() WHERE address = '$address'";
			$updr = $dbh->exec($updq);
			db_error();
		}
	}
}







/*
	while ($group_data = $grp_result->fetch(PDO::FETCH_ASSOC))
	{
        $group_id = $group_data['id'];

        //echo create_group_header($group_data);
        
        
        
        $acc_result = $dbh->query("SELECT * FROM accounts WHERE `group` = '".$group_id."' ORDER BY name ASC");
        db_error();        
        if ($acc_result)
        {
          while ($acc_data = $acc_result->fetch(PDO::FETCH_ASSOC))
          {
            echo get_acc_summary($acc_data, $group_data);
          }
        }
        
        echo create_group_totals();
	    echo "</table>";
	    echo "</form>";
    }
*/

?>