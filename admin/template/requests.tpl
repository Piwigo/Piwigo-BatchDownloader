{combine_css path=$BATCH_DOWNLOAD_PATH|cat:"admin/template/style.css"}
<div id="bd-request-table">
  <div id="bd-request-table-content">
    <div class="table-head">
        <span class="user-header-col number-col">#</span>
        <span class="user-header-col first-name-col" >{'First name'|translate}</span>
        <span class="user-header-col last-name-col ">{'Last name'|translate}</span>
        <span class="user-header-col email-col">{'Email'|translate}</span>
        <span class="user-header-col set-col">{'Set'|translate}</span>
        <span class="user-header-col request-date-col">{'Request Date'|translate}</span>
        <span class="user-header-col status-col">{'Status'|translate}</span>
        <span class="user-header-col details-col"></span>
    </div>
    <div class="table-body">

      <div class="user-container" id="jango-fett">
        <span class="user-col number-col"></span>
        <span class="user-col first-name-col"></span>
        <span class="user-col last-name-col"></span>
        <span class="user-col email-col"></span>
        <span class="user-col set-col"></span>
        <span class="user-col request-date-col"></span>
        <span class="user-col status-col">
          <span id="status-col-pending">{'pending'|translate}</span>
          <span id="status-col-accepted" class="greenText">
            <span id="">{'accepted'|translate}</span>
          </span>
          <span id="status-col-rejected" class="redText">
            <span id="">{'rejected'|translate}</span></span>
          <div id="pending_options">
            <a id="accept_request_icon" class="button-accept">
              <i class="icon-ok"></i>
            </a>
            <a id="reject_request_icon" class="button-reject">
              <i class="icon-cancel-circled"></i>
            </a>
          </div>
          <div class="wait-for-server"><i class="icon-spin6 animate-spin"></i></div>
        </span>
        <span class="user-col details-col">
          <button id="" >
            <i class="icon-info-circled-1"></i>
          </button>
        </span>
      </div>

    </div>
  </div>
</div>

<div id="request_popin">
  <div class="bd-request-details-popin">
    <div class="details-popin-content">
      <a class="icon-cancel CloseUserList" onclick="hideDetails()"></a>
      <div class="bd-detail-row">
        <div class="bd-details-table">
          <ul>
            <li id="popin_details_firstname">
              <label>{'First name'|translate}</label>
              <p></p>
            </li>
            <li id="popin_details_lastname">
              <label>{'Last name'|translate}</label>
              <p></p>
            </li>
            <li id="popin_details_organisation">
              <label>{'Organisation'|translate}</label>
              <p></p>
            </li>
            <li id="popin_details_email">
              <label>{'Email'|translate}</label>
              <p></p>
            </li>
            <li id="popin_details_telephone">
              <label>{'Telephone'|translate}</label>
              <p></p>
            </li>
            <li id="popin_details_profession">
              <label>{'Profession'|translate}</label>
              <p></p>
            </li>
            <li id="popin_details_reason">
              <label>{'Reason'|translate}</label>
              <p></p>
            </li>
            <li id="popin_details_set">
              <label>{'Set'|translate}</label>
              <p></p>
            </li>
            <li id="popin_details_nbr_photos">
              <label>{'Number of photos'|translate}</label>
              <p></p>
            </li>
            <li id="popin_details_request_date">
              <label>{'Request Date'|translate}</label>
              <p></p>
            </li>
            <li id="popin_details_set_size">
              <label>{'Set size'|translate}</label>
              <p></p>
            </li>
            <li id="popin_details_rejected_status">
              <label class="redText">{'Rejected date'|translate}</label>
              <p class="redText"></p>
            </li>
            <li id="popin_details_accepted_status">
              <label class="greenText">{'Accepted date'|translate}</label>
              <p class="greenText"></p>
            </li>
            <li id="popin_details_request_pending_status">
              <label>{'Request status'|translate}</label>
              <p>{'pending'|translate}</p>
            </li>
          </ul>
        </div>
        <div id="change_request_status">
          <button id="accept_request" class="button-accept" type="submit" value="accept"><i class="icon-ok "></i>{'Accept'|translate}</button>
          <button id="reject_request" type="submit" class="button-reject" value="reject"><i class="icon-cancel "></i>{'Reject'|translate}</button>
        </div>
        <div class="wait-for-server"><i class="icon-spin6 animate-spin"></i></div>
      </div>
    </div>
  </div>
</div>



{footer_script require='jquery'}
var page_infos_for_update = {$PAGE_INFOS_FOR_UPDATE};

var activated_collection_request = {$ACTIVATED_COLLECTION_REQUEST};

{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}
{combine_script id='bd_download_form' require='jquery' load='footer' path='plugins/BatchDownloader/admin/template/js/requests.js'}

{/footer_script}