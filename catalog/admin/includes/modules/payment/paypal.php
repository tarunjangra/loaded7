<?php
/**  
  $Id: cod.php v1.0 2013-01-01 datazen $

  Loaded Commerce, Innovative eCommerce Solutions
  http://www.loadedcommerce.com

  Copyright (c) 2013 Loaded Commerce, LLC

  @author     Loaded Commerce Team
  @copyright  (c) 2013 Loaded Commerce Team
  @license    http://loadedcommerce.com/license.html
 
*/
class lC_Payment_paypal extends lC_Payment_Admin {
 /**
  * The administrative title of the payment module
  *
  * @var string
  * @access public
  */
  public $_title;
 /**
  * The code of the payment module
  *
  * @var string
  * @access public
  */
  public $_code = 'paypal';
 /**
  * The developers name
  *
  * @var string
  * @access protected
  */
  protected $_author_name = 'Loaded Commerce';
 /**
  * The developers address
  *
  * @var string
  * @access protected
  */
  protected $_author_www = 'http://www.loadedcommerce.com';
 /**
  * The status of the module
  *
  * @var boolean
  * @access protected
  */
  protected $_status = false;
 /**
  * Constructor
  */
  public function lC_Payment_paypal() {
    global $lC_Language;

    $this->_title = $lC_Language->get('payment_paypal_title');
    $this->_description = $lC_Language->get('payment_paypal_description');
    $this->_method_title = $lC_Language->get('payment_paypal_method_title');
    $this->_status = (defined('MODULE_PAYMENT_PAYPAL_STATUS') && (MODULE_PAYMENT_PAYPAL_STATUS == '1') ? true : false);
    $this->_sort_order = (defined('MODULE_PAYMENT_PAYPAL_SORT_ORDER') ? MODULE_PAYMENT_PAYPAL_SORT_ORDER : null);
  }
 /**
  * Checks to see if the module has been installed
  *
  * @access public
  * @return boolean
  */
  public function isInstalled() {
    return (bool)defined('MODULE_PAYMENT_PAYPAL_STATUS');
  }
 /**
  * Install the module
  *
  * @access public
  * @return void
  */
  public function install() {
    global $lC_Database;

    parent::install();

    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Enable Module', 'MODULE_PAYMENT_PAYPAL_STATUS', '-1', 'Enable this module?', '6', '0', 'lc_cfg_use_get_boolean_value', 'lc_cfg_set_boolean_value(array(1, -1))', now())");

    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('E-Mail Address', 'MODULE_PAYMENT_PAYPAL_ID', '', 'The e-mail address to use for the PayPal service', '6', '1', now())");
    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Business ID', 'MODULE_PAYMENT_PAYPAL_BUSINESS_ID', '', 'Email address or account ID of the payment recipient', '6', '2', now())");

    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Default Currency', 'MODULE_PAYMENT_PAYPAL_DEFAULT_CURRENCY', 'USD', 'The <b>default</b> currency to use for when the customer chooses to checkout via the store using a currency not supported by PayPal.<br />(This currency must exist in your store)', '6', '3', 'lc_cfg_set_boolean_value(array(\'USD\',\'CAD\',\'EUR\',\'GBP\',\'JPY\',\'AUD\'))', now())");

    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Currency', 'MODULE_PAYMENT_PAYPAL_CURRENCY', 'Selected Currency', 'The currency to use for credit card transactions', '6', '4', 'lc_cfg_set_boolean_value(array(\'Selected Currency\',\'Only USD\',\'Only CAD\',\'Only EUR\',\'Only GBP\',\'Only JPY\'))', now())");

    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Default Currency', 'MODULE_PAYMENT_PAYPAL_DEFAULT_CURRENCY', '1', 'The <b>default</b> currency to use for when the customer chooses to checkout via the store using a currency not supported by PayPal.', '6', '3', '', 'lc_cfg_set_currencies_pulldown_menu(class=\"select\",', now())");
    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Transaction Currency', 'MODULE_PAYMENT_PAYPAL_CURRENCY', '1', 'The currency to use for credit card transactions', '6', '4', '', 'lc_cfg_set_currencies_pulldown_menu(class=\"select\",', now())");

    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_PAYPAL_ZONE', '0', 'If a zone is selected, enable this payment method for that zone only.', '6', '5', 'lc_cfg_use_get_zone_class_title', 'lc_cfg_set_zone_classes_pull_down_menu(class=\"select\",', now())");

   // $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Set Pending Notification Status', 'MODULE_PAYMENT_PAYPAL_PROCESSING_STATUS_ID', '1', 'Set the Pending Notification status of orders made with this payment module', '6', '6', 'lc_cfg_use_get_order_status_title', 'lc_cfg_set_order_statuses_pull_down_menu(class=\"select\",', now())");

    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_PAYPAL_ORDER_DEFAULT_STATUS_ID', '1', 'Set the status of orders made with this payment module', '6', '7', 'lc_cfg_use_get_order_status_title', 'lc_cfg_set_order_statuses_pull_down_menu(class=\"select\",', now())");

    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Set On Hold Order Status', 'MODULE_PAYMENT_PAYPAL_ORDER_ONHOLD_STATUS_ID', '1', 'Set the status of <b>On Hold</b> orders made with this payment module', '6', '8', 'lc_cfg_use_get_order_status_title', 'lc_cfg_set_order_statuses_pull_down_menu(class=\"select\",', now())");

    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Set Canceled Order Status', 'MODULE_PAYMENT_PAYPAL_ORDER_CANCELED_STATUS_ID', '1', 'Set the status of <b>Canceled</b> orders made with this payment module', '6', '8', 'lc_cfg_use_get_order_status_title', 'lc_cfg_set_order_statuses_pull_down_menu(class=\"select\",', now())");

    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Synchronize Invoice', 'MODULE_PAYMENT_PAYPAL_INVOICE_REQUIRED', 'False', 'Show the PayPal Express Checkout shortcut button on the shopping cart page?', '6', '9', 'lc_cfg_use_get_boolean_value', 'lc_cfg_set_boolean_value(array(\'True\', \'False\'))', now())");

    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display', 'MODULE_PAYMENT_PAYPAL_SORT_ORDER', '100', 'Sort order of display. Lowest is displayed first.', '6', '10' , now())");

    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Set Refunded Order Status', 'MODULE_PAYMENT_PAYPAL_ORDER_REFUNDED_STATUS_ID', '1', 'Set the status of <b>Refunded</b> orders made with this payment module', '6', '11', 'lc_cfg_use_get_order_status_title', 'lc_cfg_set_order_statuses_pull_down_menu(class=\"select\",', now())");

    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Background Color', 'MODULE_PAYMENT_PAYPAL_CS', 'White', 'Select the background color of PayPal\'s payment pages.', '6', '12', 'lc_cfg_set_boolean_value(array(\'White\', \'Black\'))', now())");
    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Processing logo', 'MODULE_PAYMENT_PAYPAL_PROCESSING_LOGO', 'loaded_header_logo.gif', The image file name to display the store\'s checkout process', '6', '13', now())");
    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Store logo', 'MODULE_PAYMENT_PAYPAL_STORE_LOGO', '',The image file name for PayPal to display (leave empty if your store does not have SSL)', '6', '14', now())");
    ///$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('PayPal Page Style Name', 'MODULE_PAYMENT_PAYPAL_PAGE_STYLE', 'default',The name of the page style you have configured in your PayPal Account', '6', '15', now())");

    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Include a note with payment', 'MODULE_PAYMENT_PAYPAL_NO_NOTE', 'No', 'Choose whether your customer should be prompted to include a note or not?', '6', '16', 'lc_cfg_set_boolean_value(array(\'Yes\', \'No\'))', now())"); 
    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Shopping Cart Method', 'MODULE_PAYMENT_PAYPAL_METHOD', 'Aggregate', 'What type of shopping cart do you want to use?', '6', '17', 'lc_cfg_set_boolean_value(array(\'Aggregate\', \'Itemized\'))', now())"); 

    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable PayPal Shipping Address', 'MODULE_PAYMENT_PAYPAL_SHIPPING_ALLOWED', 'No', 'Allow the customer to choose their own PayPal shipping address?', '6', '18', 'lc_cfg_set_boolean_value(array(\'Yes\', \'No\'))', now())"); 

    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Debug Email Notifications', 'MODULE_PAYMENT_PAYPAL_IPN_DEBUG', 'Yes', 'Enable debug email notifications', '6', '19', 'lc_cfg_set_boolean_value(array(\'Yes\', \'No\'))', now())"); 
    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Digest Key', 'MODULE_PAYMENT_PAYPAL_IPN_DIGEST_KEY', 'PayPal_Shopping_Cart_IPN', 'Key to use for the digest functionality', '6', '20', now())");

    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Test Mode', 'MODULE_PAYMENT_PAYPAL_IPN_TEST_MODE', 'Off', 'Set test mode', '6', '21', 'lc_cfg_set_boolean_value(array(\'Off\', \'On\'))', now())"); 
    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Cart Test', 'MODULE_PAYMENT_PAYPAL_IPN_CART_TEST', 'On', 'Set cart test mode to verify the transaction amounts', '6', '22', 'lc_cfg_set_boolean_value(array(\'Off\', \'On\'))', now())");
    
    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Debug Email Notification Address', 'MODULE_PAYMENT_PAYPAL_IPN_DEBUG_EMAIL', '', 'The e-mail address to send <b>debug</b> notifications to', '6', '23', now())");

    //$lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('PayPal Domain', 'MODULE_PAYMENT_PAYPAL_DOMAIN', 'www.paypal.com', 'Select which PayPal domain to use<br>(for live production select www.paypal.com)', '6', '24', 'lc_cfg_set_boolean_value(array(\'www.paypal.com\', \'www.sandbox.paypal.com\'))', now())");

     $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Sandbox Mode', 'MODULE_PAYMENT_PAYPAL_TEST_MODE', '-1', 'Set to \'Yes\' for sandbox test environment or set to \'No\' for production environment.', '6', '24', 'lc_cfg_use_get_boolean_value', 'lc_cfg_set_boolean_value(array(1, -1))', now())");
    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Return URL behavior', 'MODULE_PAYMENT_PAYPAL_RM', '1', 'How should the customer be sent back from PayPal to the specified URL?<br>0=No IPN, 1=GET, 2=POST', '6', '25', 'lc_cfg_set_boolean_value(array(\'0\',\'1\',\'2\'))', now())");    


  }
 /**
  * Return the configuration parameter keys in an array
  *
  * @access public
  * @return array
  */
  public function getKeys() {
    if (!isset($this->_keys)) {
      $this->_keys = array( 
          'MODULE_PAYMENT_PAYPAL_STATUS',
          'MODULE_PAYMENT_PAYPAL_ID',
          'MODULE_PAYMENT_PAYPAL_BUSINESS_ID',
          'MODULE_PAYMENT_PAYPAL_DEFAULT_CURRENCY',          
          'MODULE_PAYMENT_PAYPAL_ZONE',
          'MODULE_PAYMENT_PAYPAL_ORDER_DEFAULT_STATUS_ID',
          'MODULE_PAYMENT_PAYPAL_SORT_ORDER',          
          'MODULE_PAYMENT_PAYPAL_NO_NOTE',
          'MODULE_PAYMENT_PAYPAL_METHOD',
          'MODULE_PAYMENT_PAYPAL_IPN_DIGEST_KEY',
          'MODULE_PAYMENT_PAYPAL_IPN_DEBUG',
          'MODULE_PAYMENT_PAYPAL_IPN_DEBUG_EMAIL',
          'MODULE_PAYMENT_PAYPAL_TEST_MODE',
          'MODULE_PAYMENT_PAYPAL_RM'
      );
    }

    return $this->_keys;
  }
}
?>