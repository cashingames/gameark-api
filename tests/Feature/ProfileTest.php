<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Database\Seeders\RewardSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PlanSeeder;
use Tests\TestCase;
use UserSeeder;

class ProfileTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    const PROFILE_DATA_URL = '/api/v3/user/profile';
    const CHANGE_PASSWORD_URL = '/api/v3/profile/me/password/change';
    const UPDATE_REFERRER_URL = '/api/v3/referrer/update';
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->seed(PlanSeeder::class);
        $this->seed(RewardSeeder::class);
        $this->user = User::first();

        $this->actingAs($this->user);
    }

    public function test_relevant_user_profile_can_be_retrieved_with_data()
    {
        $response = $this->withHeaders([
            'x-brand-id' => 10,
        ])->get(self::PROFILE_DATA_URL);
        $response->assertJson([
            'data' => [
                'username' => $this->user->username,
                'email' => $this->user->email,
                'userType' => $this->user->user_type,
                'lastName' => $this->user->profile->last_name,
                'firstName' => $this->user->profile->first_name,
                'points' => $this->user->points(),
                'boosts' => [],
                'hasActivePlan' => false,
                'unreadNotificationsCount' => 0,
                'coinsBalance' => $this->user->getUserCoins(),
            ],
        ]);
    }

    public function test_relevant_user_profile_can_be_retrieved_with_used_boost_count()
    {
        $response = $this->withHeaders([
            'x-brand-id' => 10,
        ])->get(self::PROFILE_DATA_URL);
        $response->assertJson([
            'data' => [
                'usedBoostCount' => $this->user->getUserUsedBoostCount(),
            ],
        ]);
    }

    public function test_personal_details_can_be_edited_with_all_fields_set()
    {

        $this->postjson('/api/v3/profile/me/edit-personal', [
            'username' => 'JayDee',
            'email' => 'johndoe@email.com',
            'gender' => 'male',
            'dateOfBirth' => '23-09-1998',
        ]);
        $this->assertEquals($this->user->username , 'JayDee');
    }

    public function test_username_can_be_edited()
    {

        $this->postjson('/api/v3/profile/me/edit-personal', [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'username' => 'JayDee',
            'phoneNumber' => '09098989898',
            'email' => 'johndoe@email.com',
            'password' => 'password111',
            'gender' => 'male',
            'dateOfBirth' => '23-09-1998',
        ]);
        $this->assertEquals($this->user->username, 'JayDee');
    }

    public function test_email_can_be_edited()
    {

        $this->postjson('/api/v3/profile/me/edit-personal', [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'username' => 'JayDee',
            'phoneNumber' => '09098989898',
            'email' => 'johndoe@email.com',
            'password' => 'password111',
            'gender' => 'male',
            'dateOfBirth' => '23-09-1998',
        ]);
        $this->assertEquals($this->user->email, 'johndoe@email.com');
    }

    public function test_gameark_profile_can_be_edited()
    {
        $response = $this->withHeaders(['x-brand-id' => 10])->postjson('/api/v3/profile/me/edit-personal', [
            'username' => 'JayDee',
            'email' => 'johndoe@email.com',
            'gender' => 'male',
            'dateOfBirth' => '23-09-1998',
        ]);
        $response->assertStatus(200);
    }

    public function test_profile_image_can_be_uploaded()
    {
        Storage::fake('avatars');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $this->post('/api/v3/profile/me/picture', [
            'avatar' => $file,
        ]);

        $avatar = $this->user->profile->avatar;

        $this->assertTrue(!is_null($avatar));
    }

    public function test_that_referrer_profile_returns_null_when_not_referrer_not_found()
    {

        //setup
        $profile = Profile::factory()->for(User::factory())->create([
            'referrer' => 'xxxxabc', //a non existing referrer
        ]);

        //act
        $response = $profile->getReferrerProfile();

        //assert

        $this->assertNull($response);
    }

    public function test_that_referrer_profile_returns_null_when_referrer_is_empty()
    {

        //setup
        $profile = Profile::factory()->for(User::factory())->create(); //no referrer populated

        //act
        $response = $profile->getReferrerProfile();

        //assert

        $this->assertNull($response);
    }

    public function test_that_referrer_profile_returns_value_when_referrer_is_a_valid_username()
    {

        //setup
        $newUser = User::factory()->create();
        $profile = Profile::factory()->for($newUser)->create([
            'referrer' => $this->user->username,
        ]);

        //act
        $response = $profile->getReferrerProfile();

        //assert
        $this->assertEquals($response->first_name, $this->user->profile->first_name);
    }

    public function test_that_referrer_profile_returns_value_when_referrer_is_a_valid_referral_code()
    {

        //setup
        //user1 referred user 2 with old referral code format
        $newUser1 = User::factory()->create();
        $profile1 = Profile::factory()->for($newUser1)->create([
            'referral_code' => "random-referral-code",
        ]);

        $newUser2 = User::factory()->create();
        $profile2 = Profile::factory()->for($newUser2)->create([
            'referrer' => $profile1->referral_code,
        ]);

        //act
        $response = $profile2->getReferrerProfile();

        //assert
        $this->assertEquals($response->first_name, $profile1->first_name);
    }

    public function test_that_referrer_can_be_updated()
    {
        $response = $this->postjson(self::UPDATE_REFERRER_URL, [
            'referrer' => User::skip(1)->first()->username,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => "Profile Updated.",
        ]);
    }
    public function test_that_password_can_be_changed()
    {
        $response = $this->postjson(self::CHANGE_PASSWORD_URL, [
            'password' => 'password',
            'new_password' => 'password123',
            'new_password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Password Changed!.',
        ]);
    }

    public function test_that_old_password_must_be_correct_before_being_changed()
    {
        $response = $this->postjson(self::CHANGE_PASSWORD_URL, [
            'password' => 'password111',
            'new_password' => 'password123',
            'new_password_confirmation' => 'password123',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Old password inputed does not match existing password.',
        ]);
    }

    public function test_that_old_password_must_differ_from_new_password()
    {
        $response = $this->postjson(self::CHANGE_PASSWORD_URL, [
            'password' => 'password111',
            'new_password' => 'password111',
            'new_password_confirmation' => 'password111',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'The new password must be different from the old password.',
        ]);
    }

    public function test_gameark_users_receive_coinsBalance_payload()
    {
        $response = $this->withHeaders([
            'x-brand-id' => 10,
        ])->get(self::PROFILE_DATA_URL);

        $response->assertJson([
            'data' => [
                'coinsBalance' => $this->user->getUserCoins(),
            ],
        ]);
    }
}
