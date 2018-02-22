jQuery(document).ready(function() {
    if('#genmapper' === window.location.hash) {
        show_genmapper()
    }
})

function show_genmapper(){
    "use strict";
    jQuery('#chart').empty().html('<span class="section-header">'+ wpApiGenMapper.translations.genmapper +'</span><hr />')

    genmapper()
}

function genmapper() {
    "use strict";
    jQuery('#chart').append('<div id="genmapper" style="height: 500px;margin: 2.5em 1em;"></div><hr />')

    jQuery.ajax({
        type: "GET",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        url: wpApiGenMapper.root + 'dt/v1/genmapper/groups',
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', wpApiGenMapper.nonce);
        },
    })
        .done(function (data) {

            jQuery('#genmapper').html( JSON.stringify( data ));

        })
        .fail(function (err) {
            console.log("error")
            console.log(err)
            jQuery("#errors").append(err.responseText)
        })
}