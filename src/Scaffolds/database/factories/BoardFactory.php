<?php

namespace Database\Factories\Laraboard;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Board>
 */
class BoarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "name" => $this->faker->realText(
                $this->faker->numberBetween(4, 10)
            ),
            "name_ko" => "테스트게시판",
            "description" => "테스트 게시판 입니다",
            "post_point" => $this->faker->number,
            "comment_point" => 5,
            "posts_per_page" => 20,
            "comments_per_page" => 200,
        ];

        // return [
        //     "name" => "test",
        //     "name_ko" => "테스트게시판",
        //     "description" => "테스트 게시판 입니다",
        //     "post_point" => 50,
        //     "comment_point" => 5,
        //     "posts_per_page" => 20,
        //     "comments_per_page" => 200,
        // ];
    }
}
