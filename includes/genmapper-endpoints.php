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

        register_rest_route(
            $namespace, $branch . 'disciples', [
                [
                'methods'  => WP_REST_Server::READABLE,
                'callback' => [ $this, 'disciples' ],
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
                "id" => 0,
                "parentId" => "",
                "name" => "John",
                "email" => "",
                "peopleGroup" => "",
                "attenders" => "5",
                "believers" => "5",
                "baptized" => "3",
                "newlyBaptized" => "1",
                "church" => true,
                "churchType" => "legacy",
                "elementBaptism" => true,
                "elementWord" => true,
                "elementPrayer" => true,
                "elementLordsSupper" => true,
                "elementGive" => true,
                "elementLove" => true,
                "elementWorship" => true,
                "elementLeaders" => true,
                "elementMakeDisciples" => true,
                "place" => "New York",
                "date" => "9-13",
                "threeThirds" => "1234567",
                "active" => true
              ],
              [
                  "id" => 2,
                "parentId" => 0,
                "name" => "Joe",
                "email" => "",
                "peopleGroup" => "",
                "attenders" => "7",
                "believers" => "6",
                "baptized" => "3",
                "newlyBaptized" => "3",
                "church" => true,
                "churchType" => "existingBelievers",
                "elementBaptism" => true,
                "elementWord" => true,
                "elementPrayer" => true,
                "elementLordsSupper" => false,
                "elementGive" => false,
                "elementLove" => true,
                "elementWorship" => true,
                "elementLeaders" => true,
                "elementMakeDisciples" => true,
                "place" => "Phoenix",
                "date" => "12-13",
                "threeThirds" => "123456",
                "active" => true
              ],
              [
                  "id" => 1,
                "parentId" => 0,
                "name" => "Jack",
                "email" => "",
                "peopleGroup" => "",
                "attenders" => "5",
                "believers" => "1",
                "baptized" => "1",
                "newlyBaptized" => "0",
                "church" => false,
                "churchType" => "newBelievers",
                "elementBaptism" => false,
                "elementWord" => true,
                "elementPrayer" => true,
                "elementLordsSupper" => false,
                "elementGive" => false,
                "elementLove" => true,
                "elementWorship" => false,
                "elementLeaders" => false,
                "elementMakeDisciples" => false,
                "place" => "Phoenix",
                "date" => "1-14",
                "threeThirds" => "13456",
                "active" => true
              ],
              [
                  "id" => 4,
                "parentId" => 2,
                "name" => "Joanna",
                "email" => "",
                "peopleGroup" => "",
                "attenders" => "4",
                "believers" => "2",
                "baptized" => "0",
                "newlyBaptized" => "0",
                "church" => false,
                "churchType" => "newBelievers",
                "elementBaptism" => false,
                "elementWord" => true,
                "elementPrayer" => true,
                "elementLordsSupper" => true,
                "elementGive" => false,
                "elementLove" => true,
                "elementWorship" => true,
                "elementLeaders" => false,
                "elementMakeDisciples" => false,
                "place" => "Orlando",
                "date" => "5-14",
                "threeThirds" => "123456",
                "active" => true
              ],
              [
                  "id" => 3,
                "parentId" => 2,
                "name" => "Jasmine",
                "email" => "",
                "peopleGroup" => "",
                "attenders" => "6",
                "believers" => "3",
                "baptized" => "1",
                "newlyBaptized" => "0",
                "church" => false,
                "churchType" => "newBelievers",
                "elementBaptism" => true,
                "elementWord" => true,
                "elementPrayer" => true,
                "elementLordsSupper" => false,
                "elementGive" => false,
                "elementLove" => true,
                "elementWorship" => false,
                "elementLeaders" => true,
                "elementMakeDisciples" => false,
                "place" => "Berlin",
                "date" => "7-14",
                "threeThirds" => "1356",
                "active" => false
              ]
        ];

        if ( empty( $prepared_array ) ) {
            return new WP_Error( 'failed_to_build_data', 'Failed to build data', [ 'status' => 400 ] );
        } else {
            return $prepared_array;
        }
    }

    /**
     * Respond to transfer request of files
     *
     * @param \WP_REST_Request $request
     * @return array|\WP_Error
     */
    public function disciples( WP_REST_Request $request ) {

        $params = $request->get_params();

        $prepared_array = [
            [
            "id" => 0,
            "parentId" => "",
            "name" => "Barnabas",
            "date" => "30",
            "believer" => true,
            "baptized" => true,
            "word" => true,
            "prayer" => true,
            "field1" => true,
            "field2" => true,
            "field3" => true,
            "field4" => true,
            "field5" => true,
            "discipleType" => "facilitatesChurch",
            "timothy" => true,
            "active" => true
            ],
            [
              "id" => 1,
            "parentId" => 0,
            "name" => "Paul",
            "date" => "40",
            "believer" => true,
            "baptized" => true,
            "word" => true,
            "prayer" => true,
            "field1" => true,
            "field2" => true,
            "field3" => true,
            "field4" => true,
            "field5" => true,
            "discipleType" => "facilitatesChurch",
            "timothy" => true,
            "active" => true
            ],
            [
              "id" => 2,
            "parentId" => 1,
            "name" => "Timothy",
            "date" => "",
            "believer" => true,
            "baptized" => true,
            "word" => true,
            "prayer" => true,
            "field1" => true,
            "field2" => true,
            "field3" => true,
            "field4" => true,
            "field5" => true,
            "discipleType" => "facilitatesGroup",
            "timothy" => true,
            "active" => true
            ],
            [
              "id" => 3,
            "parentId" => 1,
            "name" => "Titus",
            "date" => "",
            "believer" => true,
            "baptized" => true,
            "word" => true,
            "prayer" => true,
            "field1" => false,
            "field2" => false,
            "field3" => false,
            "field4" => false,
            "field5" => false,
            "discipleType" => "individual",
            "timothy" => false,
            "active" => true
            ]
        ];

        if ( empty( $prepared_array ) ) {
            return new WP_Error( 'failed_to_build_data', 'Failed to build data', [ 'status' => 400 ] );
        } else {
            return $prepared_array;
        }
    }
}