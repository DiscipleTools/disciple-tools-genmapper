<?php

class CountsTest extends TestCase
{
    protected $plugin;

    public $sample_contact = [
        'title' => 'Bob',
        'overall_status' => 'active',
        'milestones' => [ "values" => [ [ "value" => 'milestone_has_bible' ], [ "value" => "milestone_baptizing" ] ] ],
        'baptism_date' => "2018-12-31",
        "location_grid" => [ "values" => [ [ "value" => '100089589' ] ] ],
        "assigned_to" => "1",
        "requires_update" => true,
        "nickname" => "Bob the builder",
        "contact_phone" => [ "values" => [ [ "value" => "798456780" ] ] ],
        "contact_email" => [ "values" => [ [ "value" => "bob@example.com" ] ] ],
        "tags" => [ "values" => [ [ "value" => "tag1" ] ] ],
    ];

    public function test_milestone_counts() {
        $user_id = wp_create_user( "user3", "test", "test3@example.com" );
        wp_set_current_user( $user_id );
        $current_user = wp_get_current_user();
        $current_user->set_role( 'dispatcher' );
        $contact2_fields = [
            'title' => 'contact 2',
        ];

        $contact1 = DT_Posts::create_post( 'contacts', $this->sample_contact );
        $contact2 = DT_Posts::create_post( 'contacts', $contact2_fields );
        //create group with contact1 as member
        $group1 = DT_Posts::create_post( 'groups', [
            'title'   => 'group1',
            "members" => [ "values" => [ [ "value" => $contact1["ID"] ], [ "value" => $contact2["ID"] ] ] ]
        ] );
        $milestones1 = get_post_meta( $contact1["ID"], 'milestones' );
        $milestones2 = get_post_meta( $contact2["ID"], 'milestones' );
        $group1_baptism_count = (int) get_post_meta( $group1["ID"], 'baptized_count', true );
        $group1_belief_count = (int) get_post_meta( $group1["ID"], 'believer_count', true );

        $this->assertFalse( in_array( 'milestone_baptized', $milestones1 ) );
        $this->assertFalse( in_array( 'milestone_belief', $milestones1 ) );
        $this->assertFalse( in_array( 'milestone_baptized', $milestones2 ) );
        $this->assertFalse( in_array( 'milestone_belief', $milestones2 ) );
        $this->assertEquals( 0, $group1_baptism_count );
        $this->assertEquals( 0, $group1_belief_count );

        add_post_meta( $contact1["ID"], 'milestones', 'milestone_baptized' );
        add_post_meta( $contact2["ID"], 'milestones', 'milestone_belief' );

        $milestones1 = get_metadata( 'post', $contact1["ID"], 'milestones' );
        $milestones2 = get_metadata( 'post', $contact2["ID"], 'milestones' );
        $group1_baptism_count = (int) get_post_meta( $group1["ID"], 'baptized_count', true );
        $group1_belief_count = (int) get_post_meta( $group1["ID"], 'believer_count', true );

        $this->assertTrue( in_array( 'milestone_baptized', $milestones1 ) );
        $this->assertFalse( in_array( 'milestone_belief', $milestones1 ) );
        $this->assertFalse( in_array( 'milestone_baptized', $milestones2 ) );
        $this->assertTrue( in_array( 'milestone_belief', $milestones2 ) );
        $this->assertEquals( 1, $group1_baptism_count );
        $this->assertEquals( 1, $group1_belief_count );

        add_post_meta( $contact2["ID"], 'milestones', 'milestone_baptized' );

        $milestones1 = get_metadata( 'post', $contact1["ID"], 'milestones' );
        $milestones2 = get_metadata( 'post', $contact2["ID"], 'milestones' );
        $group1_baptism_count = (int) get_post_meta( $group1["ID"], 'baptized_count', true );
        $group1_belief_count = (int) get_post_meta( $group1["ID"], 'believer_count', true );

        $this->assertTrue( in_array( 'milestone_baptized', $milestones1 ) );
        $this->assertFalse( in_array( 'milestone_bphjpelief', $milestones1 ) );
        $this->assertTrue( in_array( 'milestone_baptized', $milestones2 ) );
        $this->assertTrue( in_array( 'milestone_belief', $milestones2 ) );
        $this->assertEquals( 2, $group1_baptism_count );
        $this->assertEquals( 1, $group1_belief_count );

        //test handling manually set count
        update_post_meta( $group1["ID"], 'believer_count', 5 );

        $group1_belief_count = (int) get_post_meta( $group1["ID"], 'believer_count', true );
        $this->assertEquals( 5, $group1_belief_count );

        add_post_meta( $contact1["ID"], 'milestones', 'milestone_belief' );

        $milestones1 = get_metadata( 'post', $contact1["ID"], 'milestones' );
        $milestones2 = get_metadata( 'post', $contact2["ID"], 'milestones' );
        $group1_baptism_count = (int) get_post_meta( $group1["ID"], 'baptized_count', true );
        $group1_belief_count = (int) get_post_meta( $group1["ID"], 'believer_count', true );

        $this->assertTrue( in_array( 'milestone_baptized', $milestones1 ) );
        $this->assertTrue( in_array( 'milestone_belief', $milestones1 ) );
        $this->assertTrue( in_array( 'milestone_baptized', $milestones2 ) );
        $this->assertTrue( in_array( 'milestone_belief', $milestones2 ) );
        $this->assertEquals( 2, $group1_baptism_count );
        $this->assertEquals( 5, $group1_belief_count );

        //Delete
        delete_post_meta( $contact2["ID"], 'milestones', 'milestone_baptized' );

        $milestones1 = get_metadata( 'post', $contact1["ID"], 'milestones' );
        $milestones2 = get_metadata( 'post', $contact2["ID"], 'milestones' );
        $group1_baptism_count = (int) get_post_meta( $group1["ID"], 'baptized_count', true );
        $group1_belief_count = (int) get_post_meta( $group1["ID"], 'believer_count', true );

        $this->assertTrue( in_array( 'milestone_baptized', $milestones1 ) );
        $this->assertTrue( in_array( 'milestone_belief', $milestones1 ) );
        $this->assertFalse( in_array( 'milestone_baptized', $milestones2 ) );
        $this->assertTrue( in_array( 'milestone_belief', $milestones2 ) );
        $this->assertEquals( 1, $group1_baptism_count );
        $this->assertEquals( 2, $group1_belief_count );
    }
}
