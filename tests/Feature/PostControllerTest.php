<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;

use Tests\TestCase;

use function PHPUnit\Framework\assertJson;

class PostControllerTest extends TestCase
{

    use RefreshDatabase, WithFaker;


    #[Test]
    public function authenticated_user_can_create_post(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $banner = Banner::factory()->for($user)->create();



        $payload = [
            'title' => 'Test Post',
            'body' => 'This is a test post body.',
            'banner_id' => $banner->id,
            'user_id' => $user->id
        ];

        $response = $this->postJson('/api/posts', $payload);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'user_id' => $user->id,
        ]);
    }

    public function it_can_list_all_banners(): void
    {
        $post = Post::factory()->count(3)->create();

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'data');
    }

    Public function it_can_show_a_single_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['id' => $post->id]
            ]);
    }

    public function authenticated_user_can_update_post(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $post = Post::factory()->for($user)->create();

        $updateData = [
            'title' => 'Updated Title',
            'body' => 'Updated Body',
        ];

        $response = $this->putJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
        ]);
    }


        public function authenticated_user_can_delete_post(): void
        {
            $user = User::factory()->create();
            $post = Post::factory()->for($user)->create();

            $this->actingAs($user);

            $response = $this->deleteJson("/api/posts/{$post->id}");

            $response->assertStatus(200)
                ->assertJson(['success' => true]);

            $this->assertDatabaseMissing('posts', [
                'id' => $post->id,
            ]);
        }
    }

