{combine_css path=$BATCH_DOWNLOAD_PATH|@cat:"admin/template/style.css"}

{combine_script id='jquery.ui.slider' require='jquery.ui' load='footer' path='themes/default/js/ui/jquery.ui.slider.js'}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}
{combine_script id='jquery.chosen' load='footer' path='themes/default/js/plugins/chosen.jquery.min.js'}
{combine_css path="themes/default/js/plugins/chosen.css"}

{footer_script}
jQuery(".chzn-select").chosen();

$("#max_size").slider({ldelim}
  range: "min",
  value: {$batch_download.max_size},
  min: 10,
  max: {$batch_download.max_size_value},
  slide: function(event, ui) {ldelim}
    $("input[name='max_size']").val(ui.value);
  }
});
$("input[name='max_size']").change(function() {ldelim}
  $("#max_size").slider("value", $(this).val());
});

$("#max_elements").slider({ldelim}
  range: "min",
  value: {$batch_download.max_elements},
  min: 10,
  max: {$batch_download.max_elements_value},
  slide: function(event, ui) {ldelim}
    $("input[name='max_elements']").val(ui.value);
  }
});
$("input[name='max_elements']").change(function() {ldelim}
  $("#max_elements").slider("value", $(this).val());
});
{/footer_script}

<div class="titrePage">
	<h2>Batch Downloader</h2>
</div>

<form method="post" action="" class="properties">
<fieldset>
  <legend>{'Download permissions'|@translate}</legend>
  
  <ul>
    <li>
      <label>
      {if $group_options}
        <span class="property">{'User groups'|@translate}</span>
        <select name="groups[]" data-placeholder="{'Everybody'|@translate}" class="chzn-select" multiple="multiple" style="width:370px;">
          {html_options options=$group_options selected=$batch_download.groups}
        </select>
      {else}
        {'There is no group in this gallery.'|@translate} <a href="admin.php?page=group_list">{'Group management'|@translate}</a>
      {/if}
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
  <legend>{'Archives'|@translate}</legend>
  
  <ul>
    <li style="display:none;">
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
        <span class="property">{'Delete downloads after'|@translate}</span>
        <input type="text" name="archive_timeout" value="{$batch_download.archive_timeout}" size="3"> {'hours'|@translate}
      </label>
    </li>
    <li>
      <label>
        <span class="property">{'Maximum number of photos per download set'|@translate}</span>
        <div id="max_elements""></div>
        <input type="text" name="max_elements" value="{$batch_download.max_elements}" size="5">
      </label>
    </li>
    <li>
      <label>
        <span class="property">{'Maximum size of each archive (in Megabytes)'|@translate}</span>
        <div id="max_size"></div>
        <input type="text" name="max_size" value="{$batch_download.max_size}" size="5">
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
        <span class="property">{'Archive comment'|@translate} :</span><br>
        <textarea name="archive_comment" rows="5" style="width:450px;">{$batch_download.archive_comment}</textarea>
      </label>
    </li>
  </ul>
</fieldset>

<p class="formButtons"><input type="submit" name="save_config" value="{'Save Settings'|@translate}"></p>  
</form>