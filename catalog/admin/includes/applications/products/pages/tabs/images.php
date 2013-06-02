<?php
/**
  $Id: images.php v1.0 2013-01-01 datazen $

  LoadedCommerce, Innovative eCommerce Solutions
  http://www.loadedcommerce.com

  Copyright (c) 2013 Loaded Commerce, LLC

  @author     LoadedCommerce Team
  @copyright  (c) 2013 LoadedCommerce Team
  @license    http://loadedcommerce.com/license.html
*/
global $lC_Language, $lC_Template, $pInfo; 
?>   
<div id="section_images_content" class="with-padding">
  <div class="content-panel margin-bottom enabled-panels">
  
    <div class="panel-navigation silver-gradient">
      <div class="panel-control"></div>
      <div class="scrollable custom-scroll">
        <div class="navigable">
          <ul class="files-list mini open-on-panel-content">
            <li id="images-gallery-trigger" class="with-right-arrow grey-arrow">     
              <a class="file-link selected-menu" href="javascript://" onclick="showContent('default');">
                <span class="icon file-jpg"></span>
                <b><?php echo $lC_Language->get('text_product_images'); ?></b>
              </a>            
            </li>
            <li id="additional-gallery-trigger">     
              <a class="file-link" href="javascript://" onclick="showContent('additional');">
                <span class="icon file-jpg"></span>
                <b><?php echo $lC_Language->get('text_additional_images'); ?></b>
              </a>            
            </li>            
          </ul>
        </div> 
      </div>
    </div>
    
    <div class="panel-content linen" style="height:auto">
      <div class="panel-control align-right"></div>
      <div style="height: auto; position: relative;" class="scrollable with-padding custom-scroll">
        <div class="gallery" id="images-gallery">
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td width="100%" height="100%" valign="top">
                <div class="message white-gradient margin-bottom" style="min-height:37px;">
                  <div style="float: right;">
                    <?php echo $lC_Language->get('text_product_image_drag_n_drop'); ?>
                    <!--<a href="#" id="remoteFilesLink" onclick="switchImageFilesView('remote');" style="font-weight:bolder; color:#000;"><?php echo $lC_Language->get('image_remote_upload'); ?></a> | <a href="#" id="localFilesLink" onclick="switchImageFilesView('local');" style="color:#000;"><?php echo $lC_Language->get('image_local_files'); ?></a>-->
                  </div>
                  
                  <div id="remoteFiles" style="white-space:nowrap;">
                    <span id="fileUploadField"></span>
                    <?php
                    if ( isset($pInfo) ) {
                      ?>
                      <div id="fileUploaderContainer" class="small-margin-top">
                        <noscript>
                          <p><?php echo $lC_Language->get('ms_error_javascript_not_enabled_for_upload'); ?></p>
                        </noscript>
                      </div>
                      <?php
                    } else {
                      echo lc_draw_file_field('products_image', null, 'class="file"');
                    }
                    ?>                              
                  </div>

                  <div id="localFiles" style="display: none;">
                    <p><?php echo $lC_Language->get('text_introduction_select_local_images'); ?></p>
                    <select id="localImagesSelection" name="localimages[]" size="5" multiple="multiple" style="width: 100%;"></select>
                    <div id="showProgressGetLocalImages" style="display: none; float: right; padding-right: 10px;"><?php echo lc_icon_admin('progress_ani.gif') . '&nbsp;' . $lC_Language->get('image_retrieving_local_files'); ?></div>
                    <p><?php echo realpath('../images/products/_upload'); ?></p>
                    <?php
                      if ( isset($pInfo) ) {
                        echo '<input type="button" value="Assign To Product" class="operationButton" onclick="assignLocalImages();" /><div id="showProgressAssigningLocalImages" style="display: none; padding-left: 10px;">' . lc_icon_admin('progress_ani.gif') . '&nbsp;' . $lC_Language->get('image_multiple_upload_progress') . '</div>';
                      }
                    ?>
                  </div>
                  
                </div>
                <?php
                  if ( isset($pInfo) ) {
                    ?>
                    <div id="defaultImagesContainer">
                      <div id="defaultImages" style="overflow: auto;" class="small-margin-top"></div>
                    </div>
                    <div id="additionalImagesContainer" style="display:none;">
                      <div class="message white-gradient"><span class="anthracite"><strong><?php echo $lC_Language->get('subsection_original_images'); ?></strong></span></div>
                      <div id="additionalOriginal" style="overflow: auto;" class="small-margin-top"></div>
                      <div class="message white-gradient"><span class="anthracite"><strong><?php echo $lC_Language->get('subsection_images'); ?></strong></span></div>
                      <div id="additionalOther" style="overflow: auto;"></div>                    
                    </div>                    
                  <?php
                  }
                ?>
              </td>
            </tr>
          </table>
        </div>  
      </div>
    </div>
  </div>
