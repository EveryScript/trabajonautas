<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        $admin = User::where('email', 'ricardooropeza15@gmail.com')->first();

        return [
            'announce_title' => fake()->sentence(5),
            'description' => fake()->paragraph(30),
            'expiration_time' => now()->addDays(fake()->numberBetween(5, 30)),
            'salary' => fake()->numberBetween(1500, 60000),
            'pro' => fake()->boolean(),
            'area_id' => Area::inRandomOrder()->first()?->id,
            'user_id' => $admin->id,
            'company_id' => Company::inRandomOrder()->first()?->id,
        ];
    }
}
