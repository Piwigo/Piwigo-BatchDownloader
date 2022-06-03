{combine_css path=$BATCH_DOWNLOAD_PATH|cat:"template/style.css"}
{if !$HAS_PERMISSION_TO_DOWNLOAD && $BATCH_DWN_REQUEST_PARAM }
<a id="batchDownloadRequest" title="{'Request permission to download all pictures of this selection'|translate}" class="pwg-state-default pwg-button nav-link " rel="nofollow">
  <span class="pwg-icon batch-downloader-icon fas fa-cloud-download-alt fa-fw" style="background:url('{$ROOT_URL}{$BATCH_DOWNLOAD_PATH}template/images/zip.png') center center no-repeat;">&nbsp;</span><span class="pwg-button-text">{'Request download'|translate}</span>
  {foreach from=$BATCH_DWN_SIZES_ACCEPETED item=size name=loop}{if !$smarty.foreach.loop.first}<br>{/if}
  <a href="{$BATCH_DWN_URL}{$size.TYPE}" rel="nofollow">
    {$size.DISPLAY} {if $size.SIZE}<span class="downloadSizeDetails">({$size.SIZE})</span>{/if}
  </a>
  {/foreach}
</a>

{else}
<a href="{$BATCH_DWN_URL}{$BATCH_DWN_SIZE}" id="batchDownloadLink" title="{'Download all pictures of this selection'|translate}" class="pwg-state-default pwg-button" rel="nofollow">
  <span class="pwg-icon batch-downloader-icon fas fa-cloud-download-alt fa-fw" style="background:url('{$ROOT_URL}{$BATCH_DOWNLOAD_PATH}template/images/zip.png') center center no-repeat;">&nbsp;</span><span class="pwg-button-text">{'Download'|translate}</span>
</a>
{/if}

{if isset($BATCH_DWN_SIZES)}
{combine_script id='core.switchbox' load='async' require='jquery' path='themes/default/js/switchbox.js'}

<div id="batchDownloadBox" class="switchBox" style="display:none">
{if $BATCH_DWN_REQUEST_PARAM }
  <div id="batchDownloadAnotherRequest"><a href="#">{'Request to download another size'|translate}</a></div>
{/if}
  <div class="batchDownloadSizeList">
    <div class="switchBoxTitle">{'Download'|translate} - {'Photo sizes'|translate}</div>
{foreach from=$BATCH_DWN_SIZES item=size name=loop}{if !$smarty.foreach.loop.first}<br>{/if}
    <a href="{$BATCH_DWN_URL}{$size.TYPE}" rel="nofollow">
      {$size.DISPLAY} {if isset($size.SIZE)}<span class="downloadSizeDetails">({$size.SIZE})</span>{/if}
    </a>
{/foreach}
  </div>
  
</div>

{/if}


{footer_script require='jquery'}

var batchdown_count = {$BATCH_DWN_COUNT};
var batchdown_string = "{'Confirm the download of %d pictures?'|translate}";

{* Language variable *}
var str_request_form = '{'Request permission to download'|translate|escape:javascript}';
var str_save = '{'Save'|translate|escape:javascript}';
var str_request = '{'Request'|translate|escape:javascript}';
var str_cancel = '{'Cancel'|translate|escape:javascript}';
var str_download_request = '{'Download request'|translate|escape:javascript}';
var str_download_request_sent = '{'Your download request has been sent'|translate|escape:javascript}';
var str_download_request_error = '{'There was an error sending your request, please try again'|translate|escape:javascript}';

{* Pass HTML form *}
var bd_request_form = `{$BATCH_DWN_REQUEST}`;

{*Get page infos*}
var page_infos_for_request = {$PAGE_INFOS_FOR_REQUEST};

{if isset($BATCH_DWN_SIZES)}
  (SwitchBox=window.SwitchBox||[]).push("#batchDownloadLink", "#batchDownloadBox");

  jQuery("#batchDownloadBox .batchDownloadSizeList a").click(function() {
    return confirm(batchdown_string.replace('%d', batchdown_count));
  });
{else}
  jQuery("#batchDownloadLink").click(function() {
    return confirm(batchdown_string.replace('%d', batchdown_count));
  });
{/if}

{/footer_script}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{combine_script id='jquery.ajaxmanager' path='themes/default/js/plugins/jquery.ajaxmanager.js' load='footer'}
{combine_script id='thumbnails.loader' path='themes/default/js/thumbnails.loader.js' require='jquery.ajaxmanager' load='footer'}
{combine_script id='bd_download_common' require='jquery' load='footer' path='plugins/BatchDownloader/template/js/downloadCommon.js'}
{combine_script id='bd_download_form' require='jquery' load='footer' path='plugins/BatchDownloader/template/js/downloadForm.js'}

{html_style}
.downloadSizeDetails { font-style:italic; font-size:80%; }
{/html_style}