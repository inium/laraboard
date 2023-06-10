<?php

namespace Tests\Feature;

use App\Models\Laraboard\Board;
use App\Models\Laraboard\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostNoticeListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 공지사항 목록 테스트
     * - 성공 200 OK
     *
     * @return void
     */
    public function test_공지사항_목록_성공_200_Ok(): void
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
        $currentPage = 1;
        $lastPage = (int) ceil($notice->count() / $board->posts_per_page);

        $response = $this->getJson(
            route("v1.board.post.index", [
                "boardName" => $board->name,
                "notice" => 1,
            ])
        );

        $response->assertOk()->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    "items",
                    $notice->count(),
                    fn($json) => $json->where("notice", 1)->etc()
                )
                ->where("total", $notice->count())
                ->where("current_page", $currentPage)
                ->where("last_page", $lastPage)
                ->where("per_page", $board->posts_per_page)
        );
    }
}
