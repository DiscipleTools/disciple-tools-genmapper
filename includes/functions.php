<?php

/**
 * Functions class
 */
class DT_Genmapper_Plugin_Functions
{
    private static $_instance = null;

    public static function instance() {
        if (is_null( self::$_instance )) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * DT_Genmapper_Plugin_Functions constructor.
     */
    public function __construct() {
        //We only need these filters if we're showing metrics
        if (get_option( "dt_genmapper_show_health_metrics", true )) {
            add_filter( 'dt_custom_fields_settings', [ $this, 'dt_custom_fields_settings' ], 10, 2 );
            add_filter( 'dt_post_update_allow_fields', [ $this, 'dt_post_update_allow_fields' ], 1, 2 );
            add_action( 'added_post_meta', [ $this, 'dt_updated_post_meta' ], 10, 4 );
            add_action( 'updated_post_meta', [ $this, 'dt_updated_post_meta' ], 10, 4 );
            add_action( 'deleted_post_meta', [ $this, 'dt_deleted_post_meta' ], 10, 4 );
            add_action( "post_connection_added", [ $this, "post_connection_added" ], 10, 4 );
            add_action( "post_connection_removed", [ $this, "post_connection_removed" ], 10, 4 );
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_group_scripts' ], 99 );
            add_action( 'dt_details_additional_section', [ $this, 'dt_details_additional_section' ], 99, 2 );
        }
    }

    /**
     * Enqueue the extra groups page script
     */
    public function enqueue_group_scripts() {
        if (is_singular( "groups" ) && get_the_ID() && DT_Posts::can_view( 'groups', get_the_ID() )) {
            wp_enqueue_script('dt_genmapper_script', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'groups.js', [],
            filemtime( plugin_dir_path( __FILE__ ) . '/groups.js' ), true);
        }
    }

    public function dt_details_additional_section( $section, $post_type ) {
        if ( $post_type === "groups" && $section === "relationships" ) {
            $post_settings = DT_Posts::get_post_settings( $post_type );
            function map_fields( $field) {
                $field['custom_display'] = false;
                return $field;
            }
            function filter_fields( $field) {
                return !empty( $field['genmapper_metric'] );
            }
            $fields = array_map( "map_fields", array_filter( $post_settings['fields'], "filter_fields" ) );
            $post = DT_Posts::get_post( $post_type, get_the_ID() );
            $collapse_fields = get_option( 'dt_genmapper_collapse_metrics', true );
            include DT_Genmapper_Metrics::includes_dir() . 'template-group-relationship-fields.php';
        }
    }


    /**
     * Add extra count fields to the groups page
     * @param $fields
     * @param $post_type
     * @return mixed
     */
    public function dt_custom_fields_settings( $fields, $post_type) {
        if ($post_type === 'groups') {
            $fields["believer_count"] = [
                'name' => __( 'Believer Count', 'disciple_tools' ),
                'description' => _x( 'The number of believers in this group.', 'Optional Documentation', 'disciple_tools' ),
                'type' => 'number',
                'default' => '',
                'tile' => 'relationships',
                'custom_display' => true,
                'genmapper_metric' => true,
                "icon" => DT_Genmapper_Metrics::path() . '/includes/charts/church-circles/icons/believers.svg',
            ];
            $fields["baptized_count"] = [
                'name' => __( 'Baptized Count', 'disciple_tools' ),
                'description' => _x( 'The number of believers who are baptized.', 'Optional Documentation', 'disciple_tools' ),
                'type' => 'number',
                'default' => '',
                'tile' => 'relationships',
                'custom_display' => true,
                'genmapper_metric' => true,
                "icon" => get_template_directory_uri() . '/dt-assets/images/groups/baptism-2.svg',
            ];
            $fields["baptized_in_group_count"] = [
                'name' => __( 'Baptized in Group Count', 'disciple_tools' ),
                'description' => _x( 'The number of believers who are baptized by the group', 'Optional Documentation', 'disciple_tools' ),
                'type' => 'number',
                'default' => '',
                'tile' => 'relationships',
                'custom_display' => true,
                'genmapper_metric' => true,
                "icon" => DT_Genmapper_Metrics::path() . '/includes/charts/church-circles/icons/baptized-in-group.svg',
            ];
        }

        return $fields;
    }

    /**
     * Allow the extra counts fields to be included in updates.
     * @param $fields
     * @param $post_type
     * @return mixed
     */
    public function dt_post_update_allow_fields( $fields, $post_type) {
        if ($post_type === 'groups') {
            $fields[] = "believer_count";
            $fields[] = "baptized_count";
            $fields[] = "baptized_in_group_count";
        }

        return $fields;
    }

    /**
     * Refresh counts when milestones are updated
     * @param $meta_id
     * @param $object_id
     * @param $meta_key
     * @param $_meta_value
     */
    public function dt_updated_post_meta( $meta_id, $object_id, $meta_key, $_meta_value) {
        if ($meta_key === 'milestones') {
            self::refresh_milestone_counts_from_contact( $object_id );
        }
    }

    /**
     * Refresh counts when milestones are deleted
     * @param $meta_id
     * @param $object_id
     * @param $meta_key
     * @param $_meta_value
     */
    public function dt_deleted_post_meta( $meta_id, $object_id, $meta_key, $_meta_value) {
        if ($meta_key === 'milestones') {
            self::refresh_milestone_counts_from_contact( $object_id, 'removed' );
        }
    }

    /**
     * Update a contacts counts.
     *
     * @param $contact_id
     * @param string $action
     */
    private static function refresh_milestone_counts_from_contact( $contact_id, $action = "added") {
        $groups = get_posts([
            'connected_type' => 'contacts_to_groups',
            'connected_items' => get_post( $contact_id ),
            'nopaging' => true,
            'suppress_filters' => false
        ]);
        if ( !$groups) {
            return;
        }
        foreach ($groups as $group) {
            self::refresh_milestone_counts( $group->ID, $action );
        }
    }

    /**
     * action when a post connection is added during create or update
     *
     * @param $post_type
     * @param $post_id
     * @param $field_key
     * @param $value
     */
    public function post_connection_added( $post_type, $post_id, $field_key, $value) {
        if ($post_type === "groups" && $field_key === "members") {
            self::refresh_milestone_counts( $post_id );
        } elseif ($post_type === "contacts" && $field_key === "groups") {
            self::refresh_milestone_counts( $value );
        }
    }

    /**
     * action when a post connection is removed during create or update
     *
     * @param $post_type
     * @param $post_id
     * @param $field_key
     * @param $value
     */
    public function post_connection_removed( $post_type, $post_id, $field_key, $value) {
        if ($post_type === "groups" && $field_key === "members") {
            self::refresh_milestone_counts( $post_id, "removed" );
        } elseif ($post_type === "contacts" && $field_key === "groups") {
            self::refresh_milestone_counts( $value, "removed" );
        }
    }

    /**
     * Refresh the milestone counts
     * @param $group_id
     * @param string $action
     */
    private static function refresh_milestone_counts( $group_id, $action = "added") {
        $group = get_post( $group_id );
        $contacts = get_posts([
            'connected_type' => 'contacts_to_groups',
            'connected_items' => $group,
            'connected_direction' => 'to',
            'nopaging' => true,
            'suppress_filters' => false
        ]);
        if ( !$contacts) {
            return;
        }
        $current_belief_count = intval( get_post_meta( $group_id, 'believer_count', true ) );
        $belief_count = 0;
        $current_baptism_count = intval( get_post_meta( $group_id, 'baptized_count', true ) );
        $baptism_count = 0;
        foreach ($contacts as $contact) {
            $milestones = get_metadata( 'post', $contact->ID, 'milestones' );
            if (in_array( 'milestone_belief', $milestones )) {
                $belief_count++;
            }
            if (in_array( 'milestone_baptized', $milestones )) {
                $baptism_count++;
            }
        }
        if ($action === 'added' && $current_belief_count < $belief_count) {
            update_post_meta( $group_id, 'believer_count', $belief_count );
        } else if ($action === 'removed' && $current_belief_count > $belief_count) {
            update_post_meta( $group_id, 'believer_count', $belief_count );
        }
        if ($action === 'added' && $current_baptism_count < $baptism_count) {
            update_post_meta( $group_id, 'baptized_count', $baptism_count );
        } else if ($action === 'removed' && $current_baptism_count > $baptism_count) {
            update_post_meta( $group_id, 'baptized_count', $baptism_count );
        }
    }
}
