(function($) {
  $('.dt_genmapper_upload_image_button').click(function() {
    var send_attachment_bkp = wp.media.editor.send.attachment;
    var button = $(this);
    wp.media.editor.send.attachment = function(props, attachment) {
      $(button).parent().prev().attr('src', attachment.url);
      $(button).prev().val(attachment.id);
      wp.media.editor.send.attachment = send_attachment_bkp;
    }
    wp.media.editor.open(button);
    return false;
  });

// The "Remove" button (remove the value from input type='hidden')
  $('.dt_genmapper_remove_image_button').click(function() {
    var answer = confirm('Are you sure?');
    if (answer == true) {
      var src = $(this).parent().prev().attr('data-src');
      $(this).parent().prev().attr('src', src);
      $(this).prev().prev().val('');
    }
    return false;
  });
})(jQuery);
