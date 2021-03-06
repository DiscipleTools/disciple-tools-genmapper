<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.

class DT_Genmapper_Groups_Chart extends DT_Genmapper_Metrics_Chart_Base
{

    public $title = 'Groups';
    public $slug = 'groups'; // lowercase
    public $js_object_name = 'wpApiGenmapper'; // This object will be loaded into the metrics.js file by the wp_localize_script.
    public $js_file_name = 'groups.js'; // should be full file name plus extension
    public $deep_link_hash = '#groups'; // should be the full hash name. #genmapper_of_hash
    public $permissions = [ 'dt_all_access_contacts', 'view_project_metrics' ];

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
        wp_enqueue_style( "hint", "https://cdnjs.cloudflare.com/ajax/libs/hint.css/2.5.1/hint.min.css", [], "2.5.1" );
        wp_enqueue_style( "group-styles", trailingslashit( plugin_dir_url( __FILE__ ) ) . "church-circles/style.css", [], filemtime( plugin_dir_path( __FILE__ ) . "church-circles/style.css" ) );
        wp_enqueue_style( "styles", trailingslashit( plugin_dir_url( __FILE__ ) ) . "style.css", [], filemtime( plugin_dir_path( __FILE__ ) . "style.css" ) );
        wp_register_script( 'd3', 'https://d3js.org/d3.v5.min.js', false, '5' );

        $group_fields = DT_Posts::get_post_field_settings( "groups" );
        wp_enqueue_script( 'gen-template', trailingslashit( plugin_dir_url( __FILE__ ) ) . "church-circles/template.js", [
            'jquery',
            'jquery-ui-core',
            'wp-i18n'
        ], filemtime( plugin_dir_path( __FILE__ ) . "church-circles/template.js" ), true );
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
                'translation' => [
                    'string1' => __( 'Group Generation Tree', 'disciple-tools-genmapper' ),
                    'string2' => __( 'This tree only shows First Generation groups that have multiplied.', 'disciple-tools-genmapper' ),
                    'string3' => __( 'See descendants of a specific group', 'disciple-tools-genmapper' ),
                    'string4' => __( 'Reset', 'disciple-tools-genmapper' ),
                ]
            ]
        );
    }

    //${localizedObject.translation.string /**/}

    public function add_api_routes() {
        register_rest_route(
            $this->namespace, 'groups', [
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
     * @param WP_REST_Request $request
     * @return array|WP_Error
     */
    public function groups( WP_REST_Request $request ) {

        $params = $request->get_params();

        global $wpdb;
        $prepared_array = [
            [
                "id" => 0,
                "parentId" => "",
                "name" => "source"
            ]
        ];
        $groups = dt_queries()->tree( 'multiplying_groups_only' );
        if ( is_wp_error( $groups )){
            return $groups;
        }

        if ( !empty( $params["node"] && $params["node"] != "null" ) ){
            $node = [];
            foreach ( $groups as $group ){
                if ( $group["id"] === $params["node"] ){
                    $prepared_array = [];
                    $node = $group;
                    $node["parent_id"] = "";
                }
            }
            $groups = array_merge( [ $node ], $this->get_node_descendants( $groups, [ $params["node"] ] ) );
        }

        foreach ( $groups as $group ){
            $values = [
                "id" => $group["id"],
                "parentId" => $group["parent_id"] ?? 0,
                "name" => $group["name"],
                "church" => $group["group_type"] === "church",
                "active" => $group["group_status"] === "active",
                "group_type" => $group["group_type"],
                "post_type" => "groups"
            ];
            $prepared_array[] = $values;
        }

        if ( empty( $prepared_array ) ) {
            return new WP_Error( 'failed_to_build_data', 'Failed to build data', [ 'status' => 400 ] );
        } else {
            return $prepared_array;
        }
    }
}
