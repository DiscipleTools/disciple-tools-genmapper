<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.

class DT_Genmapper_Baptisms_Chart extends DT_Genmapper_Metrics_Chart_Base
{

    public $title = 'Baptisms';
    public $slug = 'baptisms'; // lowercase
    public $js_object_name = 'wpApiGenmapper'; // This object will be loaded into the metrics.js file by the wp_localize_script.
    public $js_file_name = 'baptisms.js'; // should be full file name plus extension
    public $deep_link_hash = '#baptisms'; // should be the full hash name. #genmapper_of_hash
    public $permissions = [ 'view_any_contacts', 'view_project_metrics', 'access_contacts' ];

    public function __construct() {
        parent::__construct();
        if ( !$this->has_permission() ){
            return;
        }
        $url_path = dt_get_url_path();

        // only load scripts if exact url
        if ( 'metrics/genmapper/'.$this->slug === $url_path ) {
            add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ], 99 );
        }
        add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
    }


    /**
     * Load scripts for the plugin
     */
    public function scripts() {
        wp_enqueue_style( "hint", "https://cdnjs.cloudflare.com/ajax/libs/hint.css/2.5.1/hint.min.css", [], '2.5.1' );
        wp_enqueue_style( "baptism-styles", trailingslashit( plugin_dir_url( __FILE__ ) ) . "disciples/style.css", [], filemtime( plugin_dir_path( __FILE__ ) . "disciples/style.css" ) );
        wp_enqueue_style( "styles", trailingslashit( plugin_dir_url( __FILE__ ) ) . "style.css", [], filemtime( plugin_dir_path( __FILE__ ) . "style.css" ) );
        wp_register_script( 'd3', 'https://d3js.org/d3.v5.min.js', false, '5' );

        $group_fields = Disciple_Tools_Groups_Post_Type::instance()->get_custom_fields_settings();
        wp_enqueue_script( 'gen-template', trailingslashit( plugin_dir_url( __FILE__ ) ) . "disciples/template.js", [
            'jquery',
            'jquery-ui-core',
            'wp-i18n'
        ], filemtime( plugin_dir_path( __FILE__ ) . "disciples/template.js" ), true );
        wp_localize_script(
            'gen-template', 'genApiTemplate', [
                'plugin_uri' => plugin_dir_url( __DIR__ ),
                'group_fields' => $group_fields
            ]
        );
        wp_enqueue_script( 'genmapper', trailingslashit( plugin_dir_url( __FILE__ ) ) . "genmapper.js", [
            'jquery',
            'jquery-ui-core',
            'd3',
            'gen-template',
            'wp-i18n'
        ], filemtime( plugin_dir_path( __FILE__ ) . "genmapper.js" ), true );
        wp_enqueue_script( 'dt_'.$this->slug.'_script', trailingslashit( plugin_dir_url( __FILE__ ) ) . $this->js_file_name, [
            'jquery',
            'jquery-ui-core',
            'genmapper',
            'wp-i18n',
            'moment'
        ], filemtime( plugin_dir_path( __FILE__ ) .$this->js_file_name ), true );

        // Localize script with array data
        wp_localize_script(
            'dt_'.$this->slug.'_script', $this->js_object_name, [
                'name_key' => $this->slug,
                'root' => esc_url_raw( rest_url() ),
                'plugin_uri' => plugin_dir_url( __DIR__ ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'current_user_login' => wp_get_current_user()->user_login,
                'current_user_id' => get_current_user_id(),
                'spinner' => '<img src="' .trailingslashit( plugin_dir_url( __DIR__ ) ) . 'ajax-loader.gif" style="height:1em;" />',
            ]
        );
    }

    public function add_api_routes() {
        register_rest_route(
            $this->namespace, 'baptisms', [
                [
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => [ $this, 'baptisms' ],
                ],
            ]
        );
    }

    public static function array_to_sql( $values ) {
        foreach ( $values as &$val ) {
            $val = "'" . esc_sql( trim( $val ) ) . "'";
        }

        return implode( ',', $values );
    }

    /**
     * Respond to transfer request of files
     *
     * @param \WP_REST_Request $request
     * @return array|\WP_Error
     */
    public function baptisms( WP_REST_Request $request ) {

        $params = $request->get_params();

        global $wpdb;
        $root_node = [
            "id" => 0,
            "parentId" => "",
            "name" => "source"
        ];
        $prepared_array = [
          $root_node
        ];
        $baptisms_results = dt_queries()->tree( 'multiplying_baptisms_only' );

        //limit multiplier's view to just their tree
        if ( !current_user_can( 'view_any_contacts' ) && !current_user_can( 'view_project_metrics' ) ) {
            $node = [];
            $contact_id = Disciple_Tools_Users::get_contact_for_user( get_current_user_id() );
            foreach ( $baptisms_results as $values ){
                if ( $values["id"] == (string) $contact_id ){
                    $node = $values;
                    $node["parentId"] = 0;
                }
            }
            $baptisms_results = array_merge( [ $node ], $this->get_node_descendants( $baptisms_results, [ $contact_id ] ) );
        }
        if ( !empty( $params["node"] && $params["node"] != "null" ) ){
            $node = [];
            foreach ( $baptisms_results as $res ){
                if ( $res["id"] === $params["node"] ){
                    $node = $res;
                    $node["parent_id"] = 0;
                }
            }
            $baptisms_results = array_merge( [ $node ], $this->get_node_descendants( $baptisms_results, [ $params["node"] ] ) );
        }


        $contact_ids = [];
        $baptisms = [];
        foreach ( $baptisms_results as $baptism ){
            $contact_ids[] = $baptism["id"];
            $baptisms[$baptism["id"]] = [
                "id" => $baptism["id"],
                "parentId" => $baptism["parent_id"] ?? 0,
                "name" => $baptism["name"],
            ];
        }
        if ( empty( $contact_ids ) ){
            return $prepared_array;
        }

        $sql_ids = self::array_to_sql( $contact_ids );

        // phpcs:disable
        // WordPress.WP.PreparedSQL.NotPrepared
        $active_contacts = $wpdb->get_results("
            SELECT pm.post_id 
            FROM $wpdb->postmeta pm
            WHERE pm.post_id IN ($sql_ids) 
            AND pm.meta_key = 'overall_status'
            AND pm.meta_value = 'active'
            GROUP BY post_id
        ", ARRAY_A );
        // phpcs:enable
        foreach ( $active_contacts as $c ){
            $baptisms[$c["post_id"]]["active"] = true;
        }
        // phpcs:disable
        // WordPress.WP.PreparedSQL.NotPrepared
        $baptism_dates = $wpdb->get_results("
            SELECT pm.post_id, pm.meta_value as date
            FROM $wpdb->postmeta pm
            WHERE pm.post_id IN ($sql_ids) 
            AND pm.meta_key = 'baptism_date'
            GROUP BY post_id
        ", ARRAY_A );
        // phpcs:enable
        $active_ids = [];
        foreach ( $baptism_dates as $c ){
            $active_ids[] = $c["post_id"];
            $baptisms[$c["post_id"]]["date"] = dt_format_date( $c["date"] );
        }


        foreach ( $baptisms as $baptism_id => $values ){
            $prepared_array[] = $values;
        }

        if ( empty( $prepared_array ) ) {
            return new WP_Error( 'failed_to_build_data', 'Failed to build data', [ 'status' => 400 ] );
        } else {
            return $prepared_array;
        }
    }

}
