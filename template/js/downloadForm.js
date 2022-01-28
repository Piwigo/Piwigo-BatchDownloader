
jQuery(document).ready(function () {

  jQuery('#batch_dwn_request_size').append('');

  jQuery('#batchDownloadRequest').click(() => requestPopin())

  function requestPopin(animation = true) {
    $.confirm({
        ...jconfirmConfigBdwn,
        boxWidth: '760px',
        title: str_request_form,
        content: bd_request_form,
        icon: 'uc-icon-share',
        buttons: {
            formSubmit: {
                text: str_request,
                btnClass: 'btn-uc send-request',
                action: function () {
                  $('#request_form').submit(
                    sendRequest(page_infos_for_request)
                  );
                }  
            },
            cancel: {
                text: str_cancel,
            },
        },
  
    })
  }

  function sendRequest(pageInfos) {
      jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'ws.php?format=json&method=pwg.downloadRequest.create',
        data: {
            type: pageInfos.type,
            type_id :pageInfos.type_id,
            user_id:pageInfos.user_id,
            first_name:jQuery('#firstname').val(),
            last_name:jQuery('#lastname').val(),
            email:jQuery('#email').val(),
            telephone:jQuery('#telephone').val(),
            organisation:jQuery('#organisation').val(),
            profession:jQuery('#profession').val(),
            reason:jQuery('#reason').val(),
            image_size:jQuery('#batch_dwn_request_size').val(),
            nb_images:pageInfos.nb_images,
            request_date:pageInfos.request_date
        },
        success: function (data) {
            if (data.stat == 'ok') {
                console.log(data)
                jQuery.alert({
                  theme: 'modern',
                  useBootstrap: false,
                  title: str_download_request,
                  content: str_download_request_sent
                });
                
            }
            else {
                console.log("this error")
                console.log(data);
                jQuery.alert(str_download_request_error);
            }
        },
        error: function (e) {
            console.log("Another error");
            console.log(e);
            jQuery.alert(str_download_request_error);
        }
    });
  }

});