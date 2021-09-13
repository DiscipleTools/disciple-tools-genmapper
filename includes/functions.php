<?php
/**
 * Functions class
 */
class DT_Genmapper_Plugin_Functions
{
    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct() {
        add_filter( 'desktop_navbar_menu_options', [ $this, 'nav_menu' ], 10, 1 );
        add_filter( 'off_canvas_menu_options', [ $this, 'nav_menu' ] );
    }

    public function nav_menu( $tabs ){
        $tabs['genmapper'] = [
            "link" => site_url( '/genmapper/' ),
            "label" => __( "Genmapper", "disciple-tools-genmapper" )
        ];
        return $tabs;
    }
}
