<?php 

// require 'PaypalWrapper.php';
require('PaypalWrapper.php');
require('vendor/autoload.php');
$config = require('paypal_conf.php');
use Henshall\PaypalWrapper;

$paypal_wrapper = new PaypalWrapper;
$paypal_wrapper->validateConfigFile($config);
$paypal_wrapper->setConfigFile($config);
var_dump($paypal_wrapper);
die();







?>