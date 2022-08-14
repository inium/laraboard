<?php

namespace Database\Factories\Laraboard;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Laraboard\Board>
 */
class BoardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $randomDigit = $this->faker->numberBetween();

        return [
            "name" => strip_tags("board{$randomDigit}"),
            "name_ko" => strip_tags("게시판{$randomDigit}"),
            "description" => strip_tags(
                "자동으로 생성한 게시판{$randomDigit} 입니다."
            ),
            "post_points" => $this->faker->numberBetween(50, 100),
            "comment_points" => $this->faker->numberBetween(1, 50),
            "posts_per_page" => $this->faker->randomElement([1, 10, 20, 30]),
            "comments_per_page" => $this->faker->randomElement([50, 100, 150]),
        ];
    }
}
