<?php
/**
 * DT_Webform_Home_Endpoints
 *
 * @class      DT_Webform_Home_Endpoints
 * @since      0.1.0
 * @package    DT_Webform
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Initialize instance
 */
DT_Genmapper_Endpoints::instance();

/**
 * Class DT_Genmapper_Endpoints
 */
class DT_Genmapper_Endpoints
{
    /**
     * DT_Genmapper_Endpoints The single instance of DT_Genmapper_Endpoints.
     *
     * @var     object
     * @access    private
     * @since     0.1.0
     */
    private static $_instance = null;

    /**
     * Main DT_Genmapper_Endpoints Instance
     * Ensures only one instance of DT_Genmapper_Endpoints is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return DT_Genmapper_Endpoints instance
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
        add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
    } // End __construct()

    public function add_api_routes()
    {
        $version = '1';
        $namespace = 'dt/v' . $version;
//        $public_namespace = 'dt-public/v' . $version; // @todo prepared for potential public calls. Remove if unnecissary.
        $branch = '/genmapper/';

        register_rest_route(
            $namespace, $branch . 'groups', [
                [
                'methods'  => WP_REST_Server::READABLE,
                'callback' => [ $this, 'groups' ],
                ],
            ]
        );
    }

    /**
     * Respond to transfer request of files
     *
     * @param \WP_REST_Request $request
     * @return array|\WP_Error
     */
    public function groups( WP_REST_Request $request ) {

        $params = $request->get_params();

        $prepared_array = [
            [
                'id' => '1',
                'parent_id' => '',
            ],
            [
                'id' => '2',
                'parent_id' => '1',
            ],
            [
                'id' => '3',
                'parent_id' => '2',
            ],
            [
                'id' => '4',
                'parent_id' => '2',
            ]
        ];

        if ( empty( $prepared_array ) ) {
            return new WP_Error( 'failed_to_build_data', 'Failed to build data', [ 'status' => 400 ] );
        } else {
            return $prepared_array;
        }
    }
}