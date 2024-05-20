<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Comments;
use App\Models\Post;
use App\Models\User;

class CommentsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comments::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'body' => $this->faker->text(),
            'user_id' => User::factory(),
            'post_id' => Post::factory(),
        ];
    }
}
