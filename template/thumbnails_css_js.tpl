{html_style}{literal}
#thumbnails li { position:relative !important;display:inline-block; }
li .removeSet { width:100%;height:16px;display:none;position:absolute;top:0;background:rgba(0,0,0,0.8);padding:2px;border-radius:2px;font-size:10px;z-index:100 !important;color:#eee;white-space:nowrap; }
li:hover .removeSet { display:block !important; }
{/literal}{/html_style}

{footer_script require='jquery'}
jQuery(".removeSet").click(function() {
  var toggle_id = jQuery(this).data("id");
  var $trigger = jQuery(this);

  jQuery.ajax({
    type: "POST",
    url: "{$ROOT_URL}index.php",
    data: {
      action: "bd_remove_image",
      set_id: "{$SET_ID}",
      toggle_id: toggle_id
    },
    success: function(msg) {
      if (msg == "ok") {
        $trigger.parent("li").hide("fast", function() {
          jQuery(this).remove();
          if (typeof GThumb != "undefined") GThumb.build();
        });

        jQuery(".nbImagesSet").html(parseInt(jQuery(".nbImagesSet").html()) -1);
      }
      else {
        $trigger.html('{'Un unknown error occured'|translate}');
      }
    },
    error: function() {
      $trigger.html('{'Un unknown error occured'|translate}');
    }
  });

  return false;
});
{/footer_script}