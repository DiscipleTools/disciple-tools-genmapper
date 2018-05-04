<?php
/**
 * DT_Genmapper_UI
 *
 * @class DT_Genmapper_UI
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Initialize instance
 */
DT_Genmapper_UI::instance();

/**
 * Class DT_Genmapper
 */
class DT_Genmapper_UI
{
    /**
     * DT_Genmapper_UI The single instance of DT_Genmapper_UI.
     *
     * @var     object
     * @access    private
     * @since     0.1.0
     */
    private static $_instance = null;

    /**
     * Main DT_Genmapper_UI Instance
     * Ensures only one instance of DT_Genmapper_UI is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return DT_Genmapper_UI instance
     */
    public static function instance()
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     *
     * @access  public
     * @since   0.1.0
     */
    public function __construct()
    {
        add_filter( 'dt_metrics_menu', [ $this, 'metrics_menu' ], 99 );
        add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ], 999 );
    }

    /**
     * This filter adds a menu item to the metrics
     *
     * @param $content
     *
     * @return string
     */
    public function metrics_menu( $content ) {
        $content .= '<li><a href="'. site_url( '/metrics/' ) .'#genmapper_groups" onclick="show_genmapper_groups()">' .  esc_html__( 'GenMapper', 'dt_genmapper' ) . '</a>
                        <ul class="menu vertical nested">
                          <li><a href="'. site_url( '/metrics/' ) .'#genmapper_groups" onclick="show_genmapper_groups()">' .  esc_html__( 'Groups', 'dt_genmapper' ) . '</a></li>
                          <li><a href="'. site_url( '/metrics/' ) .'#genmapper_disciples" onclick="show_genmapper_disciples()">' .  esc_html__( 'Disciples', 'dt_genmapper' ) . '</a></li>
                        </ul>
                      </li>';
        return $content;
    }

    /**
     * Load scripts for the plugin
     */
    public function scripts() {
        $url_path = trim( parse_url( add_query_arg( array() ), PHP_URL_PATH ), '/' );

        if ( 'metrics' === $url_path ) {
            wp_enqueue_script( 'dt_genmapper_script', dt_genmapper()->admin_uri . 'genmapper-integration.js', [
                'jquery',
                'jquery-ui-core',
            ], filemtime( dt_genmapper()->admin_path . 'genmapper-integration.js' ), true );

            wp_localize_script(
                'dt_genmapper_script', 'wpApiGenMapper', [
                    'root' => esc_url_raw( rest_url() ),
                    'plugin_uri' => site_url() . '/wp-content/plugins/disciple-tools-genmapper/',
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                    'current_user_login' => wp_get_current_user()->user_login,
                    'current_user_id' => get_current_user_id(),
                    'translations' => [
                        "genmapper_groups" => __( "GenMapper Groups", "dt_genmapper" ),
                        "genmapper_disciples" => __( "GenMapper Disciples", "dt_genmapper" ),
                    ]
                ]
            );
        }
    }

}