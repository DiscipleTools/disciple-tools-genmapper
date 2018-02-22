<?php
/**
 * DT_Genmapper_Menu class for the admin page
 *
 * @class       DT_Genmapper_Menu
 * @version     0.1.0
 * @since       0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

/**
 * Initialize menu class
 */
DT_Genmapper_Menu::instance();

/**
 * Class DT_Genmapper_Menu
 */
class DT_Genmapper_Menu {

    public $token = 'dt_genmapper';

    private static $_instance = null;

    /**
     * DT_Genmapper_Menu Instance
     *
     * Ensures only one instance of DT_Genmapper_Menu is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return DT_Genmapper_Menu instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     * @access  public
     * @since   0.1.0
     */
    public function __construct() {
        add_action( "admin_menu", array( $this, "register_menu" ) );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
    } // End __construct()


    /**
     * Loads the subnav page
     * @since 0.1
     */
    public function register_menu()
    {
        add_menu_page( __( 'Extensions (DT)', 'disciple_tools' ), __( 'Extensions (DT)', 'disciple_tools' ), 'manage_dt', 'dt_extensions', [ $this, 'extensions_menu' ], 'dashicons-admin-generic', 59 );
        add_submenu_page( 'dt_extensions', __( 'GenMapper', 'dt_genmapper' ), __( 'GenMapper', 'dt_genmapper' ), 'manage_dt', $this->token, [ $this, 'content' ] );
    }

    /**
     * Menu stub. Replaced when Disciple Tools Theme fully loads.
     */
    public function extensions_menu() {}

    /**
     * Builds page contents
     * @since 0.1
     */
    public function content() {

        if ( !current_user_can( 'manage_dt' ) ) { // manage dt is a permission that is specific to Disciple Tools and allows admins, strategists and dispatchers into the wp-admin
            wp_die( esc_attr__( 'You do not have sufficient permissions to access this page.' ) );
        }

        if ( isset( $_GET["tab"] ) ) {
            $tab = sanitize_key( wp_unslash( $_GET["tab"] ) );
        } else {
            $tab = 'general';
        }

        $link = 'admin.php?page='.$this->token.'&tab=';

        ?>
        <div class="wrap">
            <h2><?php esc_attr_e( 'GenMapper', 'dt_genmapper' ) ?></h2>
            <h2 class="nav-tab-wrapper">
                <a href="<?php echo esc_attr( $link ) . 'general' ?>" class="nav-tab <?php ( $tab == 'general' || ! isset( $tab ) ) ? esc_attr_e( 'nav-tab-active', 'dt_genmapper' ) : print ''; ?>"><?php esc_attr_e( 'General', 'dt_genmapper' ) ?></a>
                <a href="<?php echo esc_attr( $link ) . 'second' ?>" class="nav-tab <?php ( $tab == 'second' ) ? esc_attr_e( 'nav-tab-active', 'dt_genmapper' ) : print ''; ?>"><?php esc_attr_e( 'Second', 'dt_genmapper' ) ?></a>
            </h2>

            <?php
            switch ($tab) {
                case "general":
                    $this->tab_general_settings();
                    break;
                case "second":
                    $this->tab_second_settings();
                    break;
                default:
                    break;
            }
            ?>

        </div><!-- End wrap -->

        <?php
    }

    public function tab_general_settings() {
        // begin columns template
        $this->template( 'begin' );

        /* Insert Call to contents */ $this->meta_box_sample();

        // begin right column template
        $this->template( 'right_column' );

        /* Insert Call to contents */ $this->meta_box_sample();

        // end columns template
        $this->template( 'end' );
    }

    public function tab_second_settings() {
        // begin columns template
        $this->template( 'begin' );

        /* Insert Call to contents */ $this->meta_box_sample();

        // begin right column template
        $this->template( 'right_column' );

        /* Insert Call to contents */ $this->meta_box_sample();

        // end columns template
        $this->template( 'end' );
    }

    public function template( $section, $columns = 2 ) {
        switch ( $columns ) {

            case '1':
                switch ( $section ) {
                    case 'begin':
                        ?>
                        <div class="wrap">
                        <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-1">
                        <div id="post-body-content">
                        <!-- Main Column -->
                        <?php
                        break;


                    case 'end':
                        ?>
                        </div><!-- postbox-container 1 -->
                        </div><!-- post-body meta box container -->
                        </div><!--poststuff end -->
                        </div><!-- wrap end -->
                        <?php
                        break;
                }
                break;

            case '2':
                switch ( $section ) {
                    case 'begin':
                        ?>
                        <div class="wrap">
                        <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content">
                        <!-- Main Column -->
                        <?php
                        break;
                    case 'right_column':
                        ?>
                        <!-- End Main Column -->
                        </div><!-- end post-body-content -->
                        <div id="postbox-container-1" class="postbox-container">
                        <!-- Right Column -->
                        <?php
                    break;
                    case 'end':
                        ?>
                        </div><!-- postbox-container 1 -->
                        </div><!-- post-body meta box container -->
                        </div><!--poststuff end -->
                        </div><!-- wrap end -->
                        <?php
                        break;
                }
                break;
        }
    }

    /**
     * Loads admin panel specific css and javascript
     */
    public function admin_scripts() {
        global $pagenow;

        if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'dt_genmapper' == sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {

            wp_enqueue_script( 'dt_genmapper_admin_script', dt_genmapper()->admin_uri . 'admin.js', [
                'jquery',
                'jquery-ui-core',
            ], filemtime( dt_genmapper()->admin_path . 'admin.js' ), true );

            wp_register_style( 'dt_genmapper_admin_css', dt_genmapper()->admin_uri . 'admin.css', [], filemtime( dt_genmapper()->admin_path . 'admin.css' ) );
            wp_enqueue_style( 'dt_genmapper_admin_css' );

        }
    }

    /**
     * This function is a placeholder for building metabox content @todo remove
     */
    public function meta_box_sample() {
        ?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
            <th>Header</th>
            </thead>
            <tbody>
            <tr>
                <td>
                    Content
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }
}