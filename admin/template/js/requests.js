jQuery(document).ready(function () {

  var requests = getRequests();

  jQuery(requests).each(function(){
    var today = new Date();
    var createdOn = new Date(this.request_date);

    createdOn.setHours(0,0,0,0);
    today.setHours(0,0,0,0)

    jQuery("#jango-fett").clone().removeAttr("id").attr("id","request"+this.id).appendTo(".table-body")
    //Set request details in data element
    jQuery('#request'+this.id)
      .data("id", this.id)
      .data("first_name", this.first_name)
      .data("last_name", this.last_name)
      .data("email", this.email)
      .data("type", this.type)
      .data("type_id", this.type_id)
      .data("request_date", this.request_date)
      .data("image_size", this.image_size)
      .data("nb_images", this.nb_images)
      .data("organisation", this.organisation)
      .data("profession", this.profession)
      .data("reason", this.reason)
      .data("request_status", this.request_status)
      .data("telephone", this.telephone)
      .data("user_id", this.user_id)
      .data("status_change_date", this.status_change_date)
      .data("updated_by_username", this.updated_by_username)
    ;

    //Fill request line with info
    jQuery('#request'+this.id+' td.number-col').text("#"+this.id);
    jQuery('#request'+this.id+' td.first-name-col').text(this.first_name);
    jQuery('#request'+this.id+' td.last-name-col').text(this.last_name);
    jQuery('#request'+this.id+' td.email-col').text(this.email);
    jQuery("#request"+this.id+' td.set-col').html(this.NAME);
    jQuery("#request"+this.id+' td.request-date-col').text(this.request_date);
    jQuery('#request'+this.id+' td.details-col button').attr('id','#bdrequest-details-'+this.id);
    if(activated_collection_request == false && this.type == "collection")
    {
      jQuery('#request'+this.id+' #pending_options').css('display', 'none');
      jQuery('#request'+this.id+' .status-col-pending').html('<i class="icon-attention tiptip" title="plugin User Collections is no longer active"></i>')
    }
    else{
      (this.request_status == "pending"? jQuery('#request'+this.id+' #pending_options').css('display', 'inline-block') : jQuery('#request'+this.id+' #pending_options').css('display', 'none'));
    }
    (this.request_status == "pending"? jQuery('#request'+this.id+' .status-col-pending').css('display', 'inline-block') : jQuery('#request'+this.id+' .status-col-pending').css('display', 'none'));
    (this.request_status == "accept"? jQuery('#request'+this.id+' .status-col-accepted').css('display', 'block') : jQuery('#request'+this.id+' .status-col-accepted').css('display', 'none'));
    (this.request_status == "reject"? jQuery('#request'+this.id+' .status-col-rejected').css('display', 'block') : jQuery('#request'+this.id+' .status-col-rejected').css('display', 'none'));
  
    jQuery('.tiptip').tipTip({
      delay: 0,
      fadeIn: 200,
      fadeOut: 200
    });
  
  })

  //on click display popin
  $(".details-col button").on('click', function () {
    var requestId = jQuery(this).closest('tr').data('id');
    showDetails(requestId);
  });

  //on click accept request on request line
  jQuery(document).on('click', '#accept_request_icon',  function(){
    var requestId = jQuery(this).closest('tr').data('id');
    updateRequest(requestId, 'accept', false)
  });

  //on click reject request on request line
  jQuery(document).on('click', '#reject_request_icon',  function(){
    var requestId = jQuery(this).closest('tr').data('id');
    updateRequest(requestId, 'reject', false)
  });

  //on click accept request in popin
  $('#request_popin #accept_request').on('click', function () {
    var requestId = jQuery('#request_popin').data('id');
    updateRequest(requestId, 'accept', false)
  });

    //on click reject request in popin
  $('#request_popin #reject_request').on('click', function () {
    var requestId = jQuery('#request_popin').data('id');
    updateRequest(requestId, 'reject', false)
  });

});

