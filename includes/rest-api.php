<?php
/**
 * Rest API example class
 */
class DT_Genmapper_Plugin_Endpoints
{
    public $permissions = [ 'access_contacts' ];

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
        //Add API routes here
    }
}
