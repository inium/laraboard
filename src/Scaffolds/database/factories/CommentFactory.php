<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Laraboard\Post;
use App\Models\Laraboard\Board;
use App\Models\Laraboard\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $textCount = rand(2, 6);
        $content = "";
        for ($i = 0; $i < $textCount; $i++) {
            $paragraph = $this->faker->realText();
            $content .= "<p>{$paragraph}</p>";
        }

        return [
            "content" => htmlspecialchars($content),
            "content_pure" => strip_tags($content),
            "like_count" => rand(0, 5000),
            "parent_comment_id" => null,
            "post_id" => function () {
                if ($post = Post::inRandomOrder()->first()) {
                    return $post;
                }

                return Post::factory()->create();
            },
            "board_id" => function () {
                if ($board = Board::inRandomOrder()->first()) {
                    return $board;
                }

                return Board::factory()->create();
            },
            "user_id" => function () {
                if ($user = User::inRandomOrder()->first()) {
                    return $user;
                }

                return User::factory()->create();
            },
        ];
    }

    /**
     * 자식 댓글(2 depth)
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function children()
    {
        return $this->state(function (array $attributes) {
            return [
                "parent_comment_id" => function () {
                    $comment = Comment::whereNull("parent_comment_id")
                        ->inRandomOrder()
                        ->first();
                    if ($comment) {
                        return $comment;
                    }

                    return Comment::factory()->create();
                },
            ];
        });
    }
}
