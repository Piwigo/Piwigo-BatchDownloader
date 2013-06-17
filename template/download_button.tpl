{footer_script require='jquery'}
var batchdown_count = {$BATCH_DWN_COUNT};
var batchdown_string = "{'Confirm the download of %d pictures?'|@translate}";
{literal}
jQuery().ready(function() {
  jQuery("#batchDownloadLink").click(function() {
	  var elt = jQuery("#batchDownloadBox");

    elt.css("left", Math.min( jQuery(this).offset().left, jQuery(window).width() - elt.outerWidth(true) - 5))
      .css("top", jQuery(this).offset().top + jQuery(this).outerHeight(true))
      .toggle();

    return false;
  });

  jQuery("#batchDownloadBox").on("mouseleave click", function() {
    jQuery(this).hide();
  });
  
  jQuery("#batchDownloadBox a").click(function() {
    return confirm(batchdown_string.replace('%d', batchdown_count));
  });
});
{/literal}{/footer_script}

{html_style}{literal}
.downloadSizeDetails {font-style:italic; font-size:80%;}
{/literal}{/html_style}

<script type="text/javascript"></script>
<li><a href="{$BATCH_DWN_URL}" id="batchDownloadLink" title="{'Download all pictures of this selection'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
  <span class="pwg-icon batch-downloader-icon" style="background:url('{$ROOT_PATH}{$BATCH_DOWNLOAD_PATH}template/zip.png') center center no-repeat;">&nbsp;</span><span class="pwg-button-text">{'Download'|@translate}</span>
</a></li>

<div id="batchDownloadBox" class="switchBox" style="display:none">
  <div class="switchBoxTitle">{'Download'|@translate} - {'Photo sizes'|@translate}</div>
  {foreach from=$BATCH_DOWNLOAD_SIZES item=size name=loop}{if !$smarty.foreach.loop.first}<br>{/if}
  <a href="{$BATCH_DWN_URL}{$size.TYPE}" rel="nofollow">
    {$size.DISPLAY} {if $size.SIZE}<span class="downloadSizeDetails">({$size.SIZE})</span>{/if}
  </a>
  {/foreach}
</div>