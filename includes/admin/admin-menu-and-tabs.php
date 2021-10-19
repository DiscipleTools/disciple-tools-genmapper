<?php
/**
 * DT_Genmapper_Metrics_Menu class for the admin page
 *
 * @class       DT_Genmapper_Metrics_Menu
 * @version     0.1.0
 * @since       0.1.0
 */


if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

/**
 * Initialize menu class
 */
DT_Genmapper_Metrics_Menu::instance();

/**
 * Class DT_Genmapper_Metrics_Menu
 */
class DT_Genmapper_Metrics_Menu {

    public $token = 'dt_genmapper_plugin';

    private static $_instance = null;

    /**
     * DT_Genmapper_Metrics_Menu Instance
     *
     * Ensures only one instance of DT_Genmapper_Metrics_Menu is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return DT_Genmapper_Metrics_Menu instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()


    /**
     * Constructor function.
     * @access  public
     * @since   0.1.0
     */
    public function __construct() {
        require_once( __DIR__ . '/../icons.php' );

        add_action( "admin_menu", array( $this, "register_menu" ) );
        add_action( 'admin_enqueue_scripts', function() {
            $this->scripts();
        }, 1 );
    } // End __construct()


    /**
     * Loads the subnav page
     * @since 0.1
     */
    public function register_menu() {
        add_submenu_page( 'dt_extensions', __( 'Genmapper', 'dt_genmapper_plugin' ), __( 'Genmapper', 'dt_genmapper_plugin' ), 'manage_dt', $this->token, [ $this, 'content' ] );
    }

    /**
     * Menu stub. Replaced when Disciple Tools Theme fully loads.
     */
    public function extensions_menu() {}

    public function scripts() {
        wp_enqueue_media();
        wp_localize_script( 'wp-api', 'genmapperWPApiShare', array(
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'root' => esc_url_raw( rest_url() ) . 'dt-genmapper'
        ));
        wp_enqueue_script('jquery');
        wp_register_script('jquery-ui','https://code.jquery.com/ui/1.12.1/jquery-ui.min.js',array('jquery'));
        wp_enqueue_script('jquery-ui');
        wp_enqueue_script( 'dt-genmapper-admin', DT_Genmapper_Metrics::path() . 'includes/admin.js', [
            'wp-api',
            'jquery',
            'jquery-ui',
        ], filemtime( DT_Genmapper_Metrics::dir() . 'includes/admin.js' ), true );

        // Localize script with array data\
        wp_localize_script(
            'dt-genmapper-admin', 'wpApiGenmapper', [
                'root' => esc_url_raw( rest_url() ),
                'plugin_uri' => plugin_dir_url( __DIR__ ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'current_user_login' => wp_get_current_user()->user_login,
                'current_user_id' => get_current_user_id(),
            ]
        );
    }


    /**
     * Builds page contents
     * @since 0.1
     */

    public function content() {

        if ( !current_user_can( 'manage_dt' ) ) { // manage dt is a permission that is specific to Disciple Tools and allows admins, strategists and dispatchers into the wp-admin
            wp_die( esc_attr__( 'You do not have sufficient permissions to access this page.' ) );
        }

        status_header(200);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update();
        }

        $iconGroups = DT_Genmapper_Plugin_Icons::instance()->groups();
        include DT_Genmapper_Metrics::includes_dir() . 'template-admin.php';
    }

    /**
     * Make updates before displaing.
     */
    public function update() {
    }
}
