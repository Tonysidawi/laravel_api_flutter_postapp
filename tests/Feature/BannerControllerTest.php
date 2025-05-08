<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BannerControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function authenticated_user_can_create_banner()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'title' => 'Test Banner',
            'body' => 'This is a test banner body.',
        ];

        $response = $this->postJson('/api/banners', $payload);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('banners', [
            'title' => 'Test Banner',
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function it_can_list_all_banners()
    {
        Banner::factory()->count(3)->create();

        $response = $this->getJson('/api/banners');

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function it_can_show_a_single_banner()
    {
        $banner = Banner::factory()->create();

        $response = $this->getJson("/api/banners/{$banner->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => ['id' => $banner->id]
                 ]);
    }

    #[Test]
    public function authenticated_user_can_update_banner()
    {
        $user = User::factory()->create();
        $banner = Banner::factory()->for($user)->create();

        $this->actingAs($user);

        $updateData = [
            'title' => 'Updated Title',
            'body' => 'Updated Body',
        ];

        $response = $this->putJson("/api/banners/{$banner->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('banners', [
            'id' => $banner->id,
            'title' => 'Updated Title',
        ]);
    }

    #[Test]
    public function authenticated_user_can_delete_banner()
    {
        $user = User::factory()->create();
        $banner = Banner::factory()->for($user)->create();

        $this->actingAs($user);

        $response = $this->deleteJson("/api/banners/{$banner->id}");

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('banners', [
            'id' => $banner->id,
        ]);
    }
}
