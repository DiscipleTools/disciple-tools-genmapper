<?php
include '../pre-load.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GenMapper</title>
    <?php genmapper_head() ?>

    <script>
        function genmapper_groups() {
            "use strict";
            let nonce = '<?php echo wp_create_nonce( 'wp_rest' ) ?>';
            let root = '<?php echo esc_url_raw( rest_url() ) ?>';
            return jQuery.ajax({
                type: "GET",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                url: root + 'dt/v1/genmapper/groups',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', nonce);
                },
            })
                .done(function (data) {

                    const groups = data // @todo just an idea for passing the returned data

                })
                .fail(function (err) {
                    console.log("error")
                    console.log(err)
                    jQuery("#alert-message").append(err.responseText)
                })
        }
    </script>
</head>
<body>
<div><script>console.log( genmapper_groups() )</script></div>
<div id="content">
    <aside id="left-menu">
    </aside>

    <section id="intro">
        <div id="intro-content"></div>
    </section>

    <section id="alert-message">
    </section>

    <section id="edit-group">
    </section>

    <section id="main">
        <svg id="main-svg" width="100%"></svg>
    </section>
</div>

<?php genmapper_footer() ?>

</body>
</html>