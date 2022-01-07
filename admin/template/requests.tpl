{combine_css path=$BATCH_DOWNLOAD_PATH|cat:"admin/template/style.css"}
<div id="bd-request-table">
  <div id="bd-request-table-content">
    <div class="table-head">
        <span class="user-header-col first-name-col" >First Name</span>
        <span class="user-header-col last-name-col ">Last Name</span>
        <span class="user-header-col email-col">Email</span>
        <span class="user-header-col set-col">Set</span>
        <span class="user-header-col request-date-col">Request Date</span>
        <span class="user-header-col status-col">Status</span>
        <span class="user-header-col details-col"></span>

    </div>
    <div class="table-body">
{foreach from=$requests item=request}
      <div class="user-container">
        <span class="user-col first-name-col">{$request.first_name}</span>
        <span class="user-col last-name-col">{$request.last_name}</span>
        <span class="user-col email-col">{$request.email}</span>
        <span class="user-col set-col">{$request.type} {$request.type_id} </span>
        <span class="user-col request-date-col">{$request.request_date}</span>
        <span class="user-col status-col {if $request.request_status == "accepted"}greenText{elseif $request.request_status == "rejected"}redText{/if}">
        {$request.request_status}
        </span>
        <span class="user-col details-col"><button id="bd-request-details" onclick="showDetails('bd-request-details-popin-{$request.id}')">See request details</button></span>

      </div>

      <div class="bd-request-details-popin" id="bd-request-details-popin-{$request.id}">
        <div class="details-popin-content">
          <a class="icon-cancel CloseUserList" onclick="hideDetails('bd-request-details-popin-{$request.id}')"></a>
          <div class="bd-detail-row">
            <div class="bd-details-table">
              <ul>
                <li><label class="">First name</label>
                <p class="">{$request.first_name}</p></li>
                <li><label class="">Last name</label>
                <p class="">{$request.last_name}</p></li>
                <li>{if $request.organisation}<label class="">Organisation</label>{/if}
                <p class="">{$request.organisation}</p></li>
                <li><label class="">Email</label>
                <p class="">{$request.email}</p></li>
                <li>{if $request.telephone}<label class="">Telephone</label>{/if}
                <p class="">{$request.telephone}</p></li>
                <li>{if $request.profession}<label class="">Profession</label>{/if}
                <p class="">{$request.profession}</p></li>
                <li><label class="">Reason</label>
                <p class="">{$request.reason}</p></li>
                <li><label class="">Set</label>
                <p class="">{$request.type} {$request.type_id} </p></li>
                <li><label class="">Number of photos</label>
                <p class="">{$request.nb_images}</p></li>
                <li><label class="">Request Date</label>
                <p class="">{$request.request_date}</p></li>
                <li><label class="">Set size</label>
                <p class="">{$request.size}</p></li>
  {if $request.request_status == "rejected" }
                <li><label class="redText">Rejected date</label>
                <p class="redText">{$request.status_change_date}</p></li>
  {elseif $request.request_status == "accepted" }
                <li><label class="greenText">Accepted date</label>
                <p class="greenText">{$request.status_change_date}</p></li>
  {/if}
              </ul>
            </div>
          </div>
  {if $request.request_status == "pending"}
        <form method="post" action="{$F_ACTION}">
          <input id="requestId" name="requestId" type="hidden" value="{$request.id}">
          <input class="" type="submit" value="accepted" name="status">
          <input class="" type="submit" value="rejected" name="status">
        </form>
  {/if}
        </div>

      </div>
{/foreach}
    </div>
  </div>
</div>



{footer_script require='jquery'}

function showDetails(id) {
    var popin = document.getElementById(id);
    popin.style.display= "block";
}

function hideDetails(id) {
    var popin = document.getElementById(id);
    popin.style.display= "none";
}

{/footer_script}