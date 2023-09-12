<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLevelTest extends TestCase
{

    
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->user = User::first();
        $this->actingAs($this->user);
    }
    /**
     * A basic feature test example.
     */
    public function test_that_user_level_endpoint_is_working()
    {
        $response = $this->get('/api/v3/trivia-quest/level');
        $response->assertStatus(200);
    }
}
