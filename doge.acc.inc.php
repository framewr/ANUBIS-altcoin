<?php
error_reporting('E_ALL');
ini_set('display_errors','On'); 

/*
$group_totals = array('GRP_id' => 0, 'BTC_received' => 0, 'BTC_sent' => 0, 'BTC_balance' => 0);

$mtgox_currencys = array ('USD', 'GBP', 'EUR', 'AUD', 'CAD', 'CHF', 'CNY', 'DKK',
                          'HKD', 'JPY', 'NZD', 'PLN', 'RUB', 'SEK', 'SGD', 'THB');
*/
//$mtgox_url = 'https://mtgox.com/api/1/';
//$mtgox_exchange_path = '/public/ticker';
$anxpro_url = 'https://anxpro.com/api/2/';
$anxpro_exchange_path = 'dogeusd/money/ticker/';

// $url = $blockchain_url . $blockchain_addr_path . $btc_address . $blockchain_addr_options;

$blockchain_url_doge = 'http://www.dogechain.info/';
$blockchain_addr_options_doge = '';
$blockchain_addr_path_balance = 'chain/Dogecoin/q/addressbalance/';
$blockchain_addr_path_received = 'chain/Dogecoin/q/getreceivedbyaddress/';
$blockchain_addr_path_sent = 'chain/Dogecoin/q/getsentbyaddress/';
$blockchain_addr_path_doge = 'address/';




$exchange_rate_doge = 0;
$currency_code_doge = 'USD';

$opts = array(
  'http' => array(
    'method'=>"GET",
    'user_agent'=> 'hashcash',
    'header'=>"Accept-language: en\r\n",
    'timeout' => 3
  )
);





function create_group_header_doge($group_data)
{
  global $group_totals;
  global $anxpro_url;
  global $anxpro_exchange_path;
  global $exchange_rate_doge;
  global $currency_code_doge;
  global $opts;

  /* reset BTC counters */
  $group_totals['GRP_id'] = $group_data['id'];
  $group_totals['BTC_received'] = 0;
  $group_totals['BTC_sent'] = 0;
  $group_totals['BTC_balance'] = 0;

  /* get exchange rate data from mtgox */
  $currency_code = $group_data['currency'];
  $url = $anxpro_url . $anxpro_exchange_path;
//  $url = $mtgox_url . $mtgox_exchange_path;




  $context  = stream_context_create($opts);
    
  $url_data = file_get_contents($url,false,$context);

  $mtgox_arr = json_decode($url_data, true);

  $exchange_rate_doge = $mtgox_arr['data']['last']['value'];
  //$exchange_rate = $mtgox_arr['last'];




  $line =
  "<tr>
    <th colspan='7'>".
      $group_data['name']
    ."</th>
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
      ".$group_data['currency']." \$".$exchange_rate_doge."
    </th>
  </tr>";
  
  return $line;
}


function get_acc_summary_doge($acc_data)
{
  global $group_totals;
  global $blockchain_url_doge;
  global $blockchain_addr_path_doge;
  global $blockchain_addr_options_doge;
  global $blockchain_addr_path_balance ;
  global $blockchain_addr_path_received;
  global $blockchain_addr_path_sent;
  global $blockchain_addr_path_doge;


  global $exchange_rate_doge;
  global $opts;

  /* get data of address from dogechain.info */

    $btc_address = $acc_data['address'];
// get received first
  $url = $blockchain_url_doge . $blockchain_addr_path_received . $btc_address;
  $context  = stream_context_create($opts);
  $url_data_received = file_get_contents($url,false,$context);
//  $acc_arr = json_decode($url_data, true);
// get sent
  $url = $blockchain_url_doge . $blockchain_addr_path_sent . $btc_address;
  $context  = stream_context_create($opts);
  $url_data_sent = file_get_contents($url,false,$context);
//  $acc_arr = json_decode($url_data, true);
// get balance
  $url = $blockchain_url_doge . $blockchain_addr_path_balance . $btc_address;
  $context  = stream_context_create($opts);
  $url_data_balance= file_get_contents($url,false,$context);
//  $acc_arr = json_decode($url_data, true);



  
  $btc_received = $url_data_received;
  $btc_sent = $url_data_sent;
  $btc_balance = $url_data_balance;

  $group_totals['BTC_received'] += $btc_received;
  $group_totals['BTC_sent'] += $btc_sent;
  $group_totals['BTC_balance'] += $btc_balance;

  //$btc_received /= 100000000;
  //$btc_sent /= 100000000;
  //$btc_balance /= 100000000;

  $exchanged_balance = $btc_balance * $exchange_rate_doge;

//echo $exchanged_balance; die();

  $line =
  "<tr>
    <td>
      <input type='checkbox' name='del_acc[]' value='".$acc_data['id']."'>
    </td>
    <td>".
      $acc_data['name']
    ."</td>
    <td><a target=\"_new\" href='".$blockchain_url_doge.$blockchain_addr_path_doge.$btc_address."'>".
      $btc_address
    ."</a></td>
    <td>".
      $btc_received
    ."</td>
    <td>".
      $btc_sent
    ."</td>
    <td>".
      $btc_balance
    ."</td>
    <td>$".
      round($exchanged_balance, 2)
    ."</td>
  </tr>";

  return $line;
}

function create_group_totals_doge()
{
  global $group_totals;
  global $exchange_rate_doge;

  $btc_received = $group_totals['BTC_received'];
  $btc_sent = $group_totals['BTC_sent'];
  $btc_balance = $group_totals['BTC_balance'];
  $exchanged_balance = $btc_balance * $exchange_rate_doge;

  $line =
  "<tr>
    <th>
      <input type='checkbox' name='deletegrp' value='".$group_totals['GRP_id']."'>
    </th>
    <th colspan='2'><div style='text-align:right'>
     Totals:</div>
    </th>
    <th>".
      $btc_received
    ."</th>
    <th>".
      $btc_sent
    ."</th>
    <th>".
      $btc_balance
    ."</th>
    <th>$".
      round($exchanged_balance, 2)
    ."</th>
  </tr>
  <tr>
    <th colspan='7'>
      Name: <input type='text' name='name'>&nbsp;
      Address: <input type='text' name='address'>&nbsp;
      <input type='submit' value='Add Account' name='addacc'>
      <input type='hidden' name='groupid' value='".$group_totals['GRP_id']."'>
      &nbsp; &nbsp;
      <input type='submit' value='Delete selected' name='delete'>
    </th>
  </tr>
  ";

  return $line;
}







?>