<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetPotentialJobs()
    {

        // this is test for getPotentialJobs method which is in BookingController

        $cuser = factory(User::class)->create();

        $cuserMeta = factory(UserMeta::class)->create(['user_id' => $cuser->id]);

        $languages = factory(UserLanguages::class, 3)->create(['user_id' => $cuser->id]);

        $response = $this->get('/get-potential-jobs');

        $response->assertStatus(200);

        $response->assertJsonCount(3);
    }


    public function testDistanceFeed()
    {

        // this is test for distanceFeed method which is in BookingController

        $data = [
            'distance' => '100',
            'time' => '2 hours',
            'jobid' => 1,
            'session_time' => '3 hours',
            'flagged' => 'true',
            'admincomment' => 'This job needs attention',
            'manually_handled' => 'true',
            'by_admin' => 'true',
        ];

        $response = $this->post('/distance-feed', $data); 

        // Assertions
        $response->assertStatus(200);
        $this->assertDatabaseHas('distances', [
            'job_id' => 1,
            'distance' => '100',
            'time' => '2 hours',
        ]);
        $this->assertDatabaseHas('jobs', [
            'id' => 1,
            'admin_comments' => 'This job needs attention',
            'flagged' => 'yes',
            'session_time' => '3 hours',
            'manually_handled' => 'yes',
            'by_admin' => 'yes',
        ]);
    }

}

// -----------------------------Same like others i can also make someother tests for that-------------------------------- 