//display popin and fill with request detail
function showDetails(requestId) {
  jQuery('#request_popin').data('id', requestId);
  jQuery('#request_popin #popin_details_firstname p').text(jQuery('#request'+requestId).data('first_name'));
  jQuery('#request_popin #popin_details_lastname p').text(jQuery('#request'+requestId).data('last_name'));
  jQuery('#request_popin #popin_details_email p').text(jQuery('#request'+requestId).data('email'));
  jQuery('#request_popin #popin_details_organisation p').text(jQuery('#request'+requestId).data('organisation') ?? '');
  jQuery('#request_popin #popin_details_telephone p').text(jQuery('#request'+requestId).data('telephone') ?? '');
  jQuery('#request_popin #popin_details_profession p').text(jQuery('#request'+requestId).data('profession') ?? '');
  jQuery('#request_popin #popin_details_reason p').text(jQuery('#request'+requestId).data('reason'));
  jQuery('#request_popin #popin_details_set p').text(jQuery('#request'+requestId).data('type') + ' ' + jQuery('#request'+requestId).data('type_id'));
  jQuery('#request_popin #popin_details_nbr_photos p').text(jQuery('#request'+requestId).data('nb_images'));
  jQuery('#request_popin #popin_details_request_date p').text(jQuery('#request'+requestId).data('request_date'));
  jQuery('#request_popin #popin_details_set_size p').text(jQuery('#request'+requestId).data('image_size'));

  jQuery('#request_popin #popin_details_rejected_status').hide();
  jQuery('#request_popin #popin_details_accepted_status').hide();
  jQuery('#request_popin #popin_details_rejected_by').hide();
  jQuery('#request_popin #popin_details_accepted_by').hide();
  jQuery('#request_popin #popin_details_request_pending_status').hide();
  jQuery('#request_popin #change_request_status').hide()
  
  if(activated_collection_request == false && jQuery('#request'+requestId).data('type') == "collection")
  {
    jQuery('#request_popin #popin_details_request_pending_status').show();
    jQuery('#request_popin #popin_details_request_pending_status p').html('<i class="icon-attention tiptip" title="plugin User Collections is no longer active"></i>')
    jQuery('#request_popin #change_request_status').hide()
  }
  else{
    if(jQuery('#request'+requestId).data('request_status') == 'pending'){
      jQuery('#request_popin #popin_details_request_pending_status, #change_request_status').show()
    }
  }

  if(jQuery('#request'+requestId).data('request_status') == 'reject'){
    jQuery('#request_popin #popin_details_rejected_status').show();
    jQuery('#request_popin #popin_details_rejected_status p').text(jQuery('#request'+requestId).data('status_change_date'));
    jQuery('#request_popin #popin_details_rejected_by').show();
    jQuery('#request_popin #popin_details_rejected_by p').text(
      (jQuery('#request'+requestId).data('updated_by_username') ? jQuery('#request'+requestId).data('updated_by_username') : na_trad)
    );
  }

  if(jQuery('#request'+requestId).data('request_status') == 'accept'){
    jQuery('#request_popin #popin_details_accepted_status').show();
    jQuery('#request_popin #popin_details_accepted_status p').text(jQuery('#request'+requestId).data('status_change_date'));
    jQuery('#request_popin #popin_details_accepted_by').show();
    jQuery('#request_popin #popin_details_accepted_by p').text(
      (jQuery('#request'+requestId).data('updated_by_username') ? jQuery('#request'+requestId).data('updated_by_username') : na_trad)
    );
  }

  jQuery('.tiptip').tipTip({
    delay: 0,
    fadeIn: 200,
    fadeOut: 200
  });

  jQuery('#request_popin').show();
}

function hideDetails() {
  jQuery('#request_popin').css("display", "none");
}

function getRequests() {
  var requests;

  jQuery.ajax({
    type: 'GET',
    dataType: 'json',
    async: false,
    url: 'ws.php?format=json&method=batch_download.downloadRequest.getList',
    data: { ajaxload: 'true' },
    success: function (data) {
        if (data.stat == 'ok') {
           requests = data.result;
          // console.log(data)
        }
        else {
          // console.log(data)
        }
    },
    error: function (e) {
        // console.log(e);
    }
  });

  return requests
}

function updateRequest(requestId, status, popin) {
  if(!popin ){
    jQuery('#request'+requestId+' #pending_options').toggle()
    jQuery('#request'+requestId+' .wait-for-server').toggle()
  } 
  else if(popin)
  {
    jQuery('#request_popin .wait-for-server , #request_popin #change_request_status').toggle()
  }

  jQuery.ajax({
    type: 'POST',
    url: 'ws.php?format=json&method=batch_download.downloadRequest.update',
    data: {
        id: requestId,
        status_change_date : page_infos_for_update.status_change_date,
        request_status: status,
        updated_by : page_infos_for_update.current_admin
    },

    success: function (data) {
      jQuery('#request'+requestId+' .wait-for-server, #request'+requestId+' .status-col-pending, #request'+requestId+' #pending_options ').hide();
      jQuery('#request_popin .wait-for-server').hide();
      jQuery(' #request_popin #popin_details_request_pending_status').hide();
      jQuery('#request_popin #change_request_status').hide();
      jQuery('#request'+requestId)
        .data("request_status", status)
        .data("status_change_date", page_infos_for_update.status_change_date);

      if(status == 'accept'){
        jQuery('#request'+requestId+' .status-col-accepted').show();
        jQuery('#request_popin #popin_details_accepted_status').show();
        jQuery('#request_popin #popin_details_accepted_status p').text(jQuery('#request'+requestId).data('status_change_date'));
        jQuery('#request_popin #popin_details_accepted_by').show();
        jQuery('#request_popin #popin_details_accepted_by p').text(page_infos_for_update.current_admin_name);
      }
      else if(status == 'reject')
      {
        jQuery('#request'+requestId+' .status-col-rejected').show()
        jQuery('#request_popin #popin_details_rejected_status').show(); 
        jQuery('#request_popin #popin_details_rejected_status p').text(jQuery('#request'+requestId).data('status_change_date'));
        jQuery('#request_popin #popin_details_rejected_by').show();
        jQuery('#request_popin #popin_details_rejected_by p').text(page_infos_for_update.current_admin_name);
      }
      // console.log(data);
    },
    error: function (e) {
        console.log('error');
    }
  });
  
}