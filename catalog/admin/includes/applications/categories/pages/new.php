<?php
/*
  $Id: new.php v1.0 2013-01-01 datazen $

  LoadedCommerce, Innovative eCommerce Solutions
  http://www.loadedcommerce.com

  Copyright (c) 2013 Loaded Commerce, LLC

  @author     LoadedCommerce Team
  @copyright  (c) 2013 LoadedCommerce Team
  @license    http://loadedcommerce.com/license.html
*/
  $assignedCategoryTree = new lC_CategoryTree();
  $assignedCategoryTree->setBreadcrumbUsage(false);
  $assignedCategoryTree->setSpacerString('&nbsp;', 5);

  $lC_Template->loadModal($lC_Template->getModule());
?>
<style>
.qq-upload-drop-area { min-height: 150px; top: -185px; }
.qq-upload-drop-area span { margin-top:-16px; }
</style>
<!-- Main content -->
<section role="main" id="main">
  <hgroup id="main-title" class="thin">
    <h1><?php echo (isset($lC_ObjectInfo) && isset($categories_name[$lC_Language->getID()])) ? $categories_name[$lC_Language->getID()] : $lC_Language->get('heading_title_new_category'); ?></h1>
    <?php
      if ( $lC_MessageStack->exists($lC_Template->getModule()) ) {
        echo $lC_MessageStack->get($lC_Template->getModule());
      }
    ?>
  </hgroup>
  <style>
    LABEL { font-weight:bold; }
    TD { padding: 5px 0 0 5px; }
  </style>
  <div class="with-padding-no-top">
    <form name="category" id="category" class="dataForm" action="<?php echo lc_href_link_admin(FILENAME_DEFAULT, $lC_Template->getModule() . '=&action=save'); ?>" method="post" enctype="multipart/form-data">
      <div id="category_tabs" class="side-tabs">
        <ul class="tabs">
          <li class="active"><?php echo lc_link_object('#section_general_content', $lC_Language->get('section_general')); ?></li>
          <li id="tabHeaderSectionDataContent"><?php echo lc_link_object('#section_data_content', $lC_Language->get('section_data')); ?></li>
          <li><?php echo lc_link_object('#section_categories_content', $lC_Language->get('section_categories')); ?></li>
        </ul>
        <div class="clearfix tabs-content">
          <div id="section_general_content">
            <div class="columns with-padding">
              <div class="new-row-mobile four-columns twelve-columns-mobile">
                <span class="strong margin-right"><?php echo $lC_Language->get('text_categories_image'); ?></span><?php echo lc_show_info_bubble($lC_Language->get('info_bubble_category_image'), null); ?>   
                <div style="padding-left:6px;" class="small-margin-top">
                  <div id="imagePreviewContainer" class="cat-image align-center">
                    <img src="../images/categories/no-image.png" style="max-width: 100%; height: auto;" align="center" />
                    <input type="hidden" id="categories_image" name="categories_image" value="no-image.png">
                  </div>
                </div>   
                <p class="thin mid-margin-top" align="center"><?php echo $lC_Language->get('text_drag_drop_to_replace'); ?></p>
                <center>
                  <div id="fileUploaderImageContainer" class="small-margin-top">
                    <noscript>
                      <p><?php echo $lC_Language->get('ms_error_javascript_not_enabled_for_upload'); ?></p>
                    </noscript>
                  </div>
                </center>
                <script>
                  $(document).ready(function() {
                    createUploader();
                    var qqbuttonhtmlold = $('.qq-upload-button').html();
                    var qqbuttonhtml = qqbuttonhtmlold.replace(/Upload a file/i, 'Upload');
                    $('.qq-upload-button').html(qqbuttonhtml).css('text-decoration', 'underline').css('margin', '-31px -141px 0 -49px');
                    $('.qq-upload-list').hide();
                  });
                  function createUploader() {
                    var uploader = new qq.FileUploader({
                      element: document.getElementById('fileUploaderImageContainer'),
                      action: '<?php echo lc_href_link_admin('rpc.php', 'action=fileUpload'); ?>',
                      onComplete: function(id, fileName, responseJSON) {
                        $('#imagePreviewContainer').html('<img src="<?php echo '../images/categories/'; ?>' + fileName + '" border="0" style="max-width:100%;" /><input type="hidden" id="categories_image" name="categories_image" value="' + fileName + '">');
                      },
                    });
                  }
                </script>
              </div>
              <div class="new-row-mobile eight-columns twelve-columns-mobile">
                <div id="languageTabs" class="standard-tabs">
                  <ul class="tabs">
                  <?php
                    foreach ( $lC_Language->getAll() as $l ) {
                      echo '<li>' . lc_link_object('#languageTabs_' . $l['code'], $lC_Language->showImage($l['code']) . '&nbsp;' . $l['name']) . '</li>';
                    }
                  ?>
                  </ul>
                  <div class="clearfix tabs-content with-padding">
                  <?php
                    foreach ( $lC_Language->getAll() as $l ) {
                    ?>
                    <div id="languageTabs_<?php echo $l['code']; ?>">
                      <p class="button-height block-label">
                        <label class="label" for="<?php echo 'categories_name[' . $l['id'] . ']'; ?>">
                          <?php echo $lC_Language->get('field_name'); ?>
                          <?php echo lc_show_info_bubble($lC_Language->get('info_bubble_'), null); ?>
                        </label>
                        <?php echo lc_draw_input_field('categories_name[' . $l['id'] . ']', null, 'class="required input full-width mid-margin-top"'); ?>
                      </p>
                      <p class="button-height block-label">
                        <label class="label" for="<?php echo 'categories_menu_name[' . $l['id'] . ']'; ?>">
                          <?php echo $lC_Language->get('field_menu_name'); ?>
                          <?php echo lc_show_info_bubble($lC_Language->get('info_bubble_'), null); ?>
                        </label>
                        <?php echo lc_draw_input_field('categories_menu_name[' . $l['id'] . ']', null, 'class="required input full-width mid-margin-top"'); ?>
                      </p>
                      <p class="button-height block-label">
                        <label class="label" for="<?php echo 'categories_blurb[' . $l['id'] . ']'; ?>">
                          <?php echo $lC_Language->get('field_blurb'); ?>
                          <?php echo lc_show_info_bubble($lC_Language->get('info_bubble_'), null); ?>
                        </label>
                        <?php echo lc_draw_textarea_field('categories_blurb[' . $l['id'] . ']', null, null, 1, 'class="required input full-width mid-margin-top"'); ?>
                      </p>
                      <p class="button-height block-label">
                        <label class="label" for="<?php echo 'categories_description[' . $l['id'] . ']'; ?>">
                          <?php echo $lC_Language->get('field_description'); ?>
                          <?php echo lc_show_info_bubble($lC_Language->get('info_bubble_'), null); ?>
                        </label>
                        <div style="margin-bottom:-6px;"></div>
                        <?php echo lc_draw_textarea_field('categories_description[' . $l['id'] . ']', null, null, 10, 'class="required input full-width autoexpanding clEditorCategoriesDescription"'); ?>
                        <span class="float-right small-margin-top small-margin-right"><?php echo '<a href="javascript:toggleEditor();">' . $lC_Language->get('text_toggle_html_editor') . '</a>'; ?></span>
                      </p>
                      <br />
                      <p class="button-height block-label">
                        <label class="label" for="<?php echo 'categories_tags[' . $l['id'] . ']'; ?>">
                          <?php echo $lC_Language->get('field_tags'); ?>
                          <?php echo lc_show_info_bubble($lC_Language->get('info_bubble_'), null); ?>
                        </label>
                        <?php echo lc_draw_input_field('categories_tags[' . $l['id'] . ']', null, 'class="required input full-width mid-margin-top"'); ?>
                      </p>
                    </div>
                    <div class="clear-both"></div>
                    <?php
                    }
                  ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="columns">
              <div class="twelve-columns no-margin-bottom">
                <div class="field-drop-tabs button-height black-inputs">
                  <div class="columns no-margin-bottom mid-margin-top">
                    <div class="six-columns twelve-columns-mobile margin-bottom">
                      <label class="label" for="categories_mode"><b><?php echo $lC_Language->get('text_mode'); ?></b></label>
                      <select class="select full-width" id="categories_mode" name="categories_mode" onchange="customCheck();">
                      <?php
                        // later this will become an array from the possible places to link to and expandable by dvelopers also
                        $modes_array = array(
                          array('text' => 'Category', 'value' => 'category'),
                          array('text' => 'Page', 'value' => 'page'),
                          array('text' => 'Link To: Specials', 'value' => 'specials'),
                          //array('text' => 'Link To: Featured', 'value' => 'featured'),
                          array('text' => 'Link To: New Products', 'value' => 'new'),
                          array('text' => 'Link To: Search', 'value' => 'search'),
                          array('text' => 'Link To: Shopping Cart', 'value' => 'cart'),
                          array('text' => 'Link To: My Account', 'value' => 'account'),
                          array('text' => 'Custom Link', 'value' => 'override')
                        );
                        foreach ($modes_array as $mode) {
                          echo '<option value="' . $mode['value'] . '">' . $mode['text'] . '</option>'; 
                        }
                      ?>
                      </select>
                      <p id="categories_link_target_p" class="small-margin-top" style="display:none;">
                        <input type="checkbox" class="checkbox" id="categories_link_target" name="categories_link_target"> <?php echo $lC_Language->get('text_new_window'); ?>
                      </p>
                    </div>
                    <div class="six-columns twelve-columns-mobile" id="categories_custom" style="display:none;">  
                      <span class="info-spot on-left grey mid-margin-right">
                        <span class="icon-info-round"></span>
                        <span class="info-bubble">
                          Put the bubble text here
                        </span>
                      </span>
                      <input type="text" class="input" id="categories_custom_url" name="categories_custom_url"> &nbsp;<span id="custom_url_text"><strong><?php echo $lC_Language->get('text_custom_link'); ?></strong></span>
                    </div>
                  </div>
                  <div class="columns">
                    <div class="six-columns twelve-columns-mobile">
                      <label class="label" for="parent_id"><b><?php echo $lC_Language->get('text_parent'); ?></b></label> 
                      <select class="select full-width" id="parent_id" name="parent_id">
                        <option value="0"><?php echo $lC_Language->get('text_top_category'); ?></option>
                        <?php
                          foreach ($assignedCategoryTree->getArray() as $value) {
                            $selected = ($value['id'] == $_GET['categories']) ? ' selected' : '';
                            echo '<option value="' . $value['id'] . '"' . $selected . '>' . $value['title'] . '</option>' . "\n";
                          }
                        ?>
                      </select>
                    </div>
                    <div class="six-columns twelve-columns-mobile small-margin-top">  
                      <span class="info-spot on-left grey mid-margin-right">
                        <span class="icon-info-round"></span>
                        <span class="info-bubble">
                          Put the bubble text here
                        </span>
                      </span>
                      <input type="checkbox" class="switch medium" id="categories_show_in_listings" name="categories_show_in_listings" checked> <strong><?php echo $lC_Language->get('text_show_in_listings'); ?></strong>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="columns with-padding no-margin-bottom">
              <div class="twelve-columns no-margin-top no-margin-bottom">
                <div class="columns no-margin-left">
                  <div class="three-columns twelve-columns-mobile"> 
                    <div class="margin-right">
                      <label class="label" for="categories_page_type"><b><?php echo $lC_Language->get('field_categories_page_type'); ?></b></label>
                    </div>
                  </div>
                  <div class="nine-columns twelve-columns-mobile">
                    <p class="mid-margin-bottom">
                      <input type="radio" class="radio small-margin-right" name="categories_page_type" value="html" checked disabled>
                      <?php echo $lC_Language->get('text_standard_html_page'); ?>  
                      <span class="info-spot on-left grey large-margin-left">
                        <span class="icon-info-round"></span>
                        <span class="info-bubble">
                          Put the bubble text here
                        </span>
                      </span>
                    </p>
                    <p class="mid-margin-bottom">
                      <input type="radio" class="radio small-margin-right" name="categories_page_type" value="photo" disabled>
                      <?php echo $lC_Language->get('text_photo_album'); ?>
                    </p>
                    <p class="mid-margin-bottom">
                      <input type="radio" class="radio small-margin-right" name="categories_page_type" value="faq" disabled>
                      <?php echo $lC_Language->get('text_faq'); ?>
                    </p>
                  </div>
                </div>
              </div>
              <div class="twelve-columns no-margin-top no-margin-bottom">
                <div class="columns no-margin-left">
                  <div class="three-columns twelve-columns-mobile"> 
                    <div class="margin-right">
                      <label class="label" for="categories_content_file"><b><?php echo $lC_Language->get('field_categories_content_file'); ?></b></label>
                    </div>
                  </div>
                  <div class="nine-columns twelve-columns-mobile">
                    <?php echo lc_draw_input_field('categories_content_file', null, 'id="categories_content_file" name="categories_content_file" class="input" style="min-width:250px;" placeholder="/customhtml.php" disabled'); ?>
                    <span class="info-spot on-left grey">
                      <small class="tag red-bg mid-margin-left margin-right">Pro</small>
                      <span class="info-bubble">
                        <b>Go Pro!</b> and enjoy this feature!
                      </span>
                    </span>  
                    <span class="info-spot on-left grey large-margin-left">
                      <span class="icon-info-round"></span>
                      <span class="info-bubble">
                        Put the bubble text here
                      </span>
                    </span>
                    <p class="small-margin-top"><?php echo $lC_Language->get('text_path_to_file'); ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="section_data_content" class="with-padding">
            <fieldset class="fieldset">
              <legend class="legend"><?php echo $lC_Language->get('field_management_settings'); ?></legend>
              <div class="columns no-margin-bottom">
                <div class="six-columns twelve-columns-mobile">
                  <label class="label" for="<?php echo 'categories_slug'; ?>">
                    <?php echo $lC_Language->get('field_slug'); ?>
                    <span class="info-spot on-left grey float-right small-margin-bottom">
                      <span class="icon-info-round"></span>
                      <span class="info-bubble">
                        Put the bubble text here
                      </span>
                    </span>
                  </label>
                  <?php echo lc_draw_input_field('categories_slug', null, 'class="required input full-width mid-margin-top" placeholder="category-url-slug" disabled'); ?>
                </div>
                <div class="six-columns twelve-columns-mobile">
                  <label class="label" for="<?php echo 'categories_product_class'; ?>">
                    <?php echo $lC_Language->get('field_product_class'); ?>
                    <span class="info-spot on-left grey">
                      <small class="tag red-bg mid-margin-left margin-right">Pro</small>
                      <span class="info-bubble">
                        <b>Go Pro!</b> and enjoy this feature!
                      </span>
                    </span>  
                    <span class="info-spot on-left grey float-right small-margin-bottom">
                      <span class="icon-info-round"></span>
                      <span class="info-bubble">
                        Put the bubble text here
                      </span>
                    </span>
                  </label>
                  <select class="select full-width mid-margin-top" id="categories_product_class" name="categories_product_class" disabled>
                    <option>Common</option>
                  </select>
                </div>
              </div>
            </fieldset>
            <fieldset class="fieldset">
              <legend class="legend"><?php echo $lC_Language->get('field_access_settings'); ?></legend>
              <div class="columns no-margin-bottom">
                <div class="six-columns twelve-columns-mobile">
                  <p class="margin-bottom">
                    <label class="label" for="categories_access_levels"><?php echo $lC_Language->get('field_access_levels'); ?></label>
                    <span class="info-spot on-left grey">
                      <small class="tag orange-bg mid-margin-left margin-right">Pro</small>
                      <span class="info-bubble">
                        <b>Go Pro!</b> and enjoy this feature!
                      </span>
                    </span>  
                    <span class="info-spot on-left grey large-margin-left">
                      <span class="icon-info-round"></span>
                      <span class="info-bubble">
                        Put the bubble text here
                      </span>
                    </span>  
                  </p>
                  <p class="margin-left">
                    <input type="checkbox" class="checkbox small-margin-right" disabled> <?php echo $lC_Language->get('access_levels_retail'); ?>
                  </p>
                  <p class="margin-left">
                    <input type="checkbox" class="checkbox small-margin-right" disabled> <?php echo $lC_Language->get('access_levels_wholesale'); ?>
                  </p>
                  <p class="margin-left">
                    <input type="checkbox" class="checkbox small-margin-right" disabled> <?php echo $lC_Language->get('access_levels_dealerl'); ?>
                  </p>
                </div>
                <div class="six-columns twelve-columns-mobile"></div>
              </div>
            </fieldset>
          </div>
          <div id="section_categories_content" class="with-padding"> 
            Relationships (Later Phase)
          </div>
        </div>
      </div>
      <?php
        $next_sort = lC_Categories_Admin::nextSort(); 
        echo lc_draw_hidden_field('sort_order', $next_sort); 
        echo lc_draw_hidden_field('subaction', 'confirm'); 
      ?>
    </form>
    <div class="clear-both"></div>
    <div id="floating-button-container" class="six-columns twelve-columns-tablet">
      <div id="floating-menu-div-listing">
        <div id="buttons-container" style="position: relative;" class="clear-both">
          <div style="float:right;">
            <p class="button-height" align="right">
              <a class="button" href="<?php echo lc_href_link_admin(FILENAME_DEFAULT, $lC_Template->getModule() . ($_GET['categories'] != '') ? 'categories=' . $_GET['categories'] : ''); ?>">
                <span class="button-icon red-gradient glossy">
                  <span class="icon-cross"></span>
                </span><?php echo $lC_Language->get('button_cancel'); ?>
              </a>&nbsp;
              <a class="button<?php echo (((int)$_SESSION['admin']['access'][$lC_Template->getModule()] < 3) ? ' disabled' : NULL); ?>" href="<?php echo (((int)$_SESSION['admin']['access'][$lC_Template->getModule()] < 2) ? '#' : 'javascript://" onclick="$(\'#category\').submit();'); ?>">
                <span class="button-icon green-gradient glossy">
                  <span class="icon-download"></span>
                </span><?php echo $lC_Language->get('button_save'); ?>
              </a>&nbsp;
            </p>
          </div>
          <div id="floating-button-container-title" class="hidden">
            <p class="white big-text small-margin-top"><?php echo $lC_Language->get('text_new_category'); ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>