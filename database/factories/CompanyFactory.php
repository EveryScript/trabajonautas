<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_name' => fake()->sentence(2),
            'description' => fake()->sentence(20),
            'company_image' => 'empresas/tbn-new-default.webp',
            'company_type_id' => fake()->numberBetween(1, 3),
            'user_id' => User::where('email', 'ricardooropeza15@gmail.com')->first()->id
        ];
    }
}
