$(document).ready(function () {
  $("#applyAction").on("click", function () {
    console.log("hello world");
    $.confirm({
      title: str_purge,
      buttons: {
        confirm: {
          text: str_yes_purge_confirmation,
          btnClass: 'btn-red',
          action: function () {
            purgeSets();
          },
        },
        cancel: {
          text: str_no_purge_confirmation
        }
      },
      ...jConfirm_confirm_options
    })
  });
});

function purgeSets() {
  var form = $('<form action="' + purge_url + '" method="post">' +
    '<input type="checkbox" name="delete_done" value="1" checked>' +
    '</form>');
  $('body').append(form);
  form.submit();
}