<?php

/**
 * Class DT_Genmapper_Plugin_Queries
 */
class DT_Genmapper_Plugin_Queries
{
    private static $_instance = null;

    public static function instance() {
        if (is_null( self::$_instance )) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct() {
        // Load required files
    } // End __construct

    /**
     * Group tree queries
     * @param $query_name
     * @param array $args
     * @return mixed
     */
    public function tree( $query_name, $args = []) {
        global $wpdb;
        $query = [];

        switch ($query_name) {
            case 'multiplying_groups_only':
                $query = $wpdb->get_results("
                    SELECT
                      a.ID         as id,
                      0            as parent_id,
                      a.post_title as name,
                      gs1.meta_value as group_status,
                      type1.meta_value as group_type,
                      coach1.post_title as coach,
                      location1.name as location_name,
                      startdate1.meta_value as start_date,
                      enddate1.meta_value as end_date,
                      IFNULL(members1.meta_value, 0) as total_members,
                      IFNULL(believers1.meta_value, 0) as total_believers,
                      IFNULL(baptized1.meta_value, 0) as total_baptized,
                      IFNULL(baptized2.meta_value, 0) as total_baptized_by_group,

                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as metricbaptized1 WHERE metricbaptized1.post_id = a.ID AND metricbaptized1.meta_key = 'health_metrics' AND metricbaptized1.meta_value = 'church_baptism')) as health_metrics_baptism,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as metricfellowship1 WHERE metricfellowship1.post_id = a.ID AND metricfellowship1.meta_key = 'health_metrics' AND metricfellowship1.meta_value = 'church_fellowship')) as health_metrics_fellowship,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as metricsharing1 WHERE metricsharing1.post_id = a.ID AND metricsharing1.meta_key = 'health_metrics' AND metricsharing1.meta_value = 'church_sharing')) as health_metrics_sharing,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as metricpraise1 WHERE metricpraise1.post_id = a.ID AND metricpraise1.meta_key = 'health_metrics' AND metricpraise1.meta_value = 'church_praise')) as health_metrics_praise,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as metricleaders1 WHERE metricleaders1.post_id = a.ID AND metricleaders1.meta_key = 'health_metrics' AND metricleaders1.meta_value = 'church_leaders')) as health_metrics_leaders,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as metriccommitment1 WHERE metriccommitment1.post_id = a.ID AND metriccommitment1.meta_key = 'health_metrics' AND metriccommitment1.meta_value = 'church_commitment')) as health_metrics_commitment,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as metricbible1 WHERE metricbible1.post_id = a.ID AND metricbible1.meta_key = 'health_metrics' AND metricbible1.meta_value = 'church_bible')) as health_metrics_bible,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as metriccommunion1 WHERE metriccommunion1.post_id = a.ID AND metriccommunion1.meta_key = 'health_metrics' AND metriccommunion1.meta_value = 'church_communion')) as health_metrics_communion,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as metricgiving1 WHERE metricgiving1.post_id = a.ID AND metricgiving1.meta_key = 'health_metrics' AND metricgiving1.meta_value = 'church_giving')) as health_metrics_giving,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as metricprayer1 WHERE metricprayer1.post_id = a.ID AND metricprayer1.meta_key = 'health_metrics' AND metricprayer1.meta_value = 'church_prayer')) as health_metrics_prayer,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as metriccommitment1 WHERE metriccommitment1.post_id = a.ID AND metriccommitment1.meta_key = 'health_metrics' AND metriccommitment1.meta_value = 'church_commitment')) as health_metrics_commitment
                    FROM $wpdb->posts as a
                    LEFT JOIN $wpdb->postmeta as members1
                      ON members1.post_id=a.ID
                      AND members1.meta_key = 'member_count'
                    LEFT JOIN $wpdb->postmeta as believers1
                      ON believers1.post_id=a.ID
                      AND believers1.meta_key = 'believer_count'
                    LEFT JOIN $wpdb->postmeta as baptized1
                      ON baptized1.post_id=a.ID
                      AND baptized1.meta_key = 'baptized_count'
                    LEFT JOIN $wpdb->postmeta as baptized2
                      ON baptized2.post_id=a.ID
                      AND baptized2.meta_key = 'baptized_in_group_count'
                    LEFT JOIN $wpdb->postmeta as gs1
                      ON gs1.post_id=a.ID
                      AND gs1.meta_key = 'group_status'
                    LEFT JOIN $wpdb->postmeta as type1
                      ON type1.post_id=a.ID
                      AND type1.meta_key = 'group_type'
                    LEFT JOIN $wpdb->p2p as groupcoach1
                      ON groupcoach1.p2p_from=a.ID
                      AND groupcoach1.p2p_type = 'groups_to_coaches'
                    LEFT JOIN $wpdb->posts as coach1
                      ON coach1.ID=groupcoach1.p2p_to
                    LEFT JOIN $wpdb->postmeta as grouplocation1
                      ON grouplocation1.post_id=a.ID
                      AND grouplocation1.meta_key = 'location_grid'
                    LEFT JOIN $wpdb->dt_location_grid as location1
                      ON location1.grid_id=grouplocation1.meta_value
                    LEFT JOIN $wpdb->postmeta as startdate1
                      ON startdate1.post_id=a.ID
                      AND startdate1.meta_key = 'church_start_date'
                    LEFT JOIN $wpdb->postmeta as enddate1
                      ON enddate1.post_id=a.ID
                      AND enddate1.meta_key = 'end_date'
                    WHERE a.post_status = 'publish'
                    AND a.post_type = 'groups'
                    AND a.ID NOT IN (
                      SELECT DISTINCT (p2p_from)
                      FROM $wpdb->p2p
                      WHERE p2p_type = 'groups_to_groups'
                      GROUP BY p2p_from
                    )
                      AND a.ID IN (
                      SELECT DISTINCT (p2p_to)
                      FROM $wpdb->p2p
                      WHERE p2p_type = 'groups_to_groups'
                      GROUP BY p2p_to
                    )
                    UNION
                    SELECT
                      p.p2p_from  as id,
                      p.p2p_to    as parent_id,
                      (SELECT sub.post_title FROM $wpdb->posts as sub WHERE sub.ID = p.p2p_from ) as name,
                      (SELECT gsmeta.meta_value FROM $wpdb->postmeta as gsmeta WHERE gsmeta.post_id = p.p2p_from AND gsmeta.meta_key = 'group_status' LIMIT 1 ) as group_status,
                      (SELECT gsmeta.meta_value FROM $wpdb->postmeta as gsmeta WHERE gsmeta.post_id = p.p2p_from AND gsmeta.meta_key = 'group_type' LIMIT 1 ) as group_type,
                      gcoach1.post_title as coach,
                      glocation1.name as location,
                      gstartdate1.meta_value as start_date,
                      genddate1.meta_value as end_date,
                      IFNULL(gmembers1.meta_value, 0) as total_members,
                      IFNULL(gbelievers1.meta_value, 0) as total_believers,
                      IFNULL(gbaptized1.meta_value, 0) as total_believers,
                      IFNULL(gbaptized2.meta_value, 0) as total_baptized_by_group,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as gmetricbaptized1 WHERE gmetricbaptized1.post_id = p.p2p_from AND gmetricbaptized1.meta_key = 'health_metrics' AND gmetricbaptized1.meta_value = 'church_baptism')) as health_metrics_baptism,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as gmetricfellowship1 WHERE gmetricfellowship1.post_id = p.p2p_from AND gmetricfellowship1.meta_key = 'health_metrics' AND gmetricfellowship1.meta_value = 'church_fellowship')) as health_metrics_fellowship,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as gmetricsharing1 WHERE gmetricsharing1.post_id = p.p2p_from AND gmetricsharing1.meta_key = 'health_metrics' AND gmetricsharing1.meta_value = 'church_sharing')) as health_metrics_sharing,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as gmetricpraise1 WHERE gmetricpraise1.post_id = p.p2p_from AND gmetricpraise1.meta_key = 'health_metrics' AND gmetricpraise1.meta_value = 'church_praise')) as health_metrics_praise,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as gmetricleaders1 WHERE gmetricleaders1.post_id = p.p2p_from AND gmetricleaders1.meta_key = 'health_metrics' AND gmetricleaders1.meta_value = 'church_leaders')) as health_metrics_leaders,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as gmetriccommitment1 WHERE gmetriccommitment1.post_id = p.p2p_from AND gmetriccommitment1.meta_key = 'health_metrics' AND gmetriccommitment1.meta_value = 'church_commitment')) as health_metrics_commitment,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as gmetricbible1 WHERE gmetricbible1.post_id = p.p2p_from AND gmetricbible1.meta_key = 'health_metrics' AND gmetricbible1.meta_value = 'church_bible')) as health_metrics_bible,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as gmetriccommunion1 WHERE gmetriccommunion1.post_id = p.p2p_from AND gmetriccommunion1.meta_key = 'health_metrics' AND gmetriccommunion1.meta_value = 'church_communion')) as health_metrics_communion,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as gmetricgiving1 WHERE gmetricgiving1.post_id = p.p2p_from AND gmetricgiving1.meta_key = 'health_metrics' AND gmetricgiving1.meta_value = 'church_giving')) as health_metrics_giving,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as gmetricprayer1 WHERE gmetricprayer1.post_id = p.p2p_from AND gmetricprayer1.meta_key = 'health_metrics' AND gmetricprayer1.meta_value = 'church_prayer')) as health_metrics_prayer,
                      (SELECT EXISTS (SELECT 1 FROM $wpdb->postmeta as gmetriccommitment1 WHERE gmetriccommitment1.post_id = p.p2p_from AND gmetriccommitment1.meta_key = 'health_metrics' AND gmetriccommitment1.meta_value = 'church_commitment')) as health_metrics_commitment
                    FROM $wpdb->p2p as p
                    LEFT JOIN $wpdb->postmeta as gmembers1
                      ON gmembers1.post_id=p.p2p_from
                      AND gmembers1.meta_key = 'member_count'
                    LEFT JOIN $wpdb->postmeta as gbelievers1
                      ON gbelievers1.post_id=p.p2p_from
                      AND gbelievers1.meta_key = 'believer_count'
                    LEFT JOIN $wpdb->postmeta as gbaptized1
                      ON gbaptized1.post_id=p.p2p_from
                      AND gbaptized1.meta_key = 'baptized_count'
                    LEFT JOIN $wpdb->postmeta as gbaptized2
                      ON gbaptized2.post_id=p.p2p_from
                      AND gbaptized2.meta_key = 'baptized_in_group_count'
                    LEFT JOIN $wpdb->p2p as ggroupcoach1
                      ON ggroupcoach1.p2p_from=p.p2p_from
                      AND ggroupcoach1.p2p_type = 'groups_to_coaches'
                    LEFT JOIN $wpdb->posts as gcoach1
                      ON gcoach1.ID=ggroupcoach1.p2p_to
                    LEFT JOIN $wpdb->postmeta as ggrouplocation1
                      ON ggrouplocation1.post_id=p.p2p_from
                      AND ggrouplocation1.meta_key = 'location_grid'
                    LEFT JOIN $wpdb->dt_location_grid as glocation1
                      ON glocation1.grid_id=ggrouplocation1.meta_value
                    LEFT JOIN $wpdb->postmeta as gstartdate1
                      ON gstartdate1.post_id=p.p2p_from
                      AND gstartdate1.meta_key = 'start_date'
                    LEFT JOIN $wpdb->postmeta as genddate1
                      ON genddate1.post_id=p.p2p_from
                      AND genddate1.meta_key = 'end_date'
                    WHERE p.p2p_type = 'groups_to_groups'
                ", ARRAY_A);
                break;

            default:
                break;
        }

        // guarantee only one record with one parent.
        $list = [];
        foreach( $query as $q) {
            $list[$q['id']] = $q;
        }

        return dt_queries()->check_tree_health( $list );
    }
}

/**
 * Factory function
 * @return DT_Genmapper_Plugin_Queries|null
 */
function dt_genmapper_plugin_queries() {
    return DT_Genmapper_Plugin_Queries::instance();
}
