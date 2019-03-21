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
    public $permissions = [ 'view_any_contacts', 'view_project_metrics' ];

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
        wp_enqueue_style( "hint", "https://cdnjs.cloudflare.com/ajax/libs/hint.css/2.5.1/hint.min.css" );
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
            'wp-i18n'
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

//    private function get_node_descendants( $nodes, $node_ids ){
//        $descendants = [];
//        $children = [];
//        foreach ( $nodes as $node ){
//            if ( in_array( $node["parent_id"], $node_ids ) ){
//                $descendants[] = $node;
//                $children[] = $node["id"];
//            }
//        }
//        if ( sizeof( $children ) > 0 ){
//            $descendants = array_merge( $descendants, $this->get_node_descendants( $nodes, $children ) );
//        }
//        return $descendants;
//    }

    /**
     * Respond to transfer request of files
     *
     * @param \WP_REST_Request $request
     * @return array|\WP_Error
     */
    public function baptisms( WP_REST_Request $request ) {

        $params = $request->get_params();

        global $wpdb;
        $prepared_array = [
            [
                "id" => 0,
                "parentId" => "",
                "name" => "source"
            ]
        ];
        $baptisms = dt_queries()->tree( 'multiplying_baptisms_only' );

        $contact_ids = [];
        foreach ( $baptisms as $b ){
            $contact_ids[] =$b["id"];
        }


        $active_contacts = $wpdb->get_results("
            SELECT pm.post_id 
            FROM $wpdb->postmeta pm
            WHERE pm.meta_key = 'overall_status'
            AND pm.meta_value = 'active'
            GROUP BY post_id
        ", ARRAY_A );
        $active_ids = [];
        foreach ( $active_contacts as $c ){
            $active_ids[] = $c["post_id"];
        }


        foreach ( $baptisms as $baptism ){
            $values = [
                "id" => $baptism["id"],
                "parentId" => $baptism["parent_id"] ?? 0,
                "name" => $baptism["name"],
//                "church" => $baptism["group_type"] === "church",
                "active" => in_array( $baptism["id"], $active_ids )
//                "group_type" => $baptism["group_type"]
            ];
//            if ( isset( $church_health[ $baptism["id"] ] ) ){
//                $health_metrics = explode( ',', $church_health[ $baptism["id"] ] );
//                foreach ( $health_metrics as $health_metric ){
//                    $values[$health_metric] = true;
//                }
//            }
            $prepared_array[] = $values;
        }

        if ( empty( $prepared_array ) ) {
            return new WP_Error( 'failed_to_build_data', 'Failed to build data', [ 'status' => 400 ] );
        } else {
            return $prepared_array;
        }
    }

    public function sample( WP_REST_Request $request ) {
        if ( !$this->has_permission() ){
            return new WP_Error( __METHOD__, 'Missing auth.' );
        }
        $params = $request->get_params();
        if ( isset( $params['button_data'] ) ) {
            // Do something
            $results = $params['button_data'];
            return $results;
        } else {
            return new WP_Error( __METHOD__, 'Missing parameters.' );
        }
    }

}
