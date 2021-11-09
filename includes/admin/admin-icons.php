<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

/**
 * Initialize menu class
 */
DT_Genmapper_Tab_Icons::instance();

/**
 * Class DT_Genmapper_Metrics_Menu
 */
class DT_Genmapper_Tab_Icons
{
    private static $_instance = null;

    public function __construct() {
        require_once( __DIR__ . '/../icons.php' );
    }

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
    }

    public function content() {
        $icon_groups = DT_Genmapper_Plugin_Icons::instance()->by_group();
        include DT_Genmapper_Metrics::includes_dir() . 'template-admin-icons.php';
    }


    public function update() {
        //Updates handled with AJAX
    }
}
