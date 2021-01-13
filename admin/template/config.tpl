{combine_css path=$BATCH_DOWNLOAD_PATH|cat:"admin/template/style.css"}

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

$("input[name='max_elements']").change(function() {
  $("#max_elements").slider("value", $(this).val());
});

jQuery(".showInfo").tipTip({
  delay: 0,
  fadeIn: 200,
  fadeOut: 200,
  maxWidth: '300px',
  defaultPosition: 'bottom'
});

$(".show_advanced").click(function() {
  $(this).slideUp();
  $(".advanced").slideDown();
  return false;
});

$('input[name="multisize"]').on('change', function() {
  if ($(this).val() == 'true') {
    $('#multisize_title').text('{'Maximum photo size'|translate|escape:javascript}');
  }
  else {
    $('#multisize_title').text('{'Photo size'|translate|escape:javascript}');
  }
})
.filter(':checked').trigger('change');
{/footer_script}


<div class="titrePage">
	<h2>Batch Downloader</h2>
</div>

<form method="post" action="" class="properties">
<fieldset>
  <legend><span class="icon-lock icon-red"></span>{'Download permissions'|translate}</legend>

  <ul>
    <li>
      <i>{'Warning: Only registered users can use Batch Downloader.'|translate}</i>
      <br>
    {if $DOWNLOAD_PERM_LOADED}
      <i>{'%s plugin detected, albums will be downloadable according to permissions.'|translate:'<b>Download Permissions</b>'}</i>
      <br>
    {/if}
      <br>
    </li>
    <li>
      <label>
      {if $group_options}
        <b>{'User groups'|translate}</b>
        <select name="groups[]" data-placeholder="{'Everybody'|translate}" class="chzn-select" multiple="multiple" style="width:370px;">
          {html_options options=$group_options selected=$batch_download.groups}
        </select>
      {else}
        {'There is no group in this gallery.'|translate} <a href="admin.php?page=group_list">{'Group management'|translate}</a>
      {/if}
      </label>
    </li>
    <li>
      <label>
        <b>{'Privacy level'|translate}</b>
        <select name="level">
          {html_options options=$level_options selected=$batch_download.level}
        </select>
      </label>
    </li>
    <li>
      <b>{'What can be downloaded?'|translate}</b>
      <label><input type="checkbox" name="what[categories]" {if in_array('categories',$batch_download.what)}checked="checked"{/if}/> {'Albums'|translate}</label>
      {if $USER_COLLEC_LOADED}<label><input type="checkbox" name="what[collections]" {if in_array('collections',$batch_download.what)}checked="checked"{/if}/> {'Collections'|translate}</label>{/if}
      <label><input type="checkbox" name="what[specials]" {if in_array('specials',$batch_download.what)}checked="checked"{/if}/> {'Specials'|translate}</label>
      <a class="icon-info-circled-1 showInfo" title="{'Most visited'|translate}, {'Random photos'|translate}, {'Best rated'|translate}..."></a>
    </li>
    <li>
      <b>{'Photo size choices'|translate}</b>
      <label><input type="radio" name="multisize" value="true" {if $batch_download.multisize}checked{/if}> {'Any size'|translate}</label>
      <label><input type="radio" name="multisize" value="false" {if !$batch_download.multisize}checked{/if}> {'One size'|translate}</label>

      <label>
        <b id="multisize_title">{'Maximum photo size'|translate}</b>
        <select name="photo_size">
          {html_options options=$sizes_options selected=$batch_download.photo_size}
        </select>
      </label>
    </li>
  </ul>
</fieldset>

<fieldset>
  <legend><span class="icon-download icon-yellow"></span>{'Archives'|translate}</legend>

  <ul>
    <li>
      <label>
        <b>{'Delete downloads after'|translate}</b>
        <input type="text" name="archive_timeout" value="{$batch_download.archive_timeout}" size="3"> {'hours'|translate}
      </label>
    </li>
    <li>
      <label>
        <b>{'Maximum number of photos per download set'|translate}</b>
        <div id="max_elements"></div>
        <input type="text" name="max_elements" value="{$batch_download.max_elements}" size="5">
      </label>
    </li>
    <li>
      <label>
        <b>{'Maximum size of each archive (in Megabytes)'|translate}</b>
        <div id="max_size"></div>
        <input type="text" name="max_size" value="{$batch_download.max_size}" size="5">
      </label>
    </li>
    <li>
      <label>
        <b>{'Archive prefix'|translate}</b>
        <input type="text" name="archive_prefix" value="{$batch_download.archive_prefix}">
      </label>
    </li>
    <li>
      <label>
        <input type="checkbox" name="one_archive" {if $batch_download.one_archive}checked{/if}>
        <b>{'Delete previous archive when starting to download another one'|translate}</b>
      </label>
      <a class="icon-info-circled-1 showInfo" title="{'It saves space on the server but doesn\'t allow to restart failed downloads.'|translate}"></a>
    </li>
    <li>
      <label>
        <input type="checkbox" name="force_pclzip" {if $batch_download.force_pclzip}checked{/if}>
        <b>{'Force the usage of PclZip instead of ZipArchive as ZIP library'|translate}</b>
      </label>
      <a class="icon-info-circled-1 showInfo" title="{'Only check if you are experiencing corrupted archives with ZipArchive.'|translate}"></a>
    </li>
    <li>
      <label>
        <input type="checkbox" name="direct" {if $batch_download.direct}checked{/if}>
        <b>{'Don\'t download archives through PHP'|translate}</b>
      </label>
      <a class="icon-info-circled-1 showInfo" title="{'Only check if your host complains about high PHP usage.'|translate}"></a>
    </li>
  {if $use_ziparchive}
    <li>
      <label>
        <b>{'Archive comment'|translate} :</b><br>
        <textarea name="archive_comment" rows="5" style="width:450px;">{$batch_download_comment}</textarea>
      </label>
      <br>
      <i>{'Warning: ZipArchive doesn\'t accept special characters like accentuated ones, angle quotes (») and non-latin alphabets.'|translate}</i>
    </li>
  {else}
    <input type="hidden" name="archive_comment" value="">
  {/if}
  </ul>
</fieldset>

<p class="formButtons"><input type="submit" name="save_config" value="{'Save Settings'|translate}"></p>

<fieldset>
  <legend><span class="icon-info-circled-1 icon-green"></span>{'Environment'|translate}</legend>

  <b>PHP</b> {$PHP_VERSION}<br>
{if $use_ziparchive}
  <b>ZipArchive</b> {$PHP_VERSION}
{else}
  <b>PclZip</b> 2.8.2
{/if}
</fieldset>

<fieldset>
  <legend><span class="icon-cog icon-blue"></span>{'Advanced features'|translate}</legend>

  <a href="#" class="show_advanced icon-eye">{'show details'|translate}</a>

  <dl class="advanced" style="display:none;">
    {$ADVANCED_CONF}
  </dl>
</fieldset>

</form>