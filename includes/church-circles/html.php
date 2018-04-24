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
        function genmapper_groups() {
            "use strict";
            let nonce = '<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ) ?>';
            let root = '<?php echo esc_url_raw( rest_url() ) ?>';
            jQuery(document).ready(function() {
                return jQuery.ajax({
                    type: "GET",
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    url: root + 'dt/v1/genmapper/groups',
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
  const jsonString = '[{"id":0,"parentId":"","name":"John","email":"","peopleGroup":"","attenders":"5","believers":"5","baptized":"3","newlyBaptized":"1","church":true,"churchType":"legacy","elementBaptism":true,"elementWord":true,"elementPrayer":true,"elementLordsSupper":true,"elementGive":true,"elementLove":true,"elementWorship":true,"elementLeaders":true,"elementMakeDisciples":true,"place":"New York","date":"9-13","threeThirds":"1234567","active":true},{"id":2,"parentId":0,"name":"Joe","email":"","peopleGroup":"","attenders":"7","believers":"6","baptized":"3","newlyBaptized":"3","church":true,"churchType":"existingBelievers","elementBaptism":true,"elementWord":true,"elementPrayer":true,"elementLordsSupper":false,"elementGive":false,"elementLove":true,"elementWorship":true,"elementLeaders":true,"elementMakeDisciples":true,"place":"Phoenix","date":"12-13","threeThirds":"123456","active":true},{"id":1,"parentId":0,"name":"Jack","email":"","peopleGroup":"","attenders":"5","believers":"1","baptized":"1","newlyBaptized":"0","church":false,"churchType":"newBelievers","elementBaptism":false,"elementWord":true,"elementPrayer":true,"elementLordsSupper":false,"elementGive":false,"elementLove":true,"elementWorship":false,"elementLeaders":false,"elementMakeDisciples":false,"place":"Phoenix","date":"1-14","threeThirds":"13456","active":true},{"id":4,"parentId":2,"name":"Joanna","email":"","peopleGroup":"","attenders":"4","believers":"2","baptized":"0","newlyBaptized":"0","church":false,"churchType":"newBelievers","elementBaptism":false,"elementWord":true,"elementPrayer":true,"elementLordsSupper":true,"elementGive":false,"elementLove":true,"elementWorship":true,"elementLeaders":false,"elementMakeDisciples":false,"place":"Orlando","date":"5-14","threeThirds":"123456","active":true},{"id":3,"parentId":2,"name":"Jasmine","email":"","peopleGroup":"","attenders":"6","believers":"3","baptized":"1","newlyBaptized":"0","church":false,"churchType":"newBelievers","elementBaptism":true,"elementWord":true,"elementPrayer":true,"elementLordsSupper":false,"elementGive":false,"elementLove":true,"elementWorship":false,"elementLeaders":true,"elementMakeDisciples":false,"place":"Berlin","date":"7-14","threeThirds":"1356","active":false}]'
  genmapper.importJSON(jsonString)
</script>

</body>
</html>