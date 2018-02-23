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

function genmapper_head() {

}

function genmapper_footer() {

}