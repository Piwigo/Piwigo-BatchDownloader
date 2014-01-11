{if $set}
<fieldset>
  <h3>{$set.NAME}</h3>
  {if $set.COMMENT}<blockquote>{$set.COMMENT}</blockquote>{/if}
  {assign var="nb_images" value='<span class="nbImagesSet">'|cat:$set.NB_IMAGES|@cat:'</span>'}
  {'%d photos'|translate|replace:'%d':'%s':$nb_images}
  <br>
  <b><a href="{$U_INIT_ZIP}" rel="nofollow">{'Return to download page'|translate} →</a></b>
</fieldset>

{if !empty($THUMBNAILS)}
<ul class="thumbnails" id="thumbnails">
{$THUMBNAILS}
</ul>

{else}
{'This download set is empty'|translate}
{/if}

{if !empty($navbar)}{include file='navigation_bar.tpl'|get_extent:'navbar'}{/if}
{/if}