</div> 
<script>
$(document).ready(function() {
  createUploader();
  getImages();
  //getLocalImages();  
  $('#images-gallery-trigger').addClass('with-right-arrow grey-arrow');
});

function createUploader(){
  var uploader = new qq.FileUploader({
      element: document.getElementById('fileUploaderContainer'),
      action: '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '=' . $pInfo->getInt('products_id') . '&action=fileUpload&default=DEFAULT'); ?>',
      onComplete: function(id, fileName, responseJSON){
        getImages();
      },
  });
}

// added for the product images content panel switching
function showContent(tab) { 
  $('.qq-upload-list').empty();
  if (tab == 'default') {
    $('#defaultImagesContainer').show();
    $('#additionalImagesContainer').hide();
    $('#images-gallery-trigger').addClass('with-right-arrow grey-arrow');
    $('#images-gallery-trigger > a').addClass('selected-menu');
    $('#additional-gallery-trigger').removeClass('with-right-arrow grey-arrow');
    $('#additional-gallery-trigger > a').removeClass('selected-menu');    
  } else {
    $('#defaultImagesContainer').hide();
    $('#additionalImagesContainer').show();
    $('#images-gallery-trigger').removeClass('with-right-arrow grey-arrow');
    $('#images-gallery-trigger > a').removeClass('selected-menu');
    $('#additional-gallery-trigger').addClass('with-right-arrow grey-arrow');  
    $('#additional-gallery-trigger > a').addClass('selected-menu');  
  }
}   

