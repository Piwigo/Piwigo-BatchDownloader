{combine_css path=$BATCH_DOWNLOAD_PATH|@cat:"admin/template/style.css"}

{combine_script id='jquery.chosen' load='footer' path='themes/default/js/plugins/chosen.jquery.min.js'}
{combine_css path="themes/default/js/plugins/chosen.css"}

{footer_script}{literal}
jQuery(document).ready(function() {
  jQuery(".chzn-select").chosen();
});
{/literal}{/footer_script}

<div class="titrePage">
	<h2>Advanced Downloader</h2>
</div>

<form method="post" action="" class="properties">
<fieldset>
  <legend>{'Download access'|@translate}</legend>
  
  <ul>
    <li>
      <label>
        <span class="property">{'User groups'|@translate}</span>
        <select name="groups[]" data-placeholder="{'Everybody'|@translate}" class="chzn-select" multiple="multiple" style="width:370px;">
          {html_options options=$group_options selected=$batch_download.groups}
        </select>
      </label>
    </li>
    <li>
      <label>
        <span class="property">{'Privacy level'|@translate}</span>
        <select name="level">
          {html_options options=$level_options selected=$batch_download.level}
        </select>
      </label>
    </li>
  </ul>
</fieldset>

<fieldset>
  <legend>{'Archives configuration'|@translate}</legend>
  
  <ul>
    <li>
      <label>
        <span class="property">{'Maximum photo size'|@translate}</span>
        <select name="photo_size">
          {html_options options=$sizes_options selected=$batch_download.photo_size}
        </select>
        NOT IMPLEMENTED
      </label>
    </li>
    <li>
      <label>
        <span class="property">{'Archive prefix'|@translate}</span>
        <input type="text" name="archive_prefix" value="{$batch_download.archive_prefix}">
      </label>
    </li>
    <li>
      <label>
        <span class="property">{'Archive comment'|@translate}</span>
        <input type="text" name="archive_comment" value="{$batch_download.archive_comment}" size="80">
      </label>
    </li>
    <li>
      <label>
        <span class="property">{'Delete unterminated downloads after'|@translate}</span>
        <input type="text" name="archive_timeout" value="{$batch_download.archive_timeout}" size="3">{'hours'|@translate}
      </label>
    </li>
    <li>
      <label>
        <span class="property">{'Maximum number if photos in each set'|@translate}</span>
        <input type="text" name="max_elements" value="{$batch_download.max_elements}" size="3">
      </label>
    </li>
    <li>
      <label>
        <span class="property">{'Maximum size of each archive'|@translate}</span>
        <input type="text" name="max_size" value="{$batch_download.max_size}" size="4">MB
      </label>
    </li>
  </ul>
</fieldset>

<p><input type="submit" name="save_config" value="{'Save Settings'|@translate}"></p>  
</form>