<?php

namespace Database\Factories;

use App\Models\Veterinarian;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VeterinarianFactory extends Factory
{
    protected $model = Veterinarian::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'specialization' => $this->faker->randomElement(['Dogs', 'Cats', 'Birds', 'Reptiles', 'Small Animals']),
            'user_id' => User::factory(),
        ];
    }
}
