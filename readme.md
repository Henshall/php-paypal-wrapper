# Stripe Php Wrapper
## Version: 1.0.0

This is a wrapper for the Paypals PHP Package 'paypal/rest-api-sdk-php'.

Working with Paypal can be an extremely frustrating experience. Their sandbox doesn't always work, their documentation is outdated or redundant, and they are very quick to ban and freeze your account if they don't like any of the transactions (ex. monetary amount too high). 

I want to create a wrapper with some basic documentation so that users can accept payments as soon as possible. The following package lets users send a payment to your paypal account, and allows parameters to be sent along (such as user id's) so that you can easily track who has paid, so that you can upgrade their account, or send a product to them, or whatever else you need to do. 


## Installation with Composer:
```bash
composer require henshall/php-paypal-wrapper
```

## Pre-requisites
- Obtain Paypal Client ID / Secret. 
- Copy the following config file into your project:
```php

return [
    'client_id' => env("PAYPAL_CLIENT_ID"),
    'secret' => env("PAYPAL_SECRET"),
    'return_url' => 'http://localhost:8000/payment_received',
    'cancel_url' => 'http://localhost:8000/payment_received',
    /**
    * SDK configuration
    */
    'settings' => array(
        /**
        * Available option 'sandbox' or 'live'
        */
        'mode' => "sandbox",
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
// CREATE THE CLASS
$paypalWrapper = new PaypalWrapper;
// SET THE CONFIG FILE AS SHOWN ABOVE IN THE Pre-requisites SECTION
$config = config('paypal_conf');
$validate = $paypalWrapper->validateConfigFile($config);
$paypalWrapper->setConfigFile($config);
// SET A PARAM - HERE YOU CAN SET A USER ID, NAME, OR EMAIL TO KNOW WHO HAS PAID
$paypalWrapper->setParam("useremail@email.com");
// SET THE AMOUNT YOU WANT THEM TO PAY
$paypalWrapper->RedirectToPaypal(100);
if ($paypalWrapper->error) {
    // Where to put your logic if there is an error. (Save error to DB, or log file, or email to yourself etc.)
    // die($paypalWrapper->error);
}
```


### Step 3.
Once they authorize the payment, they will be redirected back to your website according to the 'redirect_url' variable you set in the config file. We can then execute the code.
```php
// CREATE THE CLASS
$paypalWrapper = new PaypalWrapper;
// SET THE CONFIG FILE AS SHOWN ABOVE IN THE Pre-requisites SECTION
$config = config('paypal_conf');
$validate = $paypalWrapper->validateConfigFile($config);
$paypalWrapper->setConfigFile($config);
// SET THE PAYMENT
$paypalWrapper->setPayment($data["PayerID"], $data["paymentId"] );
// EXECUTE THE PAYMENT
$executePayment = $paypalWrapper->executePayment();

if ($executePayment == true) {
    // LOGIC FOR WHEN PAYMENT IS SUCCESSFUL
}
if ($executePayment == false) {
    // LOGIC FOR WHEN PAYMENT IS NOT SUCCESSFUL
}
//PUT ERRORS BELOW
if ($paypalWrapper->error) {
    // Where to put your logic if there is an error. (Save error to DB, or log file, or email to yourself etc.)
    // die($paypalWrapper->error);
}
```

