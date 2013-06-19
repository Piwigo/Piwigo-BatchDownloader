{combine_css path=$BATCH_DOWNLOAD_PATH|@cat:"template/style.css"}

{if $set.U_DOWNLOAD}
{footer_script}
setTimeout("document.location.href = '{$set.U_DOWNLOAD}';", 1000);
{/footer_script}
{/if}

{if $missing_derivatives}
{combine_script id='jquery.progressBar' load='footer' path='themes/default/js/plugins/jquery.progressbar.min.js'}
{combine_script id='jquery.ajaxmanager' load='footer' path='themes/default/js/plugins/jquery.ajaxmanager.js'}

{footer_script}
var derivatives = {ldelim}
  elements: ["{'","'|@implode:$missing_derivatives}"],
  done: 0,
  total: {$missing_derivatives|@count},
  
  finished: function() {ldelim}
    return derivatives.done == derivatives.total;
  }
};

function progress() {ldelim}
  jQuery('#progressBar').progressBar(derivatives.done/derivatives.total*100, {ldelim}
    width: 300,
    height: 24,
    boxImage: '{$ROOT_URL}{$BATCH_DOWNLOAD_PATH}template/images/progress_box.png',
    barImage: '{$ROOT_URL}{$BATCH_DOWNLOAD_PATH}template/images/progress_bar.png'
  });
}

{literal}
var queuedManager = jQuery.manageAjax.create('queued', { 
  queue: true,  
  cacheResponse: false,
  maxRequests: 1
});

function next_derivative() {
  if (derivatives.finished()) {
    setTimeout("location.reload(true)", 1000);
    return;
  }
  
  queuedManager.add({
    type: 'GET', 
    url: derivatives.elements[ derivatives.done ], 
    dataType: 'json',
    data: { ajaxload: 'true' },
    success: function() {
      derivatives.done++;
      progress();
      next_derivative();
    },
    error: function() {
      derivatives.done++;
      progress();
      next_derivative();
    }
  });
}

progress();
setTimeout("next_derivative()", 1000);
{/literal}{/footer_script}

{/if}


{* <!-- Menubar & titrePage --> *}
{if $themeconf.name == "stripped" or $themeconf.parent == "stripped"}
  {include file=$BATCH_DOWNLOAD_ABS_PATH|@cat:'template/themes/stripped.tpl'}
  {assign var="clear" value="true"}
{elseif $themeconf.name == "simple-grey" or $themeconf.parent == "simple"}
  {include file=$BATCH_DOWNLOAD_ABS_PATH|@cat:'template/themes/simple.tpl'}
  {assign var="clear" value="true"}
{else}
  {include file=$BATCH_DOWNLOAD_ABS_PATH|@cat:'template/themes/default.tpl'}
{/if}

{if isset($errors) or not empty($infos)}
{include file='infos_errors.tpl'}
{/if}


{if $set}
<fieldset>
  <legend>{'Download info'|@translate}</legend>
  <h2>{$set.NAME}</h2>
  {if $set.COMMENT}<blockquote class="comment">{$set.COMMENT}</blockquote>{/if}
  
  <ul class="set-infos">
    <li class="error">{$elements_error}</li>
    <li><b>{'%d photos'|@translate|@sprintf:$set.NB_IMAGES}</b>{if $set.U_EDIT_SET}, <a href="{$set.U_EDIT_SET}" rel="nofollow">{'Edit the set'|@translate}</a>{/if}</li>
    <li><b>{'Photo sizes'|@translate}:</b> {if $set.SIZE=='original'}{'Original'|@translate}{else}{$set.SIZE|@translate}{/if} {if $set.SIZE_INFO}<span class="downloadSizeDetails">({$set.SIZE_INFO})</span>{/if}</li>
    <li><b>{'Estimated size'|@translate}:</b> {$set.TOTAL_SIZE}</li>
    <li><b>{'Estimated number of archives'|@translate}:</b> {$set.NB_ARCHIVES} <i>({'real number of archives can differ'|@translate})</i></li>
    <li><b>{'Created on'|@translate}:</b> {$set.DATE_CREATION}</li>
  </ul>
</fieldset>

{if $missing_derivatives}
<fieldset>
  <legend>{'Preparation'|@translate}</legend>
  
  <p>{'Please wait, your download is being prepared. This page will automatically refresh when it is ready.'|@translate}</p>
  
  <div id="progressBar"></div>
  
  <a href="{$set.U_CANCEL}" class="cancel-down" onClick="return confirm('{'Are you sure?'|@translate}');">{'Cancel this download'|@translate}</a>
</fieldset>

{elseif $zip_links}
<fieldset>
  <legend>{'Download links'|@translate}</legend>
  
  <ul class="download-links">
    {$zip_links}
    <li class="warning">{'<b>Warning:</b> all files will be deleted within %d hours'|@translate|@sprintf:$archive_timeout}</li>
  </ul>
  
  {if $set.U_CANCEL}<a href="{$set.U_CANCEL}" class="cancel-down" onClick="return confirm('{'Are you sure?'|@translate}');">{'Cancel this download'|@translate}</a>{/if}
</fieldset>
{/if}
{/if}

{if $clear}<div style="clear: both;"></div>
</div>{/if}
</div>{* <!-- content --> *}
