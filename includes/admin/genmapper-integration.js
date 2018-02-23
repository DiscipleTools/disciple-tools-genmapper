jQuery(document).ready(function() {
    if('#genmapper_groups' === window.location.hash) {
        show_genmapper_groups()
    }
    if('#genmapper_disciples' === window.location.hash) {
        show_genmapper_disciples()
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