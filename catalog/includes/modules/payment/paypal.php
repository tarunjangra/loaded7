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
class lC_Payment_paypal extends lC_Payment {
 /**
  * The public title of the payment module
  *
  * @var string
  * @access protected
  */  
  protected $_title;
 /**
  * The code of the payment module
  *
  * @var string
  * @access protected
  */  
  protected $_code = 'paypal';
 /**
  * The developers name
  *
  * @var string
  * @access protected
  */  
  protected $_author_name = 'Loaded Commerce'; /////
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
  * The sort order of the module
  *
  * @var integer
  * @access protected
  */  
  protected $_sort_order;   
 /**
  * The order id
  *
  * @var integer
  * @access protected
  */ 
  protected $_order_id;
 /**
  * Constructor
  */ 
  public function lC_Payment_paypal() {
    global $lC_Database, $lC_Language, $lC_ShoppingCart;
    
    $this->_title = $lC_Language->get('payment_paypal_title');
    $this->_method_title = $lC_Language->get('payment_paypal_method_title');
    $this->_status = (defined('MODULE_PAYMENT_PAYPAL_STATUS') && (MODULE_PAYMENT_PAYPAL_STATUS == '1') ? true : false);
    $this->_sort_order = (defined('MODULE_PAYMENT_PAYPAL_SORT_ORDER') ? MODULE_PAYMENT_PAYPAL_SORT_ORDER : null);    

    if (defined('MODULE_PAYMENT_PAYPAL_STATUS')) {
      $this->initialize();
     }

    
  }

  public function initialize() {
    global $lC_Database, $lC_Language, $order;

    if ((int)MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID;
    } else {
      $this->order_status = 0;
    } 

    if (is_object($order)) $this->update_status();    
    if (defined('MODULE_PAYMENT_PAYPAL_TEST_MODE') && MODULE_PAYMENT_PAYPAL_TEST_MODE == '1') {
      $this->form_action_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';  // sandbox url
    } else {
      $this->form_action_url = 'https://www.paypal.com/cgi-bin/webscr';  // production url
    }    
  
  }
 /**
  * Disable module if zone selected does not match billing zone  
  *
  * @access public
  * @return void
  */  
  public function update_status() {
    global $lC_Database, $order;

    if ( ($this->_status === true) && ((int)MODULE_PAYMENT_PAYPAL_ZONE > 0) ) {
      $check_flag = false;

      $Qcheck = $lC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
      $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
      $Qcheck->bindInt(':geo_zone_id', MODULE_PAYMENT_PAYPAL_ZONE);
      $Qcheck->bindInt(':zone_country_id', $order->billing['country']['id']);
      $Qcheck->execute();

      while ($Qcheck->next()) {
        if ($Qcheck->valueInt('zone_id') < 1) {
          $check_flag = true;
          break;
        } elseif ($Qcheck->valueInt('zone_id') == $order->billing['zone_id']) {
          $check_flag = true;
          break;
        }
      }

      if ($check_flag == false) {
        $this->_status = false;
      }
    }
  } 

 /**
  * Return the payment selections array
  *
  * @access public
  * @return array
  */ 
  public function selection() {
    global $lC_Language;

    $selection = array('id' => $this->_code,
                       'module' => '<div class="payment-selection">' . $this->_method_title . '<span></span></div>');    
    
    return $selection;
  }
  

   /**
  * Perform any pre-confirmation logic
  *
  * @access public
  * @return boolean
  */ 
  public function pre_confirmation_check() {
    return false;
  }
 /**
  * Perform any post-confirmation logic
  *
  * @access public
  * @return integer
  */ 
  public function confirmation() {
   return false;
  }

  /**
  * Return the confirmation button logic
  *
  * @access public
  * @return string
  */ 
  public function process_button() {
    echo $this->_paypal_standard_params();
    return false;
  }

