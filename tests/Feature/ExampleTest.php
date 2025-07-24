<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_user_can_register_and_login()
{
    $userData = ['name' => 'Alice', 'email' => 'alice@example.com', 'password' => 'password', 'password_confirmation' => 'password'];
    $this->postJson('/api/register', $userData)
         ->assertStatus(201)
         ->assertJsonStructure(['user', 'access_token']);

    $this->postJson('/api/login', ['email' => 'alice@example.com', 'password' => 'password'])
         ->assertStatus(200)
         ->assertJsonStructure(['user', 'access_token']);

    $token = User::first()->tokens()->first()->plainTextToken;
    $this->withHeaders(['Authorization' => "Bearer {$token}"])
         ->getJson('/api/posts')
         ->assertStatus(200);
}

}
