<?php

namespace Tests\Feature;

use App\Models\Laraboard\Comment;
use App\Models\Laraboard\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CommentListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 댓글 목록 성공 테스트
     * - 성공 200 OK
     *
     * @return void
     */
    public function test_댓글_목록_성공_200_Ok(): void
    {
        $post = Post::factory()->create();
        $comments = Comment::factory(3)->create();

        // 자식 댓글 생성
        foreach ($comments as $v) {
            Comment::factory(mt_rand(1, 5))
                ->children($v->id, $v->post_id)
                ->create();
        }

        $response = $this->getJson(
            route("v1.board.post.comment.index", [
                "boardName" => $post->board->name,
                "postId" => $post->id,
            ])
        );

        // 총 페이지 수 계산
        $currentPage = 1;
        $lastPage = (int) ceil(
            $comments->count() / $post->board->comments_per_page
        );

        $response->assertOk()->assertJson(
            fn(AssertableJson $json) => $json
                ->has("items", $comments->count())
                ->where("total", $comments->count())
                ->where("current_page", $currentPage)
                ->where("last_page", $lastPage)
                ->where("per_page", $post->board->comments_per_page)
        );
    }

    /**
     * 자식 댓글 목록 성공 테스트
     * - 성공 200 OK
     *
     * @return void
     */
    public function test_자식댓글_목록_성공_200_Ok(): void
    {
        $numOfComments = mt_rand(3, 152);

        $parent = Comment::factory()->create();
        Comment::factory($numOfComments)
            ->children($parent->id, $parent->post->id)
            ->create();

        // 총 페이지 수 계산
        $commentsPerPage = $parent->post->board->comments_per_page;
        $currentPage = 1;
        $lastPage = (int) ceil($numOfComments / $commentsPerPage);

        $response = $this->getJson(
            route("v1.board.post.comment.index", [
                "boardName" => $parent->board->name,
                "postId" => $parent->post->id,
                "parent" => $parent->id,
                "page" => $currentPage,
            ])
        );

        $response->assertOk()->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    "items",
                    $numOfComments <= $commentsPerPage
                        ? $numOfComments
                        : $commentsPerPage
                )
                ->where("total", $numOfComments)
                ->where("current_page", $currentPage)
                ->where("last_page", $lastPage)
                ->where("per_page", $parent->post->board->comments_per_page)
        );
    }
}
