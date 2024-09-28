<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Veterinarian;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition()
    {
        return [
            'person_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'animal_name' => $this->faker->randomElement(['Buddy', 'Max', 'Bella', 'Charlie', 'Lucy']),
            'animal_type' => $this->faker->randomElement(['dog', 'cat', 'bird', 'reptile']),
            'age' => $this->faker->numberBetween(1, 15),
            'symptoms' => $this->faker->sentence,
            'appointment_date' => $this->faker->date,
            'period' => $this->faker->randomElement(['morning', 'afternoon']),
            'user_id' => User::factory(), // Cria um usuário associado
            'veterinarian_id' => Veterinarian::factory(), // Cria um veterinário associado
        ];
    }
}
