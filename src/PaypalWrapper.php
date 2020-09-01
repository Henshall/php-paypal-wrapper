<?php 

namespace Henshall;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
// use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
// use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;

class PaypalWrapper 
{
    public $error = null;
    private $_api_context;
    
    public function validateConfigFile($paypal_conf){
        if ($this->error) {return $this->error;} 
        try {
            if (!$paypal_conf || $paypal_conf == null) {
                throw new \Exception("ConfigFile does not exist", 1);
            }
            if (!is_array($paypal_conf)) {
                throw new \Exception("ConfigFile is not an array", 1);
            }
            if (!isset($paypal_conf["client_id"])) {
                throw new \Exception("client_id is not set in the config file", 1);
            }
            
            if (!isset($paypal_conf["secret"])) {
                throw new \Exception("secret is not set in the config file", 1);
            }
            
            if (!isset($paypal_conf["settings"])) {
                throw new \Exception("settings is not set in the config file", 1);
            }
            if (!isset($paypal_conf["settings"]["mode"]) ||  !isset($paypal_conf["settings"]["http.ConnectionTimeOut"]) 
            ||  !isset($paypal_conf["settings"]["log.LogEnabled"]) ||  !isset($paypal_conf["settings"]["log.FileName"]) 
            ||  !isset($paypal_conf["settings"]["log.LogLevel"]) ) {
                throw new \Exception("one of the necessary settings in the config file does not exist", 1);
            }
            return $paypal_conf;
        } catch (\Exception $e) {
            $this->error = "validateConfigFile method failed: " . $e;
            return $this->error;
        }
    }
    
    // Sets the config file so you can access settings such as your paypal client_secret and client_id
    public function setConfigFile($paypal_conf){
        if ($this->error) {return $this->error;}
        $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
        $this->_api_context->setConfig($paypal_conf['settings']);
    }
    
    public function payment($depositAmount)
    {
        try {
            //CREATE PAYER
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');
            $item_temp = new Item();
            $item_temp->setName("Deposit")
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($depositAmount);
            
            //CREATE ItemList
            $item_list = new ItemList();
            $item_list->setItems([$item_temp]);
            
            //CREATE Amount
            $amount = new Amount();
            $amount->setCurrency('USD')
            ->setTotal($depositAmount);
            
            //CREATE Transaction
            $transaction = new Transaction();
            $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Deposit');
            
            //CREATE RedirectUrls
            $redirect_urls = new RedirectUrls();
            $redirect_urls->setReturnUrl(route('_payment_received')) /** Specify return URL **/
            ->setCancelUrl(route('payment_received'));
            
            //CREATE Payment OBJECT
            $payment = new Payment();
            $payment->setIntent('sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
            $payment->create($this->_api_context);
            
            //GET LINKS
            foreach($payment->getLinks() as $link) {
                if($link->getRel() == 'approval_url') {
                    $redirect_url = $link->getHref();
                    break;
                }
            }
            /** add payment paymentID to session **/
            Session::put('paypal_callback', $payment->getId());
            // if(isset($redirect_url)) {
            //     /** redirect to paypal **/
            //     return Redirect::away($redirect_url);
            // }
            // \Session::put('error','Unknown error occurred');
            // return redirect(url('deposit'));
            
        } catch (\Exception $e) {
            $this->error = "Payment method failed: " . $e;
            return $this->error;
        }
    }
    
}

?>