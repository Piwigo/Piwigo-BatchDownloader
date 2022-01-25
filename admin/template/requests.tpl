{combine_css path=$BATCH_DOWNLOAD_PATH|cat:"admin/template/style.css"}
<div id="bd-request-table">
  <div id="bd-request-table-content">
    <div class="table-head">
        <span class="user-header-col number-col">#</span>
        <span class="user-header-col first-name-col" >First Name</span>
        <span class="user-header-col last-name-col ">Last Name</span>
        <span class="user-header-col email-col">Email</span>
        <span class="user-header-col set-col">Set</span>
        <span class="user-header-col request-date-col">Request Date</span>
        <span class="user-header-col status-col">Status</span>
        <span class="user-header-col details-col"></span>
    </div>
    <div class="table-body">
    </div>
    <div id="request-popin-content">
    </div>
  </div>
</div>



{footer_script require='jquery'}
var page_infos_for_update = {$PAGE_INFOS_FOR_UPDATE};

{combine_script id='bd_download_form' require='jquery' load='footer' path='plugins/batchDownloader/admin/template/js/requests.js'}

{/footer_script}