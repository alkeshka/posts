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
            'role_type' => $this->faker->regexify('[A-Za-z0-9]{20}'),
            'role_name' => $this->faker->regexify('[A-Za-z0-9]{20}'),
        ];
    }
}
