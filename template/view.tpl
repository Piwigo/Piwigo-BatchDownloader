{if $themeconf.name != "stripped" and $themeconf.parent != "stripped" and $themeconf.name != "simple-grey" and $themeconf.parent != "simple"}
  {$MENUBAR}
{else}
  {assign var="intern_menu" value="true"}
{/if}
<div id="content" class="content{if isset($MENUBAR)} contentWithMenu{/if}">
{if $intern_menu}{$MENUBAR}{/if}

<div class="titrePage">
  <ul class="categoryActions"></ul>
  <h2>{$TITLE}</h2>
</div>{* <!-- titrePage --> *}

{if isset($errors) or not empty($infos)}
{include file='infos_errors.tpl'}
{/if}

{if $set}
<fieldset>
  <h3>{$set.NAME}</h3>
  {if $set.COMMENT}<blockquote>{$set.COMMENT}</blockquote>{/if}
  {assign var="nb_images" value='<span class="nbImagesSet">'|@cat:$set.NB_IMAGES|@cat:'</span>'}
  {'%d photos'|@translate|replace:'%d':'%s'|sprintf:$nb_images}
</fieldset>


{if !empty($THUMBNAILS)}
<ul class="thumbnails" id="thumbnails">
{$THUMBNAILS}
</ul>
{else}
{'This download set is empty'|@translate}
{/if}

{if !empty($navbar)}{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

<p style="text-align:center;font-weight:bold;margin:20px;"><a href="{$U_INIT_ZIP}" rel="nofollow">{'Return to download page'|@translate}</a></p>
{/if}

</div>{* <!-- content --> *}