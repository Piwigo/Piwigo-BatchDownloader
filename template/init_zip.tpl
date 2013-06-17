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

function progress(success) {ldelim}
  jQuery('#progressBar').progressBar(derivatives.done, {ldelim}
    max: derivatives.total,
    textFormat: 'fraction',
    boxImage: '{$ROOT_PATH}themes/default/images/progressbar.gif',
    barImage: '{$ROOT_PATH}themes/default/images/progressbg_red.gif'
  });
  if (success !== undefined) {ldelim}
		var type = success ? '.regenerateSuccess': '.regenerateError',
			s = parseInt(jQuery(type).html());
		jQuery(type).html(++s);
	}
}

{literal}
var queuedManager = jQuery.manageAjax.create('queued', { 
  queue: true,  
  cacheResponse: false,
  maxRequests: 1
});

function next_derivative() {
  if (derivatives.finished()) {
		alert("finish");
    return;
	}
  
  $("#damn").append(derivatives.elements[ derivatives.done ]+"<br>");
  
  jQuery.manageAjax.add("queued", {
    type: 'GET', 
    url: derivatives.elements[ derivatives.done ]+'&ajaxload=true', 
    dataType: 'json',
    success: function(data) {
      derivatives.done++;
      progress(true);
      next_derivative();
    },
    error: function(data) {
      derivatives.done++;
      progress(false);
      next_derivative();
    }
  });
}

$("#begin").click(function() {
  progress();
  next_derivative();
});
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
    <li><b>{'Size'|@translate}:</b> {$set.SIZE} {if $set.SIZE_INFO}<span class="downloadSizeDetails">({$set.SIZE_INFO})</span>{/if}</li>
    <li><b>{'Estimated size'|@translate}:</b> {$set.TOTAL_SIZE} MB</li>
    <li><b>{'Estimated number of archives'|@translate}:</b> {$set.NB_ARCHIVES} <i>({'real number of archives can differ'|@translate})</i></li>
    <li><b>{'Created on'|@translate}:</b> {$set.DATE_CREATION}</li>
  </ul>
</fieldset>

{if $missing_derivatives}
<fieldset>
  <legend>Stuff happening</legend>
  
  <a id="begin">GO</a>
  
  <div id="regenerationMsg" class="bulkAction">
    <span class="progressBar" id="progressBar"></span>
  </div>
  
  <span class="regenerateSuccess">0</span> -
  <span class="regenerateError">0</span>
  
  <div id="damn">
  </div>
</fieldset>
{/if}

{if $zip_links}
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
