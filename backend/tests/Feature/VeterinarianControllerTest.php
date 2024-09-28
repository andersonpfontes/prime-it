<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Veterinarian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VeterinarianControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_veterinarian()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/veterinarians', [
            'name' => 'Dr. John',
            'email' => 'drjohn@example.com',
            'specialization' => 'Small Animals',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Veterinarian created successfully.'
            ]);

        $this->assertDatabaseHas('veterinarians', [
            'name' => 'Dr. John',
            'email' => 'drjohn@example.com',
        ]);
    }

    public function test_index_veterinarians()
    {
        $user = User::factory()->create();
        Veterinarian::factory()->count(2)->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/veterinarians');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Veterinarians retrieved successfully.'
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_update_veterinarian()
    {
        $user = User::factory()->create();
        $veterinarian = Veterinarian::factory()->create();

        $response = $this->actingAs($user, 'api')->putJson('/api/veterinarians/' . $veterinarian->id, [
            'name' => 'Dr. Jane',
            'email' => 'drjane@example.com',
            'specialization' => 'Cats',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Veterinarian updated successfully.'
            ]);

        $this->assertDatabaseHas('veterinarians', [
            'id' => $veterinarian->id,
            'name' => 'Dr. Jane',
            'email' => 'drjane@example.com',
        ]);
    }

    public function test_delete_veterinarian()
    {
        $user = User::factory()->create();
        $veterinarian = Veterinarian::factory()->create();

        $response = $this->actingAs($user, 'api')->deleteJson('/api/veterinarians/' . $veterinarian->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('veterinarians', [
            'id' => $veterinarian->id,
        ]);
    }
}
