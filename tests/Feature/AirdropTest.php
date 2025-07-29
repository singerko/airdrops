<?php
// tests/Feature/AirdropTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Airdrop;
use App\Models\Project;
use App\Models\Blockchain;
use App\Models\AirdropCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AirdropTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();
    }

    /** @test */
    public function users_can_view_published_airdrops()
    {
        $blockchain = Blockchain::factory()->create();
        $category = AirdropCategory::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id]);
        
        $airdrop = Airdrop::factory()->create([
            'project_id' => $project->id,
            'blockchain_id' => $blockchain->id,
            'status' => 'active',
            'published_at' => now(),
        ]);

        $response = $this->get(route('airdrops.index'));
        
        $response->assertStatus(200);
        $response->assertSee($airdrop->title);
    }

    /** @test */
    public function users_cannot_view_draft_airdrops()
    {
        $blockchain = Blockchain::factory()->create();
        $category = AirdropCategory::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id]);
        
        $airdrop = Airdrop::factory()->create([
            'project_id' => $project->id,
            'blockchain_id' => $blockchain->id,
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->get(route('airdrops.show', $airdrop->slug));
        
        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_users_can_subscribe_to_airdrops()
    {
        $user = User::factory()->create();
        $blockchain = Blockchain::factory()->create();
        $category = AirdropCategory::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id]);
        
        $airdrop = Airdrop::factory()->create([
            'project_id' => $project->id,
            'blockchain_id' => $blockchain->id,
            'status' => 'active',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('airdrops.subscribe', $airdrop));
        
        $response->assertSuccessful();
        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $user->id,
            'airdrop_id' => $airdrop->id,
        ]);
    }

    /** @test */
    public function admin_can_create_airdrops()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $blockchain = Blockchain::factory()->create();
        $category = AirdropCategory::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id]);

        $airdropData = [
            'title' => 'Test Airdrop',
            'project_id' => $project->id,
            'blockchain_id' => $blockchain->id,
            'description' => 'Test description',
            'status' => 'upcoming',
            'estimated_value' => 1000,
        ];

        $response = $this->actingAs($admin)->post(route('admin.airdrops.store'), $airdropData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('airdrops', [
            'title' => 'Test Airdrop',
            'project_id' => $project->id,
        ]);
    }

    /** @test */
    public function regular_users_cannot_access_admin_panel()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        
        $response->assertStatus(403);
    }
}
