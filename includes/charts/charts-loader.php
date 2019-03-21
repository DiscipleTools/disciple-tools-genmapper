<?php

class DT_Genmapper_Metrics_Charts
{
    private static $_instance = null;
    public static function instance(){
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct(){
        // Load required files
        require_once( 'charts-base.php' );
        require_once( 'one-page-chart-template.php' );
        require_once( 'baptisms.php' );

        new DT_Genmapper_Metrics_Chart();
        new DT_Genmapper_Baptisms_Chart();

    } // End __construct
}
DT_Genmapper_Metrics_Charts::instance();
