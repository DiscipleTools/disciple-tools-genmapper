<?php

/**
 * Icons available to the genmapper
 */
class DT_Genmapper_Plugin_Icons
{
    private static $_instance = null;

    /**
     * Icon groups
     * @return array[]
     */
    private function groups() {
        return [
            [
                'handle' => 'metrics',
                'label' => __( 'Metrics', 'disciple-tools-genmapper' )
            ],
            [
                'handle' => 'health',
                'label' => __( 'Health', 'disciple-tools-genmapper' )
            ]
        ];
    }

    /**
     * The icons.
     * @var string[][]
     */
    private function icons() {
        return [
            [
                'label' => __( 'Attenders', 'disciple-tools-genmapper' ),
                'option' => 'dt_genmapper_attenders_icon',
                'default' => 'attenders.svg',
                'group' => 'metrics',
            ],
            [
                'label' => __( 'Believers', 'disciple-tools-genmapper' ),
                'option' => 'dt_genmapper_believers_icon',
                'default' => 'fellowship.svg',
                'group' => 'metrics'
            ],
            [
                'label' => __( 'Baptism', 'disciple-tools-genmapper' ),
                'option' => 'dt_genmapper_baptized_icon',
                'default' => 'baptism.svg',
                'group' => 'metrics'
            ],
            [
                'label' => __( 'Fellowship', 'disciple-tools-genmapper' ),
                'option' => 'dt_genmapper_health_fellowship_icon',
                'default' => 'fellowship.svg',
                'group' => 'health'
            ],
            [
                'label' => __( 'Communion', 'disciple-tools-genmapper' ),
                'option' => 'dt_genmapper_health_communion_icon',
                'default' => 'communion.svg',
                'group' => 'health'
            ],
            [
                'label' => __( 'Leaders', 'disciple-tools-genmapper' ),
                'option' => 'dt_genmapper_health_leaders_icon',
                'default' => 'leadership.svg',
                'group' => 'health'
            ],
            [
                'label' => __( 'Sharing', 'disciple-tools-genmapper' ),
                'option' => 'dt_genmapper_health_sharing_icon',
                'default' => 'evangelism.svg',
                'group' => 'health'
            ],
            [
                'label' => __( 'Praise', 'disciple-tools-genmapper' ),
                'option' => 'dt_genmapper_health_praise_icon',
                'default' => 'praise.svg',
                'group' => 'health'
            ],
            [
                'label' => __( 'Word', 'disciple-tools-genmapper' ),
                'option' => 'dt_genmapper_health_bible_icon',
                'default' => 'word.svg',
                'group' => 'health'
            ],
            [
                'label' => __( 'Baptism', 'disciple-tools-genmapper' ),
                'option' => 'dt_genmapper_health_baptism_icon',
                'default' => 'baptism.svg',
                'group' => 'health'
            ],
            [
                'label' => __( 'Giving', 'disciple-tools-genmapper' ),
                'option' => 'dt_genmapper_health_giving_icon',
                'default' => 'giving.svg',
                'group' => 'health'
            ],
            [
                'label' => __( 'Prayer', 'disciple-tools-genmapper' ),
                'option' => 'dt_genmapper_health_prayer_icon',
                'default' => 'prayer.svg',
                'group' => 'health'
            ],
        ];
    }

    /**
     * Factory
     * @return DT_Genmapper_Plugin_Icons|null
     */
    public static function instance() {
        if (is_null( self::$_instance )) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Get all icons
     * @return string[][]
     */
    public function all() {
        return array_map(function ( $icon) {
            $icon['group'] = $this->find_group( $icon['group'] );
            return $icon;
        }, $this->icons());
    }

    /**
     * Get all icons hydrated with DB data
     * @return array
     */
    public function hydrated() {
        return array_map(function ( $icon) {
            return $this->hydrate_icon( $icon );
        }, $this->all());
    }

    /**
     * Find a single icon
     * @param $option_name
     * @return mixed
     */
    public function find( $option_name, $hydrated = true) {
        $result = array_filter($this->all(), function ( $icon) use ( $option_name) {
            return $option_name === $icon['option'];
        });
        $icon = array_shift( $result );

        if ( !$icon) {
            return null;
        }

        if ( !$hydrated) {
            return $icon;
        }

        return $this->hydrate_icon( $icon );
    }

    /**
     * Find a group by handle
     * @param $group_handle
     * @return mixed
     */
    private function find_group( $group_handle ) {
        $result = array_filter($this->groups(), function ( $group) use ( $group_handle) {
            return $group_handle === $group['handle'];
        });
        return array_shift( $result );
    }

    /**
     * Get all hydrated icons by group
     * @return mixed
     */
    public function by_group() {
        return array_reduce($this->hydrated(), function ( $groups, $icon) {
            $groups[$icon['group']['handle']][] = $icon;
            return $groups;
        }, []);
    }

    /**
     * Get the icons formatted for JS
     * @return mixed
     */
    public function for_js() {
        return array_reduce($this->hydrated(), function ( $icons, $icon) {
            $icons[$icon['group']['handle'] . '_' . strtolower( $icon['label'] )] = $icon['url'];
            return $icons;
        }, []);
    }

    /**
     * Populate an icon with data from the database
     * @param $icon
     * @return mixed
     */
    private function hydrate_icon( $icon) {
        $icon['default'] = DT_Genmapper_Metrics::path() . 'includes/charts/church-circles/icons/' . $icon['default'];
        $icon['attachment'] = get_option( $icon['option'] );
        $icon['url'] = $icon['attachment'] ? wp_get_attachment_url( $icon['attachment'] ) : $icon['default'];
        return $icon;
    }
}
