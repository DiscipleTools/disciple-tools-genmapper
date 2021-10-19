<?php
/**
 * Rest API example class
 */


class DT_Genmapper_Plugin_Endpoints
{
    public $permissions = [ 'access_contacts' ];
    public $icons;

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    private $version = 1;
    private $context = "dt-genmapper";
    private $namespace;
    public function __construct() {
        $this->namespace = $this->context . "/v" . intval( $this->version );
        $this->icons = DT_Genmapper_Plugin_Icons::instance();
        add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
    }

    public function has_permission(){
        $pass = false;
        foreach ( $this->permissions as $permission ){
            if ( current_user_can( $permission ) ){
                $pass = true;
            }
        }
        return $pass;
    }


    public function add_api_routes() {
        register_rest_route(
            $this->namespace, '/icon', [
                'methods'  => 'POST',
                'callback' => [ $this, 'update_icon' ],
                'validate_callback' => [ $this, 'validate_update_icon'],
                'permission_callback' => function( WP_REST_Request $request ) {
                    return $this->has_permission();
                }
            ]
        );
    }

    public function validate_update_icon( WP_REST_Request $request ) {
        $body = $request->get_body_params();

        if (!isset($body['icon']) || !isset($body['value'])) {
            return false;
        }

        $icon = $this->icons->find($body['icon'], false);
        if (!$icon) {
            return false;
        }
        return true;
    }

    public function update_icon( WP_REST_Request $request ){
        $body = $request->get_body_params();
        $icon = $this->icons->find($body['icon'], false);
        var_dump($icon);
        $current = get_option($icon['option']);
        $result = true;
        if ($current && !$body['value']) {
            $result = delete_option($icon['option']);
        } elseif($body['value']) {
            $result = update_option($icon['option'], $body['value'], false);
        }
        if (!$result) {
            return new WP_Error(501, 'Failed to save icon.');
        }
        return true;
    }
}
