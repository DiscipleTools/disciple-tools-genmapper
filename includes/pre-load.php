<?php
/**
 * Pre-load.php is a shared security and system loading element.
 * 1. It loads the WP framework
 * 2. Checks if user is logged in
 * 3. Checks if Disciple-Tools is installed
 * 4. Checks if the user has permission to see the generation mapping
 * 5. Enqueue's required scripts
 */

if ( ! isset( $_SERVER['DOCUMENT_ROOT'] ) ) {
    die( 'missing server info' );
}
// Load wp framework
// @codingStandardsIgnoreLine
require( $_SERVER[ 'DOCUMENT_ROOT' ] . '/wp-load.php' ); // loads the wp framework

// Check if user is logged on
if( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url() );
}

// Check if Disciple Tools is installed and active
$current_theme = get_option( 'current_theme' );
if ( 'Disciple Tools' != $current_theme ) {
    die( 'Disciple Tools not installed' );
}

function genmapper_uri() {
    return site_url() . '/wp-content/plugins/disciple-tools-genmapper/';
}

// Load header scripts
function genmapper_head() {
    ?>
    <link rel="stylesheet" type="text/css" href="<?php echo esc_attr( genmapper_uri() ) ?>includes/style.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo  esc_attr( genmapper_uri() ) ?>includes/hint.min.css">
    <link rel="icon" type="image/png" href="<?php echo  esc_attr( genmapper_uri() ) ?>includes/favicon.png">
    <script src="<?php echo esc_attr( genmapper_uri() ) ?>node_modules/jquery/dist/jquery.min.js"></script>
    <?php
}

// Load footer scripts
function genmapper_footer() {
    ?>

    <script src="template.js"></script>
    <script src="<?php echo esc_attr( genmapper_uri() ) ?>node_modules/d3/build/d3.min.js"></script>
    <script src="<?php echo esc_attr( genmapper_uri() ) ?>node_modules/i18next/i18next.min.js"></script>
    <script src="<?php echo esc_attr( genmapper_uri() ) ?>node_modules/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js"></script>
    <script src="<?php echo esc_attr( genmapper_uri() ) ?>node_modules/lodash/lodash.min.js"></script>
    <script src="<?php echo esc_attr( genmapper_uri() ) ?>includes/translations.js"></script>
    <script src="<?php echo esc_attr( genmapper_uri() ) ?>includes/genmapper.js"></script>
    <script src="<?php echo esc_attr( genmapper_uri() ) ?>includes/FileSaver.min.js"></script>
    <script src="<?php echo esc_attr( genmapper_uri() ) ?>node_modules/xlsx/dist/xlsx.core.min.js"></script>
    <?php
}