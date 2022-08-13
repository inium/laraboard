<?php

namespace Database\Factories\Laraboard;

use Illuminate\Database\Eloquent\Factories\Factory;
use Inium\Laraboard\Support\Detect\Agent;
use App\Models\User;
use App\Models\Laraboard\Post;
use App\Models\Laraboard\Board;
use App\Models\Laraboard\Comment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Laraboard\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * 게시글 본문 허용할 태그 목록 (XSS 방지)
     *
     * @var array
     */
    private array $allowTags = [
        "p",
        "br",
        "div",
        "span",
        "hr",
        "a",
        "img",
        "blockquote",
        "ul",
        "ol",
        "li",
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $content = $this->getRandomContent($this->faker->numberBetween(10, 15));

        $ua = Agent::parse($this->faker->userAgent);
        $post = $this->getRandomPost();
        $board = $this->getRandomBoard();
        $user = $this->getRandomUser();

        return [
            "ip_address" => encrypt($this->faker->ipv4),
            "user_agent" => encrypt($ua->agent),
            "device_type" => $ua->device_type,
            "os_name" => $ua->os_name,
            "os_ver" => $ua->os_version,
            "browser_name" => $ua->browser_name,
            "browser_ver" => $ua->browser_version,
            "content" => htmlspecialchars(
                strip_tags($content, $this->allowTags)
            ),
            "stripped_content" => strip_tags($content),
            "points" => $board->comment_points,
            "parent_comment_id" => null,
            "post_id" => $post,
            "board_id" => $board,
            "wrote_user_id" => $user,
        ];
    }

    /**
     * 자식 댓글(2 depth)
     *
     * @param int $parentCommentId  부모 댓글 ID
     * @param int $postId           게시글 ID
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function children(int $parentCommentId, int $postId)
    {
        return $this->state(
            fn(array $attributes) => [
                "parent_comment_id" => $parentCommentId,
                "post_id" => $postId,
            ]
        );
    }

    /**
     * Random User 정보를 가져온다.
     * users 테이블에 사용자 정보가 존재하지 않을 경우 팩토리 생성 후 반환한다.
     *
     * @return mixed
     */
    private function getRandomUser(): mixed
    {
        if ($user = User::inRandomOrder()->first()) {
            return $user;
        }

        return User::factory()->create();
    }

    /**
     * Random Post 정보를 가져온다.
     * lb_board_posts 테이블에 게시글 정보가 존재하지 않을 경우
     * 팩토리 생성 후 반환한다.
     *
     * @return mixed
     */
    public function getRandomPost()
    {
        if ($post = Post::inRandomOrder()->first()) {
            return $post;
        }

        return Post::factory()->create();
    }

    /**
     * 게시판 정보를 반환한다.
     * lb_boards 테이블에 정보가 존재하지 않을 경우 팩토리 생성 후 반환한다.
     *
     * @return mixed
     */
    private function getRandomBoard(): mixed
    {
        if ($board = Board::inRandomOrder()->first()) {
            return $board;
        }

        return Board::factory()->create();
    }

    /**
     * 게시글 본문 생성
     *
     * @param integer $paragraphCount 단락 개수
     * @return string
     */
    private function getRandomContent(int $paragraphCount): string
    {
        $fakerKo = \Faker\Factory::create("ko_KR");
        $content = "";
        for ($i = 0; $i < $paragraphCount; $i++) {
            // $paragraph = $this->faker->realText();
            $paragraph = $fakerKo->realText(200, 1);
            $content .= "<p>{$paragraph}</p>";
        }

        return $content;
    }
}
