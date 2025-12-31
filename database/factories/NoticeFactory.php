<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notice>
 */
class NoticeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'description' => fake()->sentence(10),
            'image' => 'noticias/default.webp',
            'link' => fake()->url(),
            'user_id' => User::where('email', 'ricardooropeza15@gmail.com')->first()->id
        ];
    }
}
