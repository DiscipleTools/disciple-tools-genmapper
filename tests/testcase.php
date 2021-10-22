<?php

abstract class TestCase extends WP_UnitTestCase {
    public function setUp() {
        global $wpdb;
        $wpdb->query( 'START TRANSACTION' );
        parent::setUp();
    }

    public function tearDown() {
        global $wpdb;
        $wpdb->query( 'ROLLBACK' );
        parent::tearDown();
    }
}
