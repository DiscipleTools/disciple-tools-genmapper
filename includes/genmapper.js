function show_genmapper(){
    "use strict";
    jQuery('#chart').empty().html('<span class="section-header">'+ wpApiGenMapper.translations.follow_up +'</span><hr />')

    genmapper()
}

function genmapper() {
    "use strict";
    jQuery('#chart').append('<div id="critical-path-fup" style="height: 500px;margin: 2.5em 1em;"></div><hr />')

    jQuery.ajax({
        type: "GET",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        url: wpApiMetricsPage.root + 'dt/v1/metrics/critical_path_fup',
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', wpApiMetricsPage.nonce);
        },
    })
        .done(function (data) {

            google.charts.load('current', {packages: ['corechart', 'bar']});
            google.charts.setOnLoadCallback(function() {
                "use strict";
                let chartData = google.visualization.arrayToDataTable(data);

                let options = {
                    bars: 'horizontal',

                };

                let chart = new google.charts.Bar(document.getElementById('critical-path-fup'));
                chart.draw(chartData, options);
            });

        })
        .fail(function (err) {
            console.log("error")
            console.log(err)
            jQuery("#errors").append(err.responseText)
        })
}