<?php 
return [
'client_id' => 'PAYPAL_CLIENT_ID',
'secret' => 'PAYPAL_CLIENT_SECRET',
/**
* SDK configuration
*/
'settings' => array(
/**
* Available option 'sandbox' or 'live'
*/
'mode' => "PAYPAL_CLIENT_MODE",
/**
* Specify the max request time in seconds
*/
'http.ConnectionTimeOut' => 1000,
/**
* Whether want to log to a file
*/
'log.LogEnabled' => true,
/**
* Specify the file that want to write on
*/
'log.FileName' => '/logs/paypal.log',
/**
* Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
*
* Logging is most verbose in the 'FINE' level and decreases as you
* proceed towards ERROR
*/
'log.LogLevel' => 'FINE'
),
];
?>