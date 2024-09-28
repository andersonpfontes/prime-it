<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Veterinarian;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentControllerTest extends TestCase
{
    use RefreshDatabase;


    /**
     * Autentica o usuário e retorna o token JWT.
     */
    public function authenticate()
    {
        // Cria um usuário para autenticação
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        // Faz o login e obtém o token
        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        // Certifique-se de que o login foi bem-sucedido
        $response->assertStatus(200);

        // Retorna o token
        return $response->json('data.access_token');
    }

    public function test_store_appointment()
    {
        $token = $this->authenticate(); // Autentica o usuário e obtém o token
        $veterinarian = Veterinarian::factory()->create(); // Veterinário existente

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/appointments', [
                'person_name' => 'John Doe',
                'email' => 'john@example.com',
                'animal_name' => 'Buddy',
                'animal_type' => 'dog',
                'age' => 3,
                'symptoms' => 'Coughing',
                'appointment_date' => '2024-09-30',
                'period' => 'morning',
                'veterinarian_id' => $veterinarian->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Appointment created successfully.'
            ]);

        $this->assertDatabaseHas('appointments', [
            'person_name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }


    public function test_index_appointments()
    {
        $token = $this->authenticate();
        Appointment::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/appointments');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Appointments retrieved successfully.'
            ])
            ->assertJsonCount(3, 'data');
    }


    public function test_update_appointment()
    {
        $token = $this->authenticate();
        $appointment = Appointment::factory()->create();
        $newVeterinarian = Veterinarian::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/appointments/' . $appointment->id, [
                'person_name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'animal_name' => 'Kitty',
                'animal_type' => 'cat',
                'age' => 2,
                'symptoms' => 'Fever',
                'appointment_date' => '2024-10-01',
                'period' => 'afternoon',
                'veterinarian_id' => $newVeterinarian->id,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Appointment updated successfully.'
            ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'person_name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);
    }



    public function test_delete_appointment()
    {
        $token = $this->authenticate();
        $appointment = Appointment::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/appointments/' . $appointment->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('appointments', [
            'id' => $appointment->id,
        ]);
    }


    public function test_get_appointments_by_veterinarian()
    {
        $user = User::factory()->create();
        $veterinarian = Veterinarian::factory()->create(['user_id' => $user->id]);

        // Cria algumas marcações atribuídas ao veterinário
        Appointment::factory()->count(2)->create(['veterinarian_id' => $veterinarian->id]);

        // Cria outras marcações não atribuídas ao veterinário
        Appointment::factory()->count(3)->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/veterinarian/appointments');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Appointments retrieved successfully.'
            ])
            ->assertJsonCount(2, 'data'); // Certifica-se de que apenas 2 marcações estão retornando para este veterinário
    }
}
