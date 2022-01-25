jQuery(document).ready(function () {
  var requests = getRequests();

  jQuery(requests).each(function(){
    var colorClass = '';
    if(this.request_status === "accept"){
      colorClass = 'greenText';
      this.request_status = "Accepted"
    }else if(this.request_status === "reject"){
      colorClass = 'redText';
      this.request_status = "Rejected"
    }

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
  
    jQuery('#bd-request-table-content .table-body').append('<div class="user-container" id="request'+this.id+'">\
      <span class="user-col number-col">'+this.id+'</span>\
      <span class="user-col first-name-col">'+this.first_name+'</span>\
      <span class="user-col last-name-col">'+this.last_name+'</span>\
      <span class="user-col email-col">'+this.email+'</span>\
      <span class="user-col set-col">'+this.type+' '+this.type_id+' </span>\
      <span class="user-col request-date-col">'+this.request_date+', ' + days_ago+'</span>\
      <span class="user-col status-col '+colorClass+'">'+this.request_status +(this.request_status == "pending"?'\
        <a id="accept_request_icon" class="button-accept" onclick="updateRequest('+this.id+', `accept`)">\
          <i class="icon-ok "></i>\
        </a>\
        <a id="reject_request_icon" class="button-reject" onclick="updateRequest('+this.id+', `reject`)">\
          <i class="icon-cancel-circled"></i>\
        </a>': '')+'</span>\
      <span class="user-col details-col"><button id="bdrequest-details-'+this.id+'" ><i class="icon-info-circled-1"></i></button></span>\
      </div>'
    );

    jQuery('#bdrequest-details-'+this.id).click(() => showDetails(this))
  })
  
});



function showDetails(request) {
  // console.log(request);
  jQuery('#request-popin-content').append('\
  <div class="bd-request-details-popin" id="bd-request-details-popin-'+request.id+'">\
        <div class="details-popin-content">\
          <a class="icon-cancel CloseUserList" onclick="hideDetails()"></a>\
          <div class="bd-detail-row">\
            <div class="bd-details-table">\
                <ul>\
                  <li><label class="">First name</label>\
                  <p class="">'+request.first_name+'</p></li>\
                  <li><label class="">Last name</label>\
                  <p class="">'+request.last_name+'</p></li>'+
                  (request.organisation?'<li><label class="">Organisation</label><p class="">'+request.organisation+'</p></li>': '') +'\
                  <li><label class="">Email</label>\
                  <p class="">'+request.email+'</p></li>'+
                  (request.telephone?'<li><label class="">Telephone</label><p class="">'+request.telephone+'</p></li>': '') +
                  (request.profession?'<li><label class="">Profession</label><p class="">'+request.profession+'</p></li>': '') +'\
                  <li><label class="">Reason</label>\
                  <p class="">'+request.reason+'</p></li>\
                  <li><label class="">Set</label>\
                  <p class="">'+request.type + ' ' + request.type_id+' </p></li>\
                  <li><label class="">Number of photos</label>\
                  <p class="">'+request.nb_images+'</p></li>\
                  <li><label class="">Request Date</label>\
                  <p class="">'+request.request_date+'</p></li>\
                  <li><label class="">Set size</label>\
                  <p class="">'+request.image_size+'</p></li>'+
                  (request.request_status == "Rejected"?'<li><label class="redText">Rejected date</label><p class="redText">'+request.status_change_date+'</p></li>': '') +
                  (request.request_status == "Accepted"?'<li><label class="greenText">Accepted date</label><p class="greenText">'+request.status_change_date+'</p></li>': '') +
                  (request.request_status == "pending"?'\
                  <li id="request_pending_status"><label class="">Request status</label><p>Pending</p></li>\
                  ': '') +'\
                </ul>\
            </div>'+
            (request.request_status == "pending"?'\
            <div id="change_request_status">\
              <button id="accept_request" class="button-accept" type="submit" value="accept"><i class="icon-ok "></i>Accept</button>\
              <button id="reject_request" type="submit" class="button-reject" value="reject"><i class="icon-cancel "></i>Reject</button>\
            </div>\
            ': '') +'\
          </div>\
        </div>\
      </div>'
  );
  jQuery('#accept_request').click(() => updateRequest(request.id, "accept"));
  jQuery('#accept_request').click(() => hideDetails());
  jQuery('#reject_request').click(() => updateRequest(request.id, "reject"));
  jQuery('#reject_request').click(() => hideDetails());
}

function hideDetails() {
  jQuery('#request-popin-content').empty();
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
          console.log(data)
        }
    },
    error: function (e) {
        console.log(e);
    }
  });

  return requests
}

function updateRequest(requestId, status) {
  jQuery.ajax({
    type: 'POST',
    dataType: 'json',
    url: 'ws.php?format=json&method=pwg.downloadRequest.update',
    data: {
        id: requestId,
        status_change_date : page_infos_for_update.status_change_date,
        request_status: status,
    },
    success: function (data) {
        if (data.stat == 'ok') {
            console.log(data)
        }
        else {
            console.log(data)
        }
    },
    error: function (e) {
        console.log(e);
    }
  });

  if(status == "accept")
  {
    jQuery("#request"+requestId+" .status-col ").replaceWith('<span class="user-col status-col greenText">Accepted</span>');
  }
  else
  {
    jQuery("#request"+requestId+" .status-col ").replaceWith('<span class="user-col status-col redText">Rejected</span>');
  }

  console.log(jQuery("#request"+requestId+" .status-col "));
}