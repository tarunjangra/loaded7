<?php
/*
  $Id: sendEmail.php v1.0 2013-01-01 datazen $

  LoadedCommerce, Innovative eCommerce Solutions
  http://www.loadedcommerce.com

  Copyright (c) 2013 Loaded Commerce, LLC

  @author     LoadedCommerce Team
  @copyright  (c) 2013 LoadedCommerce Team
  @license    http://loadedcommerce.com/license.html
*/
?>
<script>
function sendEmail(id) {
  var accessLevel = '<?php echo $_SESSION['admin']['access'][$lC_Template->getModule()]; ?>';
  if (parseInt(accessLevel) < 3) {
    $.modal.alert('<?php echo $lC_Language->get('ms_error_no_access');?>');
    return false;
  }
  var jsonLink = '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '&action=getSendData&nid=NID'); ?>'  
  $.getJSON(jsonLink.replace('NID', parseInt(id)),
    function (data) {
      if (data.rpcStatus == -10) { // no session
        var url = "<?php echo lc_href_link_admin(FILENAME_DEFAULT, 'login'); ?>";
        $(location).attr('href',url);
      }
      if (data.rpcStatus != 1) {
        $.modal.alert('<?php echo $lC_Language->get('ms_error_retrieving_data'); ?>');
        return false;
      }
      $.modal({
          content: '<div id="sendEmailContent">'+
                   '   <p id="sendEmailContentMessage"></p>'+
                   ' </div>',
          title: '<?php echo $lC_Language->get('heading_title'); ?>',
          width: 600,
          scrolling: false,
          actions: {
            'Close' : {
              color: 'red',
              click: function(win) { win.closeModal(); }
            }
          },
          buttons: {
            '<?php echo $lC_Language->get('button_close'); ?>': {
              classes:  'glossy',
              click:    function(win) { win.closeModal(); }
            },
            '<?php echo $lC_Language->get('button_continue'); ?>': {
              classes:  'blue-gradient glossy',
              click:    function(win) {
                var nvp = $("#customers").serialize();
                if (nvp.length == 0) {
                  $.modal.alert('<?php echo $lC_Language->get('error_blank_recipient'); ?>');
                  return false;
                } else { 
                  $('.modal').closeModal();
                  var jsonLink = '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '&action=getSendData&nid=NID&NVP'); ?>'  
                  $.getJSON(jsonLink.replace('NID', parseInt(id)).replace('NVP', nvp),     
                    function (rdata) {
                     if (rdata.rpcStatus == -10) { // no session
                        var url = "<?php echo lc_href_link_admin(FILENAME_DEFAULT, 'login'); ?>";     
                        $(location).attr('href',url);
                      }
                      $.modal({
                          content: '<div id="sendEmailAudienceContent">'+
                                   '   <p id="sendEmailAudienceContentMessage"></p>'+
                                   ' </div>',
                          title: '<?php echo $lC_Language->get('heading_title'); ?>',
                          width: 600,
                          scrolling: false,
                          actions: {
                            'Close' : {
                              color: 'red',
                              click: function(win) { win.closeModal(); }
                            }
                          },
                          buttons: {
                            '<?php echo $lC_Language->get('button_close'); ?>': {
                              classes:  'glossy',
                              click:    function(win) { win.closeModal(); }
                            },
                            '<?php echo $lC_Language->get('button_send'); ?>': {
                              classes:  'blue-gradient glossy',
                              click:    function(win) { 
                                $.modal({
                                  content:'<p align="center"><?php echo lc_image('images/ani_send_email.gif'); ?></p><p align="center"><?php echo '<b>' . $lC_Language->get('sending_please_wait') . '</b>'; ?></p>',
                                  buttons: {}
                                });
                                var jsonLink = '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '&action=getSendData&nid=NID&send=1&NVP'); ?>'  
                                $.getJSON(jsonLink.replace('NID', parseInt(id)).replace('NVP', nvp),    
                                  function (edata) {
                                    if (edata.rpcStatus == -10) { // no session
                                      var url = "<?php echo lc_href_link_admin(FILENAME_DEFAULT, 'login'); ?>";     
                                      $(location).attr('href',url);
                                    } 
                                    $('.modal').closeModal(); 
                                    $.modal.alert('<p align="center"><b><?php echo $lC_Language->get('sending_finalized'); ?></b></p>');  
                                    oTable.fnReloadAjax();
                                  }
                                );                
                                win.closeModal(); 
                              }
                            }            
                          },
                          buttonsLowPadding: true
                      });                  
                      $("#sendEmailAudienceContentMessage").html(rdata.showConfirmation);                  
                    }
                  );
                }
                win.closeModal(); 
              }
            }            
          },
          buttonsLowPadding: true
      });
      $("#sendEmailContentMessage").html(data.showAudienceSelectionForm);          
    }
  );
}
</script>