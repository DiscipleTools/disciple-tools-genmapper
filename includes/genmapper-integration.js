jQuery(document).ready(function() {
    if('#genmapper_groups' === window.location.hash) {
        show_genmapper_groups()
    }
    if('#genmapper_disciples' === window.location.hash) {
        show_genmapper_disciples()
    }
    if('#genmapper_four_fields' === window.location.hash) {
        show_genmapper_four_fields()
    }
})

function show_genmapper_groups(){
    "use strict";
    let height = jQuery(window).height() - jQuery('header').height() - 150

    jQuery('#chart').empty().html('<span class="section-header">'+ wpApiGenMapper.translations.genmapper_groups +'</span><hr />' +
        '<iframe src="'+wpApiGenMapper.plugin_uri+'includes/church-circles/html.php" width="100%" height="'+height+'px" frameborder="0" ></iframe>')
}

function show_genmapper_disciples(){
    "use strict";
    let height = jQuery(window).height() - jQuery('header').height() - 150

    jQuery('#chart').empty().html('<span class="section-header">'+ wpApiGenMapper.translations.genmapper_disciples +'</span><hr />' +
        '<iframe src="'+wpApiGenMapper.plugin_uri+'includes/disciples/html.php" width="100%" height="'+height+'px" frameborder="0" ></iframe>')
}

function show_genmapper_four_fields(){
    "use strict";
    let height = jQuery(window).height() - jQuery('header').height() - 150

    jQuery('#chart').empty().html('<span class="section-header">'+ wpApiGenMapper.translations.genmapper_four_fields +'</span><hr />' +
        '<iframe src="'+wpApiGenMapper.plugin_uri+'includes/four-fields/html.php" width="100%" height="'+height+'px" frameborder="0" ></iframe>')

}






















function genmapper_disciples() {
    "use strict";
    jQuery('#chart').append('<div id="genmapper_disciples" style="height: 500px;margin: 2.5em 1em;"></div><hr />')

    jQuery.ajax({
        type: "GET",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        url: wpApiGenMapper.root + 'dt/v1/genmapper/disciples',
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', wpApiGenMapper.nonce);
        },
    })
        .done(function (data) {

            jQuery('#genmapper_disciples').html( JSON.stringify( data ));

        })
        .fail(function (err) {
            console.log("error")
            console.log(err)
            jQuery("#errors").append(err.responseText)
        })
}