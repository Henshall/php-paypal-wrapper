# PHP Paypal Wrapper
## Version: 1.0.0

This is a wrapper for the Paypals PHP Package 'paypal/rest-api-sdk-php'.

Working with Paypal can be an extremely frustrating experience. Their sandbox doesn't always work, their documentation is outdated or redundant, and they are very quick to ban and freeze your account if they don't like any of the transactions (ex. monetary amount too high). 

I want to create a wrapper with some basic documentation so that users can accept payments as soon as possible. The following package lets users send a payment to your Paypal account, and allows parameters to be sent along (such as user id's) so that you can easily track who has paid, so that you can upgrade their account, or send a product to them, or whatever else you need to do. 


## Installation with Composer:
```bash
composer require henshall/php-paypal-wrapper
```

## Pre-requisites
- Obtain Paypal Client ID / Secret at https://developer.paypal.com
- Place the following config file somewhere into your code, and change the settings to match your application.
```php

return [
    
    /**
    * get from https://developer.paypal.com
    */
    'client_id' => 'PAYPAL_CLIENT_ID',
    /**
    * get from https://developer.paypal.com
    */ 
    'secret' => 'PAYPAL_CLIENT_SECRET', 
    /**
    * after a user authorizes the payment, they will be directed to this url. This is where we will place the logic from step 2 below.
    */
    'return_url' => 'RETURN_URL',
    /**
    * // url the user is sent to if they cancel the transaction. Maybe you can bring the user back to your homepage with a message saying they cancelled the transaction.
    */
    'cancel_url' => 'CANCEL_URL', 
    /**
    * SDK configuration
    */
    'settings' => array(
        /**
        * Available option 'sandbox' or 'live'
        */
        'mode' => "sandbox",  // make sure to change this to live!
        /**
        * Specify the max request time in seconds
        */
        'http.ConnectionTimeOut' => 1000,
        /**
        * Whether want to log to a file
        */
        'log.LogEnabled' => false,
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
        'log.LogLevel' => 'FINE',
        
    ),
];
```

# Usage:

This package is 100% on the back-end - so that you can create your own html/css/js front-end however you like, and when a user wants to make a purchase, simply send a post request to the back-end of your application and use this package.

### Step 1.
A user click "pay" on your website, and you send a get/post request to the backend.
```html
<a href="/pay">Pay</a>
```

### Step 2.
In the pay.php file, or wherever you redirected them to, you will have the code below. This code will redirect them to paypals website where they will log in, and authorize the payment. 
```php
// Create the class
$paypalWrapper = new PaypalWrapper;
// Set the config file as shown above in the prerequisites section
$config = config('paypal_conf');
$validate = $paypalWrapper->validateConfigFile($config);
$paypalWrapper->setConfigFile($config);
// Set a Parameter - Here you can set a user_id, name, or email address to track which user has paid.
$paypalWrapper->setParam("useremail@email.com");
// Set the amount and the currency type (currency examples below)
$paypalWrapper->RedirectToPaypal(100, "USD");
if ($paypalWrapper->error) {
    // Where to put your logic if there is an error. (Save error to DB, or log file, or email to yourself etc.)
    // die($paypalWrapper->error);
}


//NOTE you can also check if any of the individual functions fail by see if they return false. 
// $setConfig = $paypalWrapper->setConfigFile($config);
// if (!$setConfig) {
//     // LOGIC IF FAILED TO SET CONFIG
// }

```


### Step 3.
Once they authorize the payment, they will be redirected back to your website according to the 'redirect_url' variable you set in the config file. They will pass along two parameters: 1) PayerId 2) paymentID  - we will need these parameters in order to execute the payment.
```php
// Create the class
$paypalWrapper = new PaypalWrapper;
// Set the config file as shown above in the prerequisites section
$config = config('paypal_conf');
$validate = $paypalWrapper->validateConfigFile($config); 
$paypalWrapper->setConfigFile($config);
// Set the payment
$paypalWrapper->setPayment($data["PayerID"], $data["paymentId"] );
// Execute the payment
$executePayment = $paypalWrapper->executePayment();

if ($paypalWrapper->error) {
    // Where to put your logic if there is an error with the wrapper itself, or its config
}

if ($executePayment == true) {
    // write code for when payment is successful
}

if ($executePayment == false) {
    // write code for when payment is NOT successful (ex. insufficient funds, or blocked for some reason)
}

```

### Currency Types Accepted:

|  Currency Type       Code |
|---------------------|-----|
| Australian dollar   | AUD |
| Brazilian real 2    | BRL |
| Canadian dollar     | CAD |
| Chinese Renmenbi 4  | CNY |
| Czech koruna        | CZK |
| Danish krone        | DKK |
| Euro                | EUR |
| Hong Kong dollar    | HKD |
| Hungarian forint 1  | HUF |
| Indian rupee 3      | INR |
| Israeli new shekel  | ILS |
| Japanese yen 1      | JPY |
| Malaysian ringgit 4 | MYR |
| Mexican peso        | MXN |


