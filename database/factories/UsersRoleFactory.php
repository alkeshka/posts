<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\UsersRole;

class UsersRoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UsersRole::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'role_type' => $this->faker->randomElement(['admin', 'user']),
            'role_name' => $this->faker->randomElement(['Admin', 'User']),
        ];
    }
}
