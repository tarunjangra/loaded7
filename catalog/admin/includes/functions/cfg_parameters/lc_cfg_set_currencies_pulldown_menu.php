<?php
/*
  $Id: lc_cfg_set_zones_pulldown_menu.php v1.0 2013-01-01 datazen $

  LoadedCommerce, Innovative eCommerce Solutions
  http://www.loadedcommerce.com

  Copyright (c) 2013 Loaded Commerce, LLC

  @author     LoadedCommerce Team
  @copyright  (c) 2013 LoadedCommerce Team
  @license    http://loadedcommerce.com/license.html
*/
  function lc_cfg_set_currencies_pulldown_menu($default, $key = null) {
    global $lC_Database;
    
    $css_class = 'class="input with-small-padding"';
    $args = func_get_args();
    if(count($args) > 2 &&  strpos($args[0], 'class') !== false ) {
      $css_class = $args[0];
      $default = $args[1];
      $key  = $args[2];
    }

    if (isset($_GET['plugins'])) {
      $name = (empty($key)) ? 'plugins_value' : 'plugins[' . $key . ']';
    } else {
      $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';
    }
    
    $Qcurrencies = $lC_Database->query('select * from :table_currencies');
    $Qcurrencies->bindTable(':table_currencies', TABLE_CURRENCIES);
    $Qcurrencies->execute();

    while ($Qcurrencies->next()) {
      $currencies_array[] = array('id' => $Qcurrencies->valueInt('currencies_id'),
                                'text' => $Qcurrencies->value('title'));
    }

    return lc_draw_pull_down_menu($name, $currencies_array, $default, $css_class);
  }
?>