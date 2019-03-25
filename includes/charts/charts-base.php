<?php


abstract class DT_Genmapper_Metrics_Chart_Base
{

    public $namespace = "dt/v1/genmapper/";
    public $base_slug = 'genmapper';
    public $base_title = "Genmapper";

    //child
    public $title = '';
    public $slug = '';
    public $js_object_name = ''; // This object will be loaded into the metrics.js file by the wp_localize_script.
    public $js_file_name = ''; // should be full file name plus extension
    public $deep_link_hash = ''; // should be the full hash name. #example_of_hash
    public $permissions = [];
    /**
     * Disciple_Tools_Counter constructor.
     */
    public function __construct() {
        $this->base_slug = str_replace( ' ', '', trim( strtolower( $this->base_slug ) ) );
        $url_path = dt_get_url_path();

        if ( strpos( $url_path, 'metrics' ) === 0 ) {
            if ( !$this->has_permission() ){
                return;
            }
            add_filter( 'dt_metrics_menu', [ $this, 'base_menu' ], 99 ); //load menu links
            if ( strpos( $url_path, 'metrics/genmapper' ) === 0 ) {
                add_filter( 'dt_templates_for_urls', [ $this, 'base_add_url' ] ); // add custom URLs
                add_action( 'wp_enqueue_scripts', [ $this, 'base_scripts' ], 99 );
                add_action( 'rest_api_init', [ $this, 'base_api_routes' ] );
            }
        }
    }

    public function base_menu( $content ) {
        $line = '<li><a href="'. site_url( '/metrics/'.$this->base_slug.'/'.$this->slug.'/' ) . $this->deep_link_hash.'">' . $this->title . '</a></li>';

        $ref = '<ul class="menu vertical nested" id="' . $this->base_slug . '">';
        $pos = strpos( $content, $ref );
        if ( $pos === false ){
            $content .= '
            <li><a href="'. site_url( '/metrics/'. $this->base_slug .'/'. $this->deep_link_hash ) .'">'.$this->base_title.'</a>
                <ul class="menu vertical nested" id="' . $this->base_slug . '">'
                        . $line . '
            </ul></li>';
        } else {
            $content = substr_replace( $content, $ref . $line, $pos, strlen( $ref ) );
        }

        return $content;
    }

    public function base_add_url( $template_for_url ) {
//        $template_for_url["metrics/$this->base_slug"] = 'template-metrics.php';
        $template_for_url["metrics/$this->base_slug/$this->slug"] = 'template-metrics.php';
        return $template_for_url;
    }

    public function base_scripts() {
        wp_enqueue_script( 'dt_'.$this->base_slug.'_script', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'charts-base.js', [
            'jquery',
            'jquery-ui-core',
        ], filemtime( plugin_dir_path( __FILE__ ) .'charts-base.js' ), true );

        // Localize script with array data
        wp_localize_script(
            'dt_'.$this->base_slug.'_script', 'wpApiBase', [
                'slug' => $this->base_slug,
                'root' => esc_url_raw( rest_url() ),
                'plugin_uri' => plugin_dir_url( __DIR__ ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'current_user_login' => wp_get_current_user()->user_login,
                'current_user_id' => get_current_user_id(),
                'spinner' => '<img src="' .trailingslashit( plugin_dir_url( __DIR__ ) ) . 'ajax-loader.gif" style="height:1em;" />',
                'map_key' => get_option( 'dt_map_key' ), // this expects Disciple Tools to have this google maps key installed
                'stats' => $this->base_chart_data(),
                'translations' => $this->base_translations(),
            ]
        );
    }

    public function base_chart_data() {
        return [
            'sample' => [],
        ];
    }

    public function base_translations() {
        return [
            "title" => $this->base_title,
        ];
    }

    /**
     * Rest Endpoint
     */
    public function base_api_routes() {
        register_rest_route(
            $this->namespace, 'sample', [
                'methods'  => 'POST',
                'callback' => [ $this, 'base_sample' ],
            ]
        );
    }

    public function base_sample( WP_REST_Request $request ) {
        if ( $this->has_permission() ){
            return new WP_Error( __METHOD__, __( 'Permission Denied' ) );
        }
        $params = $request->get_params();
        if ( isset( $params['button_data'] ) ) {
            // Do something
            $results = $params['button_data'];
            return $results;
        } else {
            return new WP_Error( __METHOD__, __( 'Missing parameters.' ) );
        }
    }

    public function has_permission(){
        $permissions = $this->permissions;
        $pass = count( $permissions ) === 0;
        foreach ( $this->permissions as $permission ){
            if ( current_user_can( $permission ) ){
                $pass = true;
            }
        }
        return $pass;
    }

    public function get_node_descendants( $nodes, $node_ids ){
        $descendants = [];
        $children = [];
        foreach ( $nodes as $node ){
            $parent_id = $node["parent_id"] ?? $node["parentId"];
            if ( in_array( $parent_id, $node_ids ) ){
                $descendants[] = $node;
                $children[] = $node["id"];
            }
        }
        if ( sizeof( $children ) > 0 ){
            $descendants = array_merge( $descendants, $this->get_node_descendants( $nodes, $children ) );
        }
        return $descendants;
    }
}
