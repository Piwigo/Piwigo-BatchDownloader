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
  <h3>{$set.NAME}</h3>
  {if $set.COMMENT}<blockquote>{$set.COMMENT}</blockquote>{/if}
  {assign var="nb_images" value='<span class="nbImagesSet">'|@cat:$set.NB_IMAGES|@cat:'</span>'}
  {'%d photos'|@translate|replace:'%d':'%s'|sprintf:$nb_images}
  <br>
  <b><a href="{$U_INIT_ZIP}" rel="nofollow">{'Return to download page'|@translate} →</a></b>
</fieldset>


{if !empty($THUMBNAILS)}
<ul class="thumbnails" id="thumbnails">
{$THUMBNAILS}
</ul>
{else}
{'This download set is empty'|@translate}
{/if}

{if !empty($navbar)}{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}
{/if}

{if $clear}<div style="clear: both;"></div>
</div>{/if}
</div>{* <!-- content --> *}