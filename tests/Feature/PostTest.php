<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function testsPostsAreCreatedCorrectly()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api');
        $payload = [
            'title'       => 'Lorem',
            'description' => 'Ipsum',
            'user_id'     => $user->id,
        ];

        $this->json('POST', '/api/posts', $payload)
            ->assertStatus(201)
            ->assertJson(['success' => true]);
    }

    public function testsPostsAreUpdatedCorrectly()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $post = Post::factory()->create([
            'title'       => 'First Post',
            'description' => 'First Body',
            'user_id'     => $user->id,
        ]);

        $payload = [
            'title'       => 'Lorem',
            'description' => 'Ipsum',
        ];

        $response = $this->json('PUT', '/api/posts/' . $post->id, $payload)
            ->assertStatus(204)
            ->assertJson(['success' => true]);
    }

    public function testsPostsAreDeletedCorrectly()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $post = Post::factory()->create([
            'title'       => 'First Post',
            'description' => 'First Body',
            'user_id' => 1,
        ]);

        $this->json('DELETE', '/api/posts/' . $post->id, [])
            ->assertStatus(204);
    }

    public function testPostsAreListedCorrectly()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Post::factory()->create([
            'title'       => 'First Post',
            'description' => 'First Body',
            'user_id'     => $user->id,
            'slug'        => 'first-post',
        ]);

        Post::factory()->create([
            'title'       => 'Second Post',
            'description' => 'Second Body',
            'user_id'     => $user->id,
            'slug'        => 'second-post',
        ]);

        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->json('GET', '/api/posts', [])
            ->assertStatus(200)
            ->assertJson([
                ['id' => 1, 'title' => 'First Post', 'slug' => 'first-post', 'description' => 'First Body', 'user_id' => "1"],
                ['id' => 2, 'title' => 'Second Post', 'slug' => 'second-post', 'description' => 'Second Body', 'user_id' => "1"],
            ])
            ->assertJsonStructure([
                '*' => ['id', 'description', 'title', 'slug', 'user_id', 'created_at', 'updated_at'],
            ]);
    }
}
