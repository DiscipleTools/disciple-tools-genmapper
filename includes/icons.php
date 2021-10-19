<?php
/**
 * Icons available to the genmapper
 */
class DT_Genmapper_Plugin_Icons
{
    private static $_instance = null;

    /**
     * The icons.
     * @var string[][]
     */
    private $icons = [
        [
            'label' => 'Attenders',
            'option' => 'dt_genmapper_attenders_icon',
            'default' => 'attenders.svg',
            'group' => 'metrics',
        ],
        [
            'label' => 'Believers',
            'option' => 'dt_genmapper_believers_icon',
            'default' => 'fellowship.svg',
            'group' => 'metrics'
        ],
        [
            'label' => 'Baptism',
            'option' => 'dt_genmapper_baptized_icon',
            'default' => 'baptism.svg',
            'group' => 'metrics'
        ],
        [
            'label' => 'Fellowship',
            'option' => 'dt_genmapper_health_fellowship_icon',
            'default' => 'fellowship.svg',
            'group' => 'health'
        ],
        [
            'label' => 'Communion',
            'option' => 'dt_genmapper_health_communion_icon',
            'default' => 'communion.svg',
            'group' => 'health'
        ],
        [
            'label' => 'Leaders',
            'option' => 'dt_genmapper_health_leaders_icon',
            'default' => 'leadership.svg',
            'group' => 'health'
        ],
        [
            'label' => 'Sharing',
            'option' => 'dt_genmapper_health_sharing_icon',
            'default' => 'evangelism.svg',
            'group' => 'health'
        ],
        [
            'label' => 'Praise',
            'option' => 'dt_genmapper_health_praise_icon',
            'default' => 'praise.svg',
            'group' => 'health'
        ],
        [
            'label' => 'Word',
            'option' => 'dt_genmapper_health_bible_icon',
            'default' => 'word.svg',
            'group' => 'health'
        ],
        [
            'label' => 'Baptism',
            'option' => 'dt_genmapper_health_baptism_icon',
            'default' => 'baptism.svg',
            'group' => 'health'
        ],
        [
            'label' => 'Giving',
            'option' => 'dt_genmapper_health_giving_icon',
            'default' => 'giving.svg',
            'group' => 'health'
        ],
        [
            'label' => 'Prayer',
            'option' => 'dt_genmapper_health_prayer_icon',
            'default' => 'prayer.svg',
            'group' => 'health'
        ],
    ];

    /**
     * Factory
     * @return DT_Genmapper_Plugin_Icons|null
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Get all icons
     * @return string[][]
     */
    public function all() {
        return $this->icons;
    }

    /**
     * Get all icons hydrated with DB data
     * @return array
     */
    public function hydrated() {
        return array_map(function($icon) {
           return $this->hydrateIcon($icon);
        }, $this->all());
    }

    /**
     * Find a single icon
     * @param $optionName
     * @return mixed
     */
    public function find($optionName, $hydrated = true) {
        var_dump($optionName);

        $result = array_filter($this->all(), function($icon) use ($optionName) {
            return $optionName === $icon['option'];
        });
        $icon = array_shift($result);

        if (!$icon) {
            return null;
        }

        if (!$hydrated) {
            return $icon;
        }

        return $this->hydrateIcon($icon);
    }

    /**
     * Get all hydrated icons by group
     * @return mixed
     */
    public function groups() {
        return array_reduce($this->hydrated(), function($groups, $icon) {
            $groups[$icon['group']][] = $icon;
            return $groups;
        }, []);
    }

    /**
     * Get the icons formatted for JS
     * @return mixed
     */
    public function forJs() {
        return array_reduce($this->hydrated(), function($icons, $icon) {
            $icons[$icon['group'] . '_' . strtolower($icon['label'])] = $icon['url'];
            return $icons;
        }, []);
    }

    /**
     * Populate an icon with data from the database
     * @param $icon
     * @return mixed
     */
    private function hydrateIcon($icon) {
        $icon['default'] = DT_Genmapper_Metrics::path() . 'includes/charts/church-circles/icons/' . $icon['default'];
        $icon['attachment'] = get_option($icon['option']);
        $icon['url'] = $icon['attachment'] ? wp_get_attachment_url($icon['attachment']) : $icon['default'];
        return $icon;
    }
}
