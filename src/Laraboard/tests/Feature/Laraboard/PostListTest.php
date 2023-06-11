<?php

namespace Tests\Feature\Laraboard;

use App\Models\Laraboard\Board;
use App\Models\Laraboard\Post;
use Inium\Laraboard\Support\Traits\Tests\RecursiveRefreshDatabaseTrait as RefreshDatabase;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class PostListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 게시글 목록 테스트
     * - 성공 200 OK
     *
     * @return void
     */
    public function test_게시글_목록_성공_200_Ok(): void
    {
        // 게시판, 게시글, 공지사항 생성
        $numOfPosts = 53;
        $numOfNotices = 5;

        $board = Board::factory()->create();
        $posts = Post::factory($numOfPosts)->create();
        $notice = Post::factory($numOfNotices)
            ->notice()
            ->create();

        // 총 페이지 수 계산
        $currentPage = 2;
        $lastPage = (int) ceil($posts->count() / $board->posts_per_page);

        $response = $this->getJson(
            route("v1.board.post.index", [
                "boardName" => $board->name,
                "page" => $currentPage,
            ])
        );

        $response->assertOk()->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    "items",
                    $board->posts_per_page,
                    fn($json) => $json->where("notice", 0)->etc()
                )
                ->where("total", $posts->count())
                ->where("current_page", $currentPage)
                ->where("last_page", $lastPage)
                ->where("per_page", $board->posts_per_page)
        );
    }
}
