{$MENUBAR}

<div id="content" class="content{if isset($MENUBAR)} contentWithMenu{/if}">
<div class="titrePage{if isset($chronology.TITLE)} calendarTitleBar{/if}">
  <ul class="categoryActions"></ul>
  <h2>{$TITLE}</h2>
</div>{* <!-- titrePage --> *}

{if isset($errors) or not empty($infos)}
{include file='infos_errors.tpl'}
{/if}

<p>
  <h3>{$set.NAME}</h3>
  {if $set.COMMENT}<blockquote>{$set.COMMENT}</blockquote>{/if}
  {'Nb images'|@translate}: <span class="nbImages">{$set.NB_IMAGES}</span>
</p>


{if !empty($THUMBNAILS)}
<ul class="thumbnails" id="thumbnails">
{$THUMBNAILS}
</ul>
{/if}

{if !empty($navbar)}{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

<p style="text-align:center;font-weight:bold;margin:20px;"><a href="{$U_INIT_ZIP}" rel="nofollow">{'Return to download page'|@translate}</a></p>

</div>{* <!-- content --> *}