
jQuery(document).ready(function () {

  jQuery('#batch_dwn_request_size').append('');

  jQuery('#batchDownloadRequest').click(() => requestPopin())
  jQuery('#batchDownloadAnotherRequest').click(() => requestPopin())

  function requestPopin(animation = true) {
    $.confirm({
        ...jconfirmConfigBdwn,
        backgroundDismiss: false,
        boxWidth: '760px',
        title: str_request_form,
        content: bd_request_form,
        icon: 'uc-icon-share',

        buttons: {
            formSubmit: {
                text: str_request,
                btnClass: 'btn-uc send-request',
                action: function () {
                   $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'ws.php?format=json&method=batch_download.downloadRequest.create',
                    data: {
                        type: page_infos_for_request.type,
                        type_id :page_infos_for_request.type_id,
                        user_id:page_infos_for_request.user_id,
                        first_name:jQuery('#firstname').val(),
                        last_name:jQuery('#lastname').val(),
                        email:jQuery('#email').val(),
                        telephone:jQuery('#telephone').val(),
                        organisation:jQuery('#organisation').val(),
                        profession:jQuery('#profession').val(),
                        reason:jQuery('#reason').val(),
                        image_size:jQuery('#batch_dwn_request_size').val(),
                        nb_images:page_infos_for_request.nb_images,
                        request_date:page_infos_for_request.request_date,
                    },
                    success: function (data) {
                      $("#request_form .error-block").css('display','none');
                        if (data.stat == 'ok' && data.result == null) {
                            jQuery.alert({
                              theme: 'modern',
                              useBootstrap: false,
                              title: str_download_request,
                              content: str_download_request_sent
                            });
                          $(".jconfirm.jconfirm-modern").remove();
                        }
                        else 
                        {
                          if(data.message.startsWith("Missing parameters: ")){
                            var missing_params = data.message.split(": ");
                            missing_params = missing_params[1];
                            missing_params = missing_params.split(",")
                            // console.log(missing_params);
                            if(missing_params.includes("first_name")){
                              $("#request_form .error-block.firstname").css('display','block');
                              $("#request_form .error-block.firstname").html("<p>"+str_download_request_error_firstname+"</p>");    
                            }
                            if(missing_params.includes("last_name")){
                              $("#request_form .error-block.lastname").css('display','block');
                              $("#request_form .error-block.lastname").html("<p>"+str_download_request_error_lastname+"</p>");  
                            }
                            if(missing_params.includes("organisation")){
                              $("#request_form .error-block.organisation").css('display','block');
                              $("#request_form .error-block.organisation").html("<p>"+str_download_request_error_organisation+"</p>");  
                            }
                            if(missing_params.includes("email")){
                              $("#request_form .error-block.email").css('display','block');
                              $("#request_form .error-block.email").html("<p>"+str_download_request_error_email+"</p>");  
                            }
                            if(missing_params.includes("telephone")){
                              $("#request_form .error-block.telephone").css('display','block');
                              $("#request_form .error-block.telephone").html("<p>"+str_download_request_error_telephone+"</p>");  
                            }
                            if(missing_params.includes("profession")){
                              $("#request_form .error-block.profession").css('display','block');
                              $("#request_form .error-block.profession").html("<p>"+str_download_request_error_profession+"</p>");  
                            }
                            if(missing_params.includes("reason")){
                              $("#request_form .error-block.reason").css('display','block');
                              $("#request_form .error-block.reason").html("<p>"+str_download_request_error_reason+"</p>");  
                            }
                          }
                            
                          if(data.message == "Email isn't the right format"){
                            $("#request_form .error-block.email").css('display','block');
                            $("#request_form .error-block.email").html("<p>"+str_download_request_error_email_format+"</p>");  
                          }
                          return false;
                        }
                    },
                    error: function (e) {
                        jQuery.alert(str_download_request_error);
                        return false;
                    }
                  });
                  return false;
                }  
            },
            cancel: {
                text: str_cancel,
            },
        },
        
  
    })
  }

});