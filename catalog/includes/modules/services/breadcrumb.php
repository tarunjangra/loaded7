<?php
/**
  $Id: breadcrunb.php v1.0 2013-01-01 datazen $

  LoadedCommerce, Innovative eCommerce Solutions
  http://www.loadedcommerce.com

  Copyright (c) 2013 Loaded Commerce, LLC

  @author     LoadedCommerce Team
  @copyright  (c) 2013 LoadedCommerce Team
  @license    http://loadedcommerce.com/license.html
*/
class lC_Services_breadcrumb {

  public function start() {
    global $lC_Breadcrumb, $lC_Language, $lC_Vqmod;

    include($lC_Vqmod->modCheck('includes/classes/breadcrumb.php'));
    $lC_Breadcrumb = new lC_Breadcrumb();

    //$lC_Breadcrumb->add($lC_Language->get('breadcrumb_top'), HTTP_SERVER);
    $template = (isset($_SESSION['template']['code'])) ? $_SESSION['template']['code'] : 'default';
    $lC_Breadcrumb->add('<span id="breadcrumbImg">' . lc_image(DIR_WS_CATALOG . 'templates/' . $template . '/images/iconHome.png'), lc_href_link(FILENAME_DEFAULT)) . '</span>';

    return true;
  }

  public function stop() {
    return true;
  }
}
?>