   /**
  * Return the confirmation button logic
  *
  * @access public
  * @return string
  */ 
  private function _paypal_standard_params() {
    global $lC_Language, $lC_ShoppingCart, $lC_Currencies, $lC_Customer;  

    $upload         = 0;
    $no_shipping    = '1';
    $redirect_cmd   = '';
    $handling_cart  = '';
    $item_name      = '';
    $shipping       = '';

    // get the shipping amount
    $taxTotal       = 0;
    $shippingTotal  = 0;
    foreach ($lC_ShoppingCart->getOrderTotals() as $ot) {
      if ($ot['code'] == 'shipping') $shippingTotal = (float)$ot['value'];
      if ($ot['code'] == 'tax') $taxTotal = (float)$ot['value'];
    } 

    $shoppingcart_products = $lC_ShoppingCart->getProducts();
    $amount = $lC_Currencies->formatRaw($lC_ShoppingCart->getTotal(), $lC_Currencies->getCode());

    if(MODULE_PAYMENT_PAYPAL_METHOD == 'Itemized') { 
      $discount_amount_cart = 0;     

      $paypal_action_params = array(
        'upload' => sizeof($shoppingcart_products),
        'redirect_cmd' => '_cart',
        'handling_cart' => $shippingTotal,
        'discount_amount_cart' => $discount_amount_cart
        );
       for ($i=1; $i<=sizeof($shoppingcart_products); $i++) {
          $paypal_shoppingcart_params = array(
            'item_name_'.$i => $shoppingcart_products[$i]['name'],
            'item_number_'.$i => $shoppingcart_products[$i]['item_id'],
            'quantity_'.$i => $shoppingcart_products[$i]['quantity'],
            'amount_'.$i => $shoppingcart_products[$i]['price'],
            'tax_'.$i => $shoppingcart_products[$i]['tax_class_id']            
            ); 
          $paypal_action_params =  array_merge($paypal_action_params,$paypal_shoppingcart_params);
      }
    } else {
      $item_number = '';
      for ($i=1; $i<=sizeof($shoppingcart_products); $i++) {
        $item_number .= ' '.$shoppingcart_products[$i]['name'].' ,';
      }
      $item_number = substr_replace($item_number,'',-2);
      $paypal_action_params = array(
        'item_name' => STORE_NAME,
        'redirect_cmd' => '_xclick',
        'amount' => $amount,
        'shipping' => $shippingTotal,
        'item_number' => $item_number
        ); 
    }

    $return_href_link = lc_href_link(FILENAME_CHECKOUT, 'process', 'SSL');
    $cancel_href_link = lc_href_link(FILENAME_CHECKOUT, 'cart', 'SSL');
    $notify_href_link = lc_href_link(FILENAME_IPN, null, 'SSL');
    $signature = $this->setTransactionID($amount);

    $paypal_standard_params = array(
        'cmd' => '_ext-enter', 
        'business' => MODULE_PAYMENT_PAYPAL_BUSINESS_ID,       
        'currency_code' => $_SESSION['currency'],
        'return' => $return_href_link,
        'cancel_return' => $cancel_href_link,
        /*'notify_url' => $notify_href_link,*/
        'no_shipping' => $no_shipping,
        'rm' => MODULE_PAYMENT_PAYPAL_RM,
        'custom' => $signature,
        'email' => $lC_Customer->getEmailAddress(),
        'first_name' => $lC_ShoppingCart->getBillingAddress('firstname'),
        'last_name' => $lC_ShoppingCart->getBillingAddress('lastname'),
        'address1' => $lC_ShoppingCart->getBillingAddress('street_address'),
        'address2' => '',
        'city' => $lC_ShoppingCart->getBillingAddress('city'), 
        'state' => $lC_ShoppingCart->getBillingAddress('state'), 
        'zip' => $lC_ShoppingCart->getBillingAddress('postcode'),
        'lc' => $lC_ShoppingCart->getBillingAddress('country_iso_code_3'),
        'no_note' => (MODULE_PAYMENT_PAYPAL_NO_NOTE == 'Yes') ? '0': '1',    
        'form' => 'mage');   
  
    $paypal_standard_action_params =  array_merge($paypal_standard_params,$paypal_action_params); 
    $paypal_params = '';
    foreach($paypal_standard_action_params as $name => $value) {
      $paypal_params .= lc_draw_hidden_field($name, $value);
      //$paypal_params .= $name.lc_draw_input_field($name, $value).'<br>';
    }
    return $paypal_params;    
  } 
  

 /**
  * Parse the response from the processor
  *
  * @access public
  * @return string
  */ 
  public function process() {
    $this->_order_id = lC_Order::insert();
    lC_Order::process($this->_order_id, $this->order_status);
  }

  public function setTransactionID($amount) {
    global $lC_Language, $lC_ShoppingCart, $lC_Currencies, $lC_Customer;
    $my_currency = $lC_Currencies->getCode();
    $trans_id = STORE_NAME . date('Ymdhis');
    $digest = md5($trans_id . number_format($amount * $lC_Currencies->value($my_currency), $lC_Currencies->decimalPlaces($my_currency), '.', '') . MODULE_PAYMENT_PAYPAL_IPN_DIGEST_KEY);
    return $digest;
  }
}
?>