jQuery(document).ready(function () {
    //on click display popin
    $(".set_request_info_popin").on('click', function () {
      var requestId = jQuery(this).data('id');
      console.log(requestId);
      showDetails(requestId);
    });

    
//display popin and fill with request detail
function showDetails(requestId) {
  var request_info = getOneRequest(requestId)
  console.log(jQuery(request_info))

  jQuery('#request_info_popin').data('id', request_info[0]);
  jQuery('#request_info_popin #popin_details_firstname p').text(request_info[4]);
  jQuery('#request_info_popin #popin_details_lastname p').text(request_info[5]);
  jQuery('#request_info_popin #popin_details_email p').text(request_info[7]);
  jQuery('#request_info_popin #popin_details_organisation p').text(request_info[6]);
  jQuery('#request_info_popin #popin_details_telephone p').text(request_info[8]);
  jQuery('#request_info_popin #popin_details_profession p').text(request_info[9]);
  jQuery('#request_info_popin #popin_details_reason p').text(request_info[10]);
  jQuery('#request_info_popin #popin_details_set p').text(request_info[1]+request_info[2]);
  jQuery('#request_info_popin #popin_details_nbr_photos p').text(request_info[11]);
  jQuery('#request_info_popin #popin_details_request_date p').text(request_info[12]);
  jQuery('#request_info_popin #popin_details_set_size p').text(request_info[13]);

  jQuery('#request_info_popin').show();
}

function getOneRequest(requestId) {
  var request;

  jQuery.ajax({
    type: 'GET',
    dataType: 'json',
    async: false,
    url: 'ws.php?format=json&method=batch_download.downloadRequest.getInfo',
    data: {
      id: requestId,
      ajaxload: 'true',
    },
    success: function (data) {
        if (data.stat == 'ok') {
           request = data.result;
        }
    },
    error: function (e) {
    }
  });

  return request
}

});

function hideInfoPopin() {
  jQuery('#request_info_popin').css("display", "none");
}
