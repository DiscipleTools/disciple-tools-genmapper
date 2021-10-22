<?php

class IconsTest extends TestCase {
    public function test_sorts_by_group() {
        $icons_by_group = DT_Genmapper_Plugin_Icons::instance()->by_group();
        $this->assertTrue( is_array( $icons_by_group ) );
        foreach ($icons_by_group as $handle => $group) {
            $this->assertTrue( is_array( $group ) );
            foreach ($group as $icon) {
                $this->assertTrue( $icon['group']['handle'] === $handle );
            }
        }
    }
}
