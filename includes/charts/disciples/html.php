<?php
include '../pre-load.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GenMapper</title>

    <?php dt_genmapper_head() ?>

    <script>

        function genmapper_disciples() {
            "use strict";
            let nonce = '<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ) ?>';
            let root = '<?php echo esc_url_raw( rest_url() ) ?>';
            jQuery(document).ready(function() {
                return jQuery.ajax({
                    type: "GET",
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    url: root + 'dt/v1/genmapper/disciples',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', nonce);
                    },
                })
                    .fail(function (err) {
                        console.log("error")
                        console.log(err)
                        jQuery("#alert-message").append(err.responseText)
                    })
            })
        }

    </script>

</head>
<body>
<div id="content">
  <aside id="left-menu">
  </aside>

  <section id="intro" class="intro">
    <div id="intro-content"></div>
  </section>

  <section id="alert-message" class="alert-message">
  </section>

  <section id="edit-group" class="edit-group">
  </section>

  <section id="genmapper-graph">
    <svg id="genmapper-graph-svg" width="100%"></svg>
  </section>
</div>

<?php dt_genmapper_footer() ?>

<script>
  const jsonString = '[{"id":0,"parentId":"","name":"Barnabas","date":"30","believer":true,"baptized":true,"word":true,"prayer":true,"field1":true,"field2":true,"field3":true,"field4":true,"field5":true,"discipleType":"facilitatesChurch","timothy":true,"active":true},{"id":1,"parentId":0,"name":"Paul","date":"40","believer":true,"baptized":true,"word":true,"prayer":true,"field1":true,"field2":true,"field3":true,"field4":true,"field5":true,"discipleType":"facilitatesChurch","timothy":true,"active":true},{"id":2,"parentId":1,"name":"Timothy","date":"","believer":true,"baptized":true,"word":true,"prayer":true,"field1":true,"field2":true,"field3":true,"field4":true,"field5":true,"discipleType":"facilitatesGroup","timothy":true,"active":true},{"id":3,"parentId":1,"name":"Titus","date":"","believer":true,"baptized":true,"word":true,"prayer":true,"field1":false,"field2":false,"field3":false,"field4":false,"field5":false,"discipleType":"individual","timothy":false,"active":true}]'
  genmapper.importJSON(jsonString)
</script>

</body>
</html>