function showImages(data) {
  for ( i=0; i < data.entries.length; i++ ) {
    var entry = data.entries[i];
    var style = 'width: <?php echo $lC_Image->getWidth('mini') + 20; ?>px; margin: 10px; padding: 10px; float: left; text-align: center; border-radius: 5px;';
    if ( entry[1] == '1' ) { // original (products_images_groups_id)
      style += ' background-color: #535252;';
      var onmouseover = 'this.style.backgroundColor=\'#656565\';';
      var onmouseout = 'this.style.backgroundColor=\'#535252\';';      
    } else {
      var onmouseover = 'this.style.backgroundColor=\'#656565\';';
      var onmouseout = 'this.style.backgroundColor=\'\';';
    }

    if ( entry[6] == '1' ) { // default_flag         
      var newdiv = '<span id="image_' + entry[0] + '" style="' + style + '" onmouseover="' + onmouseover + '" onmouseout="' + onmouseout + '">';
      newdiv += '<img class="framed" src="<?php echo '../images/products/mini/'; ?>' + entry[2] + '" border="0" height="<?php echo $lC_Image->getHeight('mini'); ?>" alt="' + entry[2] + '" title="' + entry[5] + ' bytes" style="max-width: <?php echo $lC_Image->getWidth('mini') + 20; ?>px;" /><br />' + entry[3];
      var prevdiv = '<img src="<?php echo '../images/products/large/'; ?>' + entry[2] + '" border="0" style="max-width:100%;" />';
      if ( entry[1] == '1' ) {    
        newdiv += '<div class="show-on-parent-hover" style="position:relative;"><span class="button-group compact children-tooltip" style="position:absolute; top:-42px; left:11px;"><a href="javascript://" class="button icon-play orange-gradient" title="<?php echo $lC_Language->get('icon_preview'); ?>" onclick="showImage(\'' + entry[4] + '\', \'' + entry[7] + '\', \'' + entry[8] + '\');"></a><a href="#" class="button icon-cross red-gradient" onclick="removeImage(\'image_' + entry[0] + '\');" title="<?php echo $lC_Language->get('icon_delete'); ?>"></a></span></div>';
      } else {
        newdiv += '<div class="show-on-parent-hover" style="position:relative;"><span class="button-group compact children-tooltip" style="position:absolute; top:-42px; left:23px;"><a href="javascript://" class="button icon-play orange-gradient" title="<?php echo $lC_Language->get('icon_preview'); ?>" onclick="showImage(\'' + entry[4] + '\', \'' + entry[7] + '\', \'' + entry[8] + '\');"></a></div>';
      }
      newdiv += '</span>';

      $('#defaultImages').append(newdiv);
      
    } else {
      if ( entry[1] == '1' ) {  // original images
        var onmouseover = 'this.style.backgroundColor=\'#656565\'; this.style.backgroundImage=\'url(<?php echo lc_href_link_admin('templates/' . $lC_Template->getCode() . '/img/icons/16/drag.png'); ?>)\'; this.style.backgroundRepeat=\'no-repeat\'; this.style.zIndex=\'300000 !important\'; this.style.backgroundPosition=\'8px 2px\';';
        var newdiv2 = '<span id="image_' + entry[0] + '" style="' + style + '" onmouseover="' + onmouseover + '" onmouseout="' + onmouseout + '">';
        newdiv2 += '<img class="framed" src="<?php echo DIR_WS_HTTP_CATALOG . 'images/products/mini/'; ?>' + entry[2] + '" border="0" height="<?php echo $lC_Image->getHeight('mini'); ?>" alt="' + entry[2] + '" title="' + entry[5] + ' bytes" style="max-width: <?php echo $lC_Image->getWidth('mini') + 20; ?>px;" /><br />' + entry[3];
        newdiv2 += '<div class="show-on-parent-hover" style="position:relative; width:125%;"><span class="button-group compact children-tooltip" style="position:absolute; top:-40px; left:0;"><a href="javascript://" class="button icon-play orange-gradient" title="<?php echo $lC_Language->get('icon_preview'); ?>" onclick="showImage(\'' + entry[4] + '\', \'' + entry[7] + '\', \'' + entry[8] + '\');"></a><a href="#" class="button icon-marker blue-gradient" onclick="setDefaultImage(\'image_' + entry[0] + '\');" title="<?php echo $lC_Language->get('icon_make_default'); ?>"></a><a href="#" class="button icon-cross red-gradient" onclick="removeImage(\'image_' + entry[0] + '\');" title="<?php echo $lC_Language->get('icon_delete'); ?>"></a></span></div>';
        
        newdiv2 += '</span>';  
        $('#additionalOriginal').append(newdiv2);      
      } else {
        var newdiv2 = '<span id="image_' + entry[0] + '" style="' + style + (( entry[1] == "1" ) ? " clear:both;" : "") + '" onmouseover="' + onmouseover + '" onmouseout="' + onmouseout + '">';
        newdiv2 += '<a href="' + entry[4] + '" target="_blank"><img class="framed" src="<?php echo DIR_WS_HTTP_CATALOG . 'images/products/mini/'; ?>' + entry[2] + '" border="0" height="<?php echo $lC_Image->getHeight('mini'); ?>" alt="' + entry[2] + '" title="' + entry[5] + ' bytes" style="max-width: <?php echo $lC_Image->getWidth('mini') + 20; ?>px;" /></a><br />' + entry[3];
        newdiv2 += '<div class="show-on-parent-hover" style="position:relative;"><span class="button-group compact children-tooltip" style="position:absolute; top:-42px; left:23px;"><a href="javascript://" class="button icon-play orange-gradient" title="<?php echo $lC_Language->get('icon_preview'); ?>" onclick="showImage(\'' + entry[4] + '\', \'' + entry[7] + '\', \'' + entry[8] + '\');"></a></div>';
        

        newdiv2 += '</span>';  
        $('#additionalOther').append(newdiv2);
        
      }
    }      
  }
  
  $('#imagePreviewContainer').html(prevdiv);

  $('#additionalOriginal').sortable({
    update: function(event, ui) {
      $.getJSON('<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '=' . $pInfo->getInt('products_id') . '&action=reorderImages'); ?>' + '&' + $(this).sortable('serialize'),
        function (data) {
          getImagesOriginals();
          getImagesOthers();
        }
      );
    }
  });

  if ( $('#showProgressOriginal').css('display') != 'none') {
    $('#showProgressOriginal').css('display', 'none');
  }

  if ( $('#showProgressOther').css('display') != 'none') {
    $('#showProgressOther').css('display', 'none');
  }
}

function getImages() {
  $('#defaultImages').empty();
  getImagesOriginals(false);
  getImagesOthers(false);

  $.getJSON('<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '=' . $pInfo->getInt('products_id') . '&action=getImages'); ?>',
    function (data) {
      showImages(data);
    }
  );
}

function getImagesOriginals(makeCall) {
  $('#additionalOriginal').empty();
  $('#imagePreviewContainer').empty();
  $('#defaultImages').html('<div id="showProgressOriginal" style="float: left; padding-left: 10px;"><span class="loader on-dark small-margin-right"></span><?php echo $lC_Language->get('image_loading_from_server'); ?></div>');
  $('#imagePreviewContainer').html('<p id="showProgressOriginal" align="center" class="large-margin-top"><span class="loader huge refreshing"></span></p>');

  if ( makeCall != false ) {
    $.getJSON('<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '=' . $pInfo->getInt('products_id') . '&action=getImages&filter=originals'); ?>',
      function (data) {
        showImages(data);
      }
    );
  }
}

function getImagesOthers(makeCall) {
  $('#additionalOther').empty();
  $('#defaultOther').html('<div id="showProgressOther" style="float: left; padding-left: 10px;"><span class="loader on-dark small-margin-right"></span><?php echo $lC_Language->get('image_loading_from_server'); ?></div>');

  if ( makeCall != false ) {
    $.getJSON('<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '=' . $pInfo->getInt('products_id') . '&action=getImages&filter=others'); ?>',
      function (data) {
        showImages(data);
      }
    );
  }
}
                           
function removeImage(id) {
  $.modal.confirm('<?php echo $lC_Language->get('text_confirm_delete'); ?>', function() {
    var image = id.split('_');
    $.getJSON('<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '=' . $pInfo->getInt('products_id') . '&action=deleteProductImage'); ?>' + '&image=' + image[1],
      function (data) {
        getImages();
      }
    );        
  }, function() {
  });
}
             
function setDefaultImage(id) {  
  $.modal.confirm('<?php echo $lC_Language->get('text_confirm_set_default'); ?>', function() {
    var image = id.split('_');
    $.getJSON('<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '=' . $pInfo->getInt('products_id') . '&action=setDefaultImage'); ?>' + '&image=' + image[1],
      function (data) {
        getImages();  
        showContent('default');
      }
    ); 
  }, function() {
  })  
}

function getLocalImages() {
  $('#showProgressGetLocalImages').css('display', 'inline');

  $.getJSON('<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '&action=getLocalImages'); ?>',
    function (data) {
      var i = 0;
      var selectList = document.getElementById('localImagesSelection');

      for ( i=selectList.options.length; i>=0; i-- ) {
        selectList.options[i] = null;
      }

      for ( i=0; i<data.entries.length; i++ ) {
        selectList.options[i] = new Option(data.entries[i]);
        selectList.options[i].selected = false;
      }

      $('#showProgressGetLocalImages').css('display', 'none');
    }
  );
}

<?php
if ( isset($pInfo) ) {
  ?>
  function assignLocalImages() {
    $('#showProgressAssigningLocalImages').css('display', 'inline');

    var selectedFiles = '';

    $('#localImagesSelection :selected').each(function(i, selected) {
      selectedFiles += 'files[]=' + $(selected).text() + '&';
    });

    $.getJSON('<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '=' . $pInfo->getInt('products_id') . '&action=assignLocalImages'); ?>' + '&' + selectedFiles,
      function (data) {
        $('#showProgressAssigningLocalImages').css('display', 'none');
        getLocalImages();
        getImages();
      }
    );
  }
  <?php
}
?>

function switchImageFilesView(layer) {
  /*
  if (layer == 'local') {
    var layer1 = document.getElementById('remoteFiles');
    var layer1link = document.getElementById('remoteFilesLink');
    var layer2 = document.getElementById('localFiles');
    var layer2link = document.getElementById('localFilesLink');
  } else {
    var layer1 = document.getElementById('localFiles');
    var layer1link = document.getElementById('localFilesLink');
    var layer2 = document.getElementById('remoteFiles');
    var layer2link = document.getElementById('remoteFilesLink');
  }

  if ( (layer != 'local') || ((layer == 'local') && (layer1.style.display != 'none')) ) {
    layer1.style.display='none';
    layer2.style.display='inline';
    layer1link.style.fontWeight='normal';
    layer2link.style.fontWeight='bolder';
  } else {
    getLocalImages();
  }
  */
}
</script>   