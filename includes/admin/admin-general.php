<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

/**
 * Initialize menu class
 */
DT_Genmapper_Tab_General::instance();

/**
 * Class DT_Genmapper_Metrics_Menu
 */
class DT_Genmapper_Tab_General
{
    private static $_instance = null;


    /**
     * DT_Genmapper_Metrics_Menu Instance
     *
     * Ensures only one instance of DT_Genmapper_Metrics_Menu is loaded or can be loaded.
     *
     * @return DT_Genmapper_Metrics_Menu instance
     * @since 0.1.0
     * @static
     */
    public static function instance() {
        if (is_null( self::$_instance )) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } //

    public function content() {
        $show_health_icons = get_option( 'dt_genmapper_show_health_icons', true );
        $show_health_metrics = get_option( 'dt_genmapper_show_health_metrics', true );
        $collapse_health_metrics_fields = get_option( 'dt_genmapper_collapse_metrics', true );
        $nonce = wp_create_nonce( static::class );
        include DT_Genmapper_Metrics::includes_dir() . 'template-admin-general.php';
    }

    public function update() {
        if ( !wp_verify_nonce( sanitize_key( $_POST['field_add_nonce'] ), static::class ) ) {
            return;
        }
        update_option( 'dt_genmapper_show_health_icons', !empty( $_POST["dt_genmapper_show_health_icons"] ) );
        update_option( 'dt_genmapper_show_health_metrics', !empty( $_POST["dt_genmapper_show_health_metrics"] ) );
        update_option( 'dt_genmapper_collapse_metrics', !empty( $_POST["dt_genmapper_collapse_metrics"] ) );
    }
}
