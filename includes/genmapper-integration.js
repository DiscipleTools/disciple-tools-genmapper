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
    jQuery('#chart').empty().html('<span class="section-header">'+ wpApiGenMapper.translations.genmapper_groups +'</span><hr />')
    genmapper_groups()
}

function show_genmapper_disciples(){
    "use strict";
    jQuery('#chart').empty().html('<span class="section-header">'+ wpApiGenMapper.translations.genmapper_disciples +'</span><hr />')
    genmapper_disciples()
}

function genmapper_groups() {
    "use strict";
    jQuery('#chart').append('<div id="genmapper_groups" style="height: 500px;margin: 2.5em 1em;"></div><hr />')

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
            let scripts = `
                        <script src="`+ wpApiGenMapper.plugin_uri +`node_modules/d3/build/d3.min.js"></script>
                        <script src="`+ wpApiGenMapper.plugin_uri +`node_modules/i18next/i18next.min.js"></script>
                        <script src="`+ wpApiGenMapper.plugin_uri +`node_modules/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js"></script>
                        <script src="`+ wpApiGenMapper.plugin_uri +`node_modules/lodash/lodash.min.js"></script>
                        <script src="`+ wpApiGenMapper.plugin_uri +`translations.js"></script>
                        <script src="`+ wpApiGenMapper.plugin_uri +`genmapper.js"></script>
                        <script src="`+ wpApiGenMapper.plugin_uri +`node_modules/xlsx/dist/xlsx.core.min.js"></script>`

            jQuery('#genmapper_groups').html( JSON.stringify( data ) + scripts );

        })
        .fail(function (err) {
            console.log("error")
            console.log(err)
            jQuery("#errors").append(err.responseText)
        })
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