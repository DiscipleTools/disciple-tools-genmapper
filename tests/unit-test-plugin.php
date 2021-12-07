<?php

class PluginTest extends TestCase
{
    public function test_plugin_installed() {
        activate_plugin( 'disciple-tools-genmapper/disciple-tools-genmapper.php' );

        $this->assertContains(
            'disciple-tools-genmapper/disciple-tools-genmapper.php',
            get_option( 'active_plugins' )
        );
    }
}
