<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $paragraphCount = rand(2, 6);
        $content = $this->getContent($paragraphCount);

        return [
            "subject" => $this->faker->realText(rand(50, 100)),
            "content" => htmlspecialchars($content),
            "content_pure" => strip_tags($content),
            "view_count" => rand(0, 5000),
            "like_count" => rand(0, 5000),
            "board_id" => Board::inRandomOrder()->first(), // test 게시판
            "user_id" => function () {
                if ($user = User::inRandomOrder()->first()) {
                    return $user;
                }

                return User::factory();
            },
        ];
    }

    /**
     * 게시글 본문 생성
     *
     * @param integer $paragraphCount 단락 개수
     * @return string
     */
    private function getContent(int $paragraphCount): string
    {
        $textCount = rand(2, 6);
        $content = "";
        for ($i = 0; $i < $textCount; $i++) {
            $paragraph = $this->faker->realText();
            $content .= "<p>{$paragraph}</p>";
        }

        return $content;
    }
}
