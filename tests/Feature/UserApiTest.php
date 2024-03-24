<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

# php artisan test --filter=UserApiTest
class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    # php artisan test --filter=UserApiTest::test_positive_for_validating_required_fields_for_storing_user --env=testing
    public function test_positive_for_validating_required_fields_for_storing_user()
    {
        $newUser = User::factory()->make()->toArray();

        $newUser['password'] = '123456';
        $newUser['password_confirmation'] = '123456';

        $response = $this->postJson('/api/user', $newUser);

        $response->assertStatus(201);

        unset($newUser['password_confirmation'],$newUser['password'],$newUser['email_verified_at']);

        $this->assertDatabaseHas('users',$newUser);
        $this->assertEquals('success',$response->original['status'], 'The status must be "success"');
    }

    /** @test */
    # php artisan test --filter=UserApiTest::test_negative_for_validating_required_fields_for_storing_user --env=testing
    public function test_negative_for_validating_required_fields_for_storing_user()
    {
        $newUser = User::factory()->make()->toArray();


        $response = $this->postJson('/api/user', $newUser);
        $response->assertStatus(422);

        $this->assertEquals('failed',$response->original['status'], 'The status must be "failed"');
        $this->assertEquals('Validate Field(s) fail.',$response->original['message'], 'The message must be "Validate Field(s) fail."');
    }

    /** @test */
    # php artisan test --filter=UserApiTest::test_negative_for_validating_required_fields_for_changing_password --env=testing
    public function test_negative_for_validating_required_fields_for_changing_password()
    {
        $response = $this->postJson('/api/user/change-password', []);

        $response->assertStatus(422);

        $responseArray = $response->json();
        $this->assertArrayHasKey('status', $responseArray);
        $this->assertArrayHasKey('message', $responseArray);
        $this->assertArrayHasKey('data', $responseArray);
        $this->assertEquals('The id field is required.',$response->original['data']['id'][0], 'The message must be "The id field is required."');
        $this->assertEquals('The password field is required.',$response->original['data']['password'][0], 'The message must be "The password field is required."');
        $this->assertEquals('The new password field is required.',$response->original['data']['new_password'][0], 'The message must be "he new password field is required."');
    }

    /** @test */
    # php artisan test --filter=UserApiTest::test_positive_for_delete_user --env=testing
    public function test_positive_for_delete_user()
    {
        $id = User::query()->max('id') + 1;

        $response = $this->deleteJson("/api/user/$id");

        $response->assertStatus(202);
    }

    /** @test */
    # php artisan test --filter=UserApiTest::test_negative_for_delete_user --env=testing
    public function test_negative_for_delete_user()
    {
        $id = null;

        $response = $this->deleteJson("/api/user/$id");

        $response->assertStatus(405);
    }

}
