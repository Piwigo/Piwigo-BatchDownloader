{$MENUBAR}

{if $set.U_DOWNLOAD}
{footer_script}
setTimeout("document.location.href = '{$set.U_DOWNLOAD}';", 1000);
{/footer_script}
{/if}

<div id="content" class="content{if isset($MENUBAR)} contentWithMenu{/if}">
<div class="titrePage">
  <ul class="categoryActions"></ul>
  <h2>{$TITLE}</h2>
</div>{* <!-- titrePage --> *}

{if isset($errors) or not empty($infos)}
{include file='infos_errors.tpl'}
{/if}


<p>
  <h3>{$set.NAME}</h3>
  {if $set.COMMENT}<blockquote>{$set.COMMENT}</blockquote>{/if}
  <span>{'%d images'|@translate|@sprintf:$set.NB_IMAGES}{if $set.U_EDIT_SET}, <a href="{$set.U_EDIT_SET}" rel="nofollow">{'Edit the set'|@translate}</a>{/if}</span>
</p>

<p>
  <b>{'Estimated size'|@translate}:</b> {$set.TOTAL_SIZE} MB<br>
  <b>{'Estimated number of archives'|@translate}:</b> {$set.NB_ARCHIVES}<br>
  <i>{'These datas are an estimation, real specs can differ'|@translate}</i>
</p>

<p>
<b>Download links:</b>
{$set.LINKS}
</p>

<p class="infos">
{'<b>Warning:</b> all files will be deleted within %d hours'|@translate|@sprintf:$archive_timeout}
</p>

</div>{* <!-- content --> *}
