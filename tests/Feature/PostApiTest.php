<?php

// tests/Feature/PostApiTest.php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Post;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_posts()
    {
        Post::factory()->count(3)->create();
        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_create_a_post()
    {
        $payload = ['title' => 'Título', 'content' => 'Contenido'];
        $response = $this->postJson('/api/posts', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment($payload);
        $this->assertDatabaseHas('posts', $payload);
    }

    // ... similar tests para update y delete ...
}
