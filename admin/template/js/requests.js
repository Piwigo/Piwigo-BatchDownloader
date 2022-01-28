jQuery(document).ready(function () {

  var requests = getRequests();
  // console.log(requests);

  jQuery(requests).each(function(){
    var today = new Date();
    var createdOn = new Date(this.request_date);
    var msInDay = 24 * 60 * 60 * 1000;

    createdOn.setHours(0,0,0,0);
    today.setHours(0,0,0,0)

    var days_ago = (+today - +createdOn)/msInDay ;
    if (days_ago == 0){
      days_ago = "Today";
    }else{
      days_ago = (+today - +createdOn)/msInDay + (days_ago == 1?' day ago': ' days ago');
    }

    jQuery("#jango-fett").clone().removeAttr("id").attr("id","request"+this.id).appendTo(".table-body")
    console.log(this);
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
    ;

    jQuery('#request'+this.id+' span.number-col').text(this.id);
    jQuery('#request'+this.id+' span.first-name-col').text(this.first_name);
    jQuery('#request'+this.id+' span.last-name-col').text(this.last_name);
    jQuery('#request'+this.id+' span.email-col').text(this.email);
    jQuery("#request"+this.id+' span.set-col').text(this.type+' '+this.type_id);
    jQuery("#request"+this.id+' .request-date-col').text(this.request_date+', ' + days_ago);
    jQuery('#request'+this.id+' span.details-col button').attr('id','#bdrequest-details-'+this.id);
    (this.request_status == "pending"? jQuery('#request'+this.id+' #pending_options').css('display', 'block') : jQuery('#request'+this.id+' #pending_options').css('display', 'none'));
    (this.request_status == "pending"? jQuery('#request'+this.id+' #status-col-pending').css('display', 'block') : jQuery('#request'+this.id+' #status-col-pending').css('display', 'none'));
    (this.request_status == "accept"? jQuery('#request'+this.id+' #status-col-accepted').css('display', 'block') : jQuery('#request'+this.id+' #status-col-accepted').css('display', 'none'));
    (this.request_status == "reject"? jQuery('#request'+this.id+' #status-col-rejected').css('display', 'block') : jQuery('#request'+this.id+' #status-col-rejected').css('display', 'none'));
  })

  $(".details-col button").on('click', function () {
    var requestId = jQuery(this).closest('.user-container').data('id');
    showDetails(requestId);
  });

  jQuery(document).on('click', '#accept_request_icon',  function(){
    var requestId = jQuery(this).closest('.user-container').data('id');
    updateRequest(requestId, 'accept', false)
  });

  jQuery(document).on('click', '#reject_request_icon',  function(){
    var requestId = jQuery(this).closest('.user-container').data('id');
    updateRequest(requestId, 'reject', false)
  });

  $('#request_popin #accept_request').on('click', function () {
    var requestId = jQuery('#request_popin').data('id');
    updateRequest(requestId, 'accept', false)
  });

  $('#request_popin #reject_request').on('click', function () {
    var requestId = jQuery('#request_popin').data('id');
    updateRequest(requestId, 'reject', false)
  });

});

function showDetails(requestId) {
  jQuery('#request_popin').data('id', requestId);
  jQuery('#request_popin #popin_details_firstname p').text(jQuery('#request'+requestId).data('first_name'));
  jQuery('#request_popin #popin_details_lastname p').text(jQuery('#request'+requestId).data('last_name'));
  jQuery('#request_popin #popin_details_email p').text(jQuery('#request'+requestId).data('email'));
  jQuery('#request_popin #popin_details_organisation p').text(jQuery('#request'+requestId).data('organisation') ?? '');
  jQuery('#request_popin #popin_details_telephone p').text(jQuery('#request'+requestId).data('telephone') ?? '');
  jQuery('#request_popin #popin_details_reason p').text(jQuery('#request'+requestId).data('reason'));
  jQuery('#request_popin #popin_details_set p').text(jQuery('#request'+requestId).data('type') + ' ' + jQuery('#request'+requestId).data('type_id'));
  jQuery('#request_popin #popin_details_nbr_photos p').text(jQuery('#request'+requestId).data('nb_images'));
  jQuery('#request_popin #popin_details_request_date p').text(jQuery('#request'+requestId).data('request_date'));
  jQuery('#request_popin #popin_details_set_size p').text(jQuery('#request'+requestId).data('image_size'));

  jQuery('#request_popin #popin_details_rejected_status').hide();
  jQuery('#request_popin #popin_details_accepted_status').hide();
  jQuery('#request_popin #popin_details_request_pending_status').hide();
  jQuery('#request_popin #change_request_status').hide()
  
  if(jQuery('#request'+requestId).data('request_status') == 'pending'){
    jQuery('#request_popin #popin_details_request_pending_status, #change_request_status').show()
  }

  if(jQuery('#request'+requestId).data('request_status') == 'reject'){
    jQuery('#request_popin #popin_details_rejected_status').show();
    jQuery('#request_popin #popin_details_rejected_status p').text(jQuery('#request'+requestId).data('status_change_date'));
    
  }

  if(jQuery('#request'+requestId).data('request_status') == 'accept'){
    jQuery('#request_popin #popin_details_accepted_status').show();
    jQuery('#request_popin #popin_details_accepted_status p').text(jQuery('#request'+requestId).data('status_change_date'));
  }

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
    url: 'ws.php?format=json&method=pwg.downloadRequest.getList',
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
    jQuery('#request'+requestId+' .wait-for-server , #request'+requestId+' #pending_options').toggle()
  } 
  else if(popin)
  {
    jQuery('#request_popin .wait-for-server , #request_popin #change_request_status').toggle()
  }

  jQuery.ajax({
    type: 'POST',
    url: 'ws.php?format=json&method=pwg.downloadRequest.update',
    data: {
        id: requestId,
        status_change_date : page_infos_for_update.status_change_date,
        request_status: status,
    },

    success: function (data) {
      jQuery('#request'+requestId+' .wait-for-server, #request'+requestId+' #status-col-pending, #request'+requestId+' #pending_options ').hide();
      jQuery('#request_popin .wait-for-server').hide();
      jQuery(' #request_popin #popin_details_request_pending_status').hide();
      jQuery('#request_popin #change_request_status').hide();
      jQuery('#request'+requestId)
        .data("request_status", status)
        .data("status_change_date", page_infos_for_update.status_change_date);

      if(status == 'accept'){
        jQuery('#request'+requestId+' #status-col-accepted').show();
        jQuery('#request_popin #popin_details_accepted_status').show();
        jQuery('#request_popin #popin_details_accepted_status p').text(jQuery('#request'+requestId).data('status_change_date'));
      }
      else if(status == 'reject')
      {
        jQuery('#request'+requestId+' #status-col-rejected').show()
        jQuery('#request_popin #popin_details_rejected_status').show(); 
        jQuery('#request_popin #popin_details_rejected_status p').text(jQuery('#request'+requestId).data('status_change_date'));
      }
      // console.log(data);
    },
    error: function (e) {
        console.log('error');
    }
  });

}