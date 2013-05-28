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
      //$this->_paypal_standard_params();  // sandbox url
    } else {
      //$this->form_action_url = 'https://www.paypal.com/cgi-bin/webscr';  // production url
      $this->form_action_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';  // sandbox url
      $this->_paypal_standard_params();  // production url
    }
    //$this->form_action_url = lc_href_link(FILENAME_CHECKOUT, 'payment_template', 'SSL', true, true, true) ; 

    //$this->cc_explain_url = lc_href_link(FILENAME_PAYPAL_INFO, '', 'SSL');


    
  
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

     print("gggg<xmp>");
     print_r($lC_ShoppingCart->getProducts());
     print("</xmp>");

         //if(MODULE_PAYMENT_PAYPAL_METHOD == 'Itemized') { 
         if(1) { 
      $discount_amount_cart = 0;
      $shoppingcart_products = $lC_ShoppingCart->getProducts();

      $paypal_standard_action_params = array(
        'upload' => sizeof($shoppingcart_products),
        'redirect_cmd' => '_cart',
        'handling_cart' => $shippingTotal,
        'discount_amount_cart' => $discount_amount_cart
        ); 

       for ($i=1; $i<=sizeof($shoppingcart_products); $i++) {
          $paypal_shoppingcart_products_params[] = array(
            'item_name_'.$i => $shoppingcart_products[$i]['name'],
            'item_number_'.$i => $shoppingcart_products[$i]['item_id'],
            'quantity_'.$i => $shoppingcart_products[$i]['quantity'],
            'amount_'.$i => $shoppingcart_products[$i]['price'],
            'tax_'.$i => $shoppingcart_products[$i]['tax_class_id']            
            ); 
      }

print("paypal_standard_action_params  : ".sizeof($shoppingcart_products));
print("paypal_standard_action_params  : <xmp>");
print_r($paypal_shoppingcart_products_params);
print("</xmp>");

/*

    //Itemized Order Details
        for ($i=0; $i<sizeof($order->products); $i++) {
          $index = $i+1;

           $paypal_standard_action_params = array(
            'item_name_'.$index => sizeof($lC_ShoppingCart->getProducts()),
            'item_number_'.$index => '_cart',
            'quantity_'.$index => $shippingTotal,
            'amount_'.$index => $discount_amount_cart,
            'tax_'.$index => $discount_amount_cart            
            ); 


 return tep_draw_hidden_field('on'.$sub_index.'_'.$index,$option).
        tep_draw_hidden_field('os'.$sub_index.'_'.$index,$value);

          //Customer Specified Product Options: PayPal Max = 2
          if ($order->products[$i]['attributes']) {
            for ($j=0, $n=sizeof($order->products[$i]['attributes']); $j<2; $j++) {
              if($order->products[$i]['attributes'][$j]['option']){
                $paypal_fields .= $this->optionSetFields($j,$index,$order->products[$i]['attributes'][$j]['option'],$order->products[$i]['attributes'][$j]['value']);
              } else {
                $paypal_fields .= $this->optionSetFields($j,$index);
              }
            }
          } else {
            for ($j=0; $j<2; $j++) {
              $paypal_fields .= $this->optionSetFields($j,$index);
            }
          }
------------
          
        }


*/

    }



   /* 
    
  
    if(MODULE_PAYMENT_PAYPAL_METHOD == 'Itemized') { 
      $discount_amount_cart = 0;
      $paypal_standard_action_params = array(
        'upload' => sizeof($lC_ShoppingCart->getProducts()),
        'redirect_cmd' => '_cart',
        'handling_cart' => $shippingTotal,
        'discount_amount_cart' => $discount_amount_cart
        ); 




    //Itemized Order Details
        for ($i=0; $i<sizeof($order->products); $i++) {
          $index = $i+1;

           $paypal_standard_action_params = array(
            'item_name_'.$index => sizeof($lC_ShoppingCart->getProducts()),
            'item_number_'.$index => '_cart',
            'quantity_'.$index => $shippingTotal,
            'amount_'.$index => $discount_amount_cart,
            'tax_'.$index => $discount_amount_cart            
            ); 

----------
 return tep_draw_hidden_field('on'.$sub_index.'_'.$index,$option).
        tep_draw_hidden_field('os'.$sub_index.'_'.$index,$value);

          //Customer Specified Product Options: PayPal Max = 2
          if ($order->products[$i]['attributes']) {
            for ($j=0, $n=sizeof($order->products[$i]['attributes']); $j<2; $j++) {
              if($order->products[$i]['attributes'][$j]['option']){
                $paypal_fields .= $this->optionSetFields($j,$index,$order->products[$i]['attributes'][$j]['option'],$order->products[$i]['attributes'][$j]['value']);
              } else {
                $paypal_fields .= $this->optionSetFields($j,$index);
              }
            }
          } else {
            for ($j=0; $j<2; $j++) {
              $paypal_fields .= $this->optionSetFields($j,$index);
            }
          }
------------
          
        }




    } else {
      $amount = $lC_Currencies->formatRaw($lC_ShoppingCart->getTotal(), $lC_Currencies->getCode());
      $paypal_standard_action_params = array(
        'item_name' => STORE_NAME,
        'redirect_cmd' => '_xclick',
        'shipping' => $shippingTotal,
        'amount' => $amount
        ); 

        -----------$item_number = '';
        for ($i=0; $i<sizeof($order->products); $i++) {
          $item_number .= ' '.$order->products[$i]['name'].' ,';
        }
        $item_number = substr_replace($item_number,'',-2);
        $paypal_fields .= tep_draw_hidden_field('item_number', $item_number);------------


    }
    $return_href_link = lc_href_link(FILENAME_CHECKOUT_SUCCESS, 'success&order_id='. $this->_order_id , 'SSL', false);
    $cancel_href_link = lc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', false);
    $notify_href_link = lc_href_link(FILENAME_IPN, '', 'SSL', false);
    $signature = (tep_not_null($txn_sign)) ? $txn_sign : $this->digest;

    $paypal_standard_action_params = array(
        'cmd' => '_ext-enter', 
        'business' => MODULE_PAYMENT_PAYPAL_BUSINESS_ID,       
        'currency_code' => $_SESSION['currency'],
        'return' => $return_href_link,
        'cancel_return' => $cancel_href_link,
        'notify_url' => $notify_href_link,
        'no_shipping' => $no_shipping,        
        'shipping' => $shipping,
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
        'no_note' => MODULE_PAYMENT_PAYPAL_NO_NOTE,    
        'form' => 'mage');   
                                  
    $response = transport::getResponse(array('url' => $paypal_standard_action_url, 'method' => 'post', 'parameters' => $paypal_standard_action_params));   

    $params = substr($response, strpos($response, 'uID='));         
  
                                  
                                  die('317');
    return $params;
    */
  }  



/*
    //Returns the gross total amount to compare with paypal.mc_gross
    public function grossPaymentAmount($my_currency) {
      global $order, $currencies;
      return number_format(($order->info['total']) * $currencies->get_value($my_currency), $currencies->get_decimal_places($my_currency));
    }

    public function amount($my_currency) {
      global $order, $currencies;
      return number_format(($order->info['total'] - $order->info['shipping_cost']) * $currencies->get_value($my_currency), $currencies->get_decimal_places($my_currency));
    }
*/
  

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

/*
  public function setTransactionID() {
      global $order, $currencies;
      $my_currency = $_SESSION['currency'];
      $trans_id = STORE_NAME . date('Ymdhis');
      $this->digest = md5($trans_id . number_format($order->info['total'] * $currencies->get_value($my_currency), $currencies->get_decimal_places($my_currency), '.', '') . MODULE_PAYMENT_PAYPAL_IPN_DIGEST_KEY);
      return $this->digest;
    }

 */




}
?>