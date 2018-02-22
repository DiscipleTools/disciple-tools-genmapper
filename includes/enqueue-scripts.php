<?php
/**
 * Loads scripts and styles for the webform admin page.
 */
function dt_admin_genmapper_scripts()
{
    global $pagenow;

    if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'dt_genmapper' == sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {

        wp_enqueue_script( 'dt_genmapper_admin_script', dt_genmapper()->includes_uri . 'admin.js', [
            'jquery',
            'jquery-ui-core',
        ], filemtime( dt_genmapper()->includes_path . 'admin.js' ), true );

        wp_register_style( 'dt_genmapper_admin_css', dt_genmapper()->includes_uri . 'admin.css', [], filemtime( dt_genmapper()->includes_path . 'admin.css' ) );
        wp_enqueue_style( 'dt_genmapper_admin_css' );

    }
}
add_action( 'admin_enqueue_scripts', 'dt_admin_genmapper_scripts' );
