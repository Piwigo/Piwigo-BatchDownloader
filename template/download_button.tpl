{combine_css path=$BATCH_DOWNLOAD_PATH|cat:"template/style.css"}
{if $BATCH_DWN_REQUEST_CONF == true}
<a id="batchDownloadRequest" title="{'Request permission to download all pictures of this selection'|translate}" class="pwg-state-default pwg-button nav-link" rel="nofollow">
  <span class="pwg-icon batch-downloader-icon fas fa-cloud-download-alt fa-fw" style="background:url('{$ROOT_URL}{$BATCH_DOWNLOAD_PATH}template/images/zip.png') center center no-repeat;">&nbsp;</span><span class="pwg-button-text">{'Download'|translate}</span>
</a>
{else}
<a href="{$BATCH_DWN_URL}{$BATCH_DWN_SIZE}" id="batchDownloadLink" title="{'Download all pictures of this selection'|translate}" class="pwg-state-default pwg-button" rel="nofollow">
  <span class="pwg-icon batch-downloader-icon fas fa-cloud-download-alt fa-fw" style="background:url('{$ROOT_URL}{$BATCH_DOWNLOAD_PATH}template/images/zip.png') center center no-repeat;">&nbsp;</span><span class="pwg-button-text">{'Download'|translate}</span>
</a>
{/if}

{if isset($BATCH_DWN_SIZES)}
{combine_script id='core.switchbox' load='async' require='jquery' path='themes/default/js/switchbox.js'}

<div id="batchDownloadBox" class="switchBox" style="display:none">
  <div class="switchBoxTitle">{'Download'|translate} - {'Photo sizes'|translate}</div>
  {foreach from=$BATCH_DWN_SIZES item=size name=loop}{if !$smarty.foreach.loop.first}<br>{/if}
  <a href="{$BATCH_DWN_URL}{$size.TYPE}" rel="nofollow">
    {$size.DISPLAY} {if $size.SIZE}<span class="downloadSizeDetails">({$size.SIZE})</span>{/if}
  </a>
  {/foreach}
</div>
{/if}

{footer_script require='jquery'}
var batchdown_count = {$BATCH_DWN_COUNT};
var batchdown_string = "{'Confirm the download of %d pictures?'|translate}";

{* Language variable *}
var str_request_form = '{'Request permission to download'|translate|escape:javascript}';
var str_save = '{'Save'|translate|escape:javascript}';
var str_request = '{'Request'|translate|escape:javascript}';
var str_cancel = '{'Cancel'|translate}';

{* Pass HTML form *}
var bd_request_form = `{$BATCH_DWN_REQUEST}`;

{if isset($BATCH_DWN_SIZES)}
  (SwitchBox=window.SwitchBox||[]).push("#batchDownloadLink", "#batchDownloadBox");

  jQuery("#batchDownloadBox a").click(function() {
    return confirm(batchdown_string.replace('%d', batchdown_count));
  });
{else}
  jQuery("#batchDownloadLink").click(function() {
    return confirm(batchdown_string.replace('%d', batchdown_count));
  });
{/if}

{literal}
if (window.jconfirmConfig !== true) {
{/literal}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}

{literal}   
}
{/literal}

{/footer_script}


{combine_script id='bd_download_common' require='jquery' load='footer' path='plugins/UserCollections/template/js/collectionCommon.js'}
{combine_script id='bd_download_form' require='jquery' load='footer' path='plugins/batchDownloader/template/js/downloadForm.js'}



{html_style}
.downloadSizeDetails { font-style:italic; font-size:80%; }
{/html_style}