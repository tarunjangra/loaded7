<?php
/**  
*  $Id: index.php v1.0 2013-01-01 datazen $
*
*  LoadedCommerce, Innovative eCommerce Solutions
*  http://www.loadedcommerce.com
*
*  Copyright (c) 2013 Loaded Commerce, LLC
*
*  @author     Loaded Commerce Team
*  @copyright  (c) 2013 Loaded Commerce Team
*  @license    http://loadedcommerce.com/license.html
*/
require('includes/application_top.php');

ini_set('log_errors', true);
ini_set('error_log', DIR_FS_WORK . 'ipn_errors.log');

// instantiate the IpnListener class
require($lC_Vqmod->modCheck('includes/modules/payment/paypal/ipnlistener.php'));
$listener = new IpnListener();

// testing your IPN in sandbox/live mode.
$listener->use_sandbox = MODULE_PAYMENT_PAYPAL_TEST_MODE;

try {
    $listener->requirePostMethod();
    $verified = $listener->processIpn();
} catch (Exception $e) {
    error_log($e->getMessage());
    exit(0);
}


/*
The processIpn() method returned true if the IPN was "VERIFIED" and false if it
was "INVALID".
*/
if ($verified) {
    /*
    Once you have a verified IPN you need to do a few more checks on the POST
    fields--typically against data you stored in your database during when the
    end user made a purchase (such as in the "success" page on a web payments
    standard button). The fields PayPal recommends checking are:
    
      1. Check the $_POST['payment_status'] is "Completed"
	    2. Check that $_POST['txn_id'] has not been previously processed 
	    3. Check that $_POST['receiver_email'] is your Primary PayPal email 
	    4. Check that $_POST['payment_amount'] and $_POST['payment_currency'] 
	       are correct
    
    Since implementations on this varies, I will leave these checks out of this
    example and just send an email using the getTextReport() method to get all
    of the details about the IPN.  
    */
    mail(STORE_OWNER_EMAIL_ADDRESS, 'Verified IPN', $listener->getTextReport());

} else {
    /*
    An Invalid IPN *may* be caused by a fraudulent transaction attempt. It's
    a good idea to have a developer or sys admin manually investigate any 
    invalid IPN.
    */
    mail(STORE_OWNER_EMAIL_ADDRESS, 'Invalid IPN', $listener->getTextReport());
}

?>
