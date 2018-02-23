<?php
include '../pre-load.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GenMapper</title>
    <link rel="stylesheet" type="text/css" href="../../vendor/gen-mapper/style.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="../../vendor/gen-mapper/hint.min.css">
    <link rel="icon" type="image/png" href="../../vendor/gen-mapper/favicon.png">
    <?php do_action( 'genmapper_head' ) ?>
</head>
<body>
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
<script src="template.js"></script>
<script src="../../vendor/gen-mapper/d3.min.js"></script>
<script src="../../vendor/gen-mapper/i18next.min.js"></script>
<script src="../../vendor/gen-mapper/i18nextBrowserLanguageDetector.min.js"></script>
<script src="../../vendor/gen-mapper/lodash.min.js"></script>
<script src="../../vendor/gen-mapper/translations.js"></script>
<script src="../../vendor/gen-mapper/genmapper.js"></script>
<script src="../../vendor/gen-mapper/FileSaver.min.js"></script>
<script src="../../vendor/gen-mapper/xlsx.core.min.js"></script>

<?php do_action( 'genmapper_footer' ) ?>
</body>
</html>