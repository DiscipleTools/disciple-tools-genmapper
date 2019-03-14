<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.

class DT_Genmapper_Metrics_Chart extends DT_Genmapper_Metrics_Chart_Base
{

    public $title = 'Groups';
    public $slug = 'groups'; // lowercase
    public $js_object_name = 'wpApiGenmapper'; // This object will be loaded into the metrics.js file by the wp_localize_script.
    public $js_file_name = 'one-page-chart-template.js'; // should be full file name plus extension
    public $deep_link_hash = '#groups'; // should be the full hash name. #genmapper_of_hash
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
        wp_enqueue_style( "group-styles", trailingslashit( plugin_dir_url( __FILE__ ) ) . "church-circles/style.css", [], filemtime( plugin_dir_path( __FILE__ ) . "church-circles/style.css" ) );
        wp_enqueue_style( "styles", trailingslashit( plugin_dir_url( __FILE__ ) ) . "style.css", [], filemtime( plugin_dir_path( __FILE__ ) . "style.css" ) );
        wp_register_script( 'd3', 'https://d3js.org/d3.v5.min.js', false, '5' ); //@todo v5

        $group_fields = Disciple_Tools_Groups_Post_Type::instance()->get_custom_fields_settings();
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
        wp_enqueue_script( 'gen_translations', trailingslashit( plugin_dir_url( __FILE__ ) ) . "translations.js", [
            'jquery',
            'jquery-ui-core',
        ], filemtime( plugin_dir_path( __FILE__ ) . "translations.js" ), true );
        wp_enqueue_script( 'genmapper', trailingslashit( plugin_dir_url( __FILE__ ) ) . "genmapper.js", [
            'jquery',
            'jquery-ui-core',
            'd3',
            'gen_translations',
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
                'map_key' => get_option( 'dt_map_key' ), // this expects Disciple Tools to have this google maps key installed
                'stats' => [
                    // add preload stats data into arrays here

                ],
                'translations' => [
                    "title" => $this->title,
                    "Sample API Call" => __( "Sample API Call" )
                ]
            ]
        );
    }

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

    function get_node_descendants( $nodes, $node_ids ){
        $descendants = [];
        $children = [];
        foreach ( $nodes as $node ){
            if ( in_array( $node["parent_id"], $node_ids ) ){
                $descendants[] = $node;
                $children[] = $node["id"];
            }
        }
        if ( sizeof( $children ) > 0 ){
            $descendants = array_merge( $descendants, $this->get_node_descendants( $nodes, $children ) );
        }
        return $descendants;
    }

    /**
     * Respond to transfer request of files
     *
     * @param \WP_REST_Request $request
     * @return array|\WP_Error
     */
    public function groups( WP_REST_Request $request ) {

        $params = $request->get_params();

        global $wpdb;
//        $groups = $wpdb->get_results("
//            SELECT ID, post_title, parent.meta_value as parent
//            FROM $wpdb->posts posts
//            LEFT JOIN $wpdb->postmeta parent ON ( parent.post_id = posts.ID AND parent.meta_key = 'parent_group_id' )
//            WHERE posts.post_type = 'groups'
//        ", ARRAY_A );
        $prepared_array = [
            [
                "id" => 0,
                "parentId" => "",
                "name" => "source"
            ]
        ];
        $groups = dt_queries()->tree( 'multiplying_groups_only' );
//        $groups = dt_queries()->tree( 'group_all' );

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


        $church_health_query = $wpdb->get_results("
            SELECT pm.post_id, GROUP_CONCAT(pm.meta_value) as meta
            FROM $wpdb->postmeta pm
            WHERE pm.meta_key = 'health_metrics'
            GROUP BY post_id
        ", ARRAY_A );
        $church_health = [];
        foreach ( $church_health_query as $query ){
            $church_health[ $query["post_id"] ] = $query["meta"];
        }


        foreach ( $groups as $group ){
            $values = [
                "id" => $group["id"],
                "parentId" => $group["parent_id"] ?? 0,
                "name" => $group["name"],
                "church" => $group["group_type"] === "church",
                "active" => $group["group_status"] === "active",
                "group_type" => $group["group_type"]
            ];
            if ( isset( $church_health[ $group["id"] ] ) ){
                $health_metrics = explode( ',', $church_health[ $group["id"] ] );
                foreach ( $health_metrics as $health_metric ){
                    $values[$health_metric] = true;
                }
            }
            $prepared_array[] = $values;
        }


//        $prepared_array = [
//              [
//                "id" => 0,
//                "parentId" => "",
//                "name" => "John",
//                "email" => "",
//                "peopleGroup" => "",
//                "attenders" => "5",
//                "believers" => "5",
//                "baptized" => "3",
//                "newlyBaptized" => "1",
//                "church" => true,
//                "churchType" => "legacy",
//                "elementBaptism" => true,
//                "elementWord" => true,
//                "elementPrayer" => true,
//                "elementLordsSupper" => true,
//                "elementGive" => true,
//                "elementLove" => true,
//                "elementWorship" => true,
//                "elementLeaders" => true,
//                "elementMakeDisciples" => true,
//                "place" => "New York",
//                "date" => "9-13",
//                "threeThirds" => "1234567",
//                "active" => true
//              ],
//              [
//                  "id" => 2,
//                "parentId" => 0,
//                "name" => "Joe",
//                "email" => "",
//                "peopleGroup" => "",
//                "attenders" => "7",
//                "believers" => "6",
//                "baptized" => "3",
//                "newlyBaptized" => "3",
//                "church" => true,
//                "churchType" => "existingBelievers",
//                "elementBaptism" => true,
//                "elementWord" => true,
//                "elementPrayer" => true,
//                "elementLordsSupper" => false,
//                "elementGive" => false,
//                "elementLove" => true,
//                "elementWorship" => true,
//                "elementLeaders" => true,
//                "elementMakeDisciples" => true,
//                "place" => "Phoenix",
//                "date" => "12-13",
//                "threeThirds" => "123456",
//                "active" => true
//              ],
//              [
//                  "id" => 1,
//                "parentId" => 0,
//                "name" => "Jack",
//                "email" => "",
//                "peopleGroup" => "",
//                "attenders" => "5",
//                "believers" => "1",
//                "baptized" => "1",
//                "newlyBaptized" => "0",
//                "church" => false,
//                "churchType" => "newBelievers",
//                "elementBaptism" => false,
//                "elementWord" => true,
//                "elementPrayer" => true,
//                "elementLordsSupper" => false,
//                "elementGive" => false,
//                "elementLove" => true,
//                "elementWorship" => false,
//                "elementLeaders" => false,
//                "elementMakeDisciples" => false,
//                "place" => "Phoenix",
//                "date" => "1-14",
//                "threeThirds" => "13456",
//                "active" => true
//              ],
//              [
//                  "id" => 4,
//                "parentId" => 2,
//                "name" => "Joanna",
//                "email" => "",
//                "peopleGroup" => "",
//                "attenders" => "4",
//                "believers" => "2",
//                "baptized" => "0",
//                "newlyBaptized" => "0",
//                "church" => false,
//                "churchType" => "newBelievers",
//                "elementBaptism" => false,
//                "elementWord" => true,
//                "elementPrayer" => true,
//                "elementLordsSupper" => true,
//                "elementGive" => false,
//                "elementLove" => true,
//                "elementWorship" => true,
//                "elementLeaders" => false,
//                "elementMakeDisciples" => false,
//                "place" => "Orlando",
//                "date" => "5-14",
//                "threeThirds" => "123456",
//                "active" => true
//              ],
//              [
//                  "id" => 3,
//                "parentId" => 2,
//                "name" => "Jasmine",
//                "email" => "",
//                "peopleGroup" => "",
//                "attenders" => "6",
//                "believers" => "3",
//                "baptized" => "3",
//                "newlyBaptized" => "0",
//                "church" => true,
//                "churchType" => "existingBelievers",
////                "elementBaptism" => true,
////                "elementWord" => true,
////                "elementPrayer" => true,
////                "elementLordsSupper" => false,
////                "elementGive" => false,
////                "elementLove" => true,
////                "elementWorship" => false,
////                "elementLeaders" => true,
////                "elementMakeDisciples" => false,
////                "place" => "Berlin",
////                "date" => "7-14",
////                "threeThirds" => "1356",
//                "active" => true
//              ]
//        ];

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
