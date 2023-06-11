<?php

namespace Tests\Feature\Laraboard;

use App\Models\Laraboard\Board;
use App\Models\Laraboard\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Inium\Laraboard\Support\Traits\Tests\RecursiveRefreshDatabaseTrait as RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostNoticeUpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * 공지글 수정 테스트
     * - 성공 200 OK
     *
     * @return void
     */
    public function test_공지글_수정_200_Ok(): void
    {
        $post = Post::factory()
            ->notice()
            ->create();

        $formData = [
            "subject" => $this->randomHtmlSentences(rand(1, 3)),
            "content" => $this->randomHtmlSentences(rand(20, 50)),
            "notice" => false,
        ];

        $response = $this->actingAs($post->user)->putJson(
            route("v1.board.post.update", [
                "boardName" => $post->board->name,
                "postId" => $post->id,
            ]),
            $formData
        );

        $response->assertOk();

        // 게시글이 수정되었는지 확인
        $isExist =
            Post::where([
                ["wrote_user_id", "=", $post->user->id],
                ["notice", "=", $formData["notice"]],
                ["subject", "=", strip_tags($formData["subject"])],
            ])->count() > 0;

        $this->assertTrue($isExist);
    }

    /**
     * 공지글 수정 테스트 실패
     * - 사용자 정보 없음 401 Unauthorized
     *
     * @return void
     */
    public function test_공지글_수정_실패_작성자_정보_없음_401_Unauthorized(): void
    {
        $post = Post::factory()->create();

        $formData = [
            "subject" => $this->randomHtmlSentences(rand(1, 3)),
            "content" => $this->randomHtmlSentences(rand(20, 50)),
            "notice" => false,
        ];

        $response = $this->putJson(
            route("v1.board.post.update", [
                "boardName" => $post->board->name,
                "postId" => $post->id,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * 공지글 수정 테스트 실패
     * - 사용자 정보 불치 401 Unauthorized
     *
     * @return void
     */
    public function test_공지글_수정_실패_게시글_작성자_정보_불일치_401_Unauthorized(): void
    {
        $post = Post::factory()
            ->notice()
            ->create();
        $user = User::factory()->create();

        $formData = [
            "subject" => $this->randomHtmlSentences(rand(1, 3)),
            "content" => $this->randomHtmlSentences(rand(20, 50)),
        ];

        $response = $this->actingAs($user)->putJson(
            route("v1.board.post.update", [
                "boardName" => $post->board->name,
                "postId" => $post->id,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * 공지글 수정 테스트 실패
     * - 잘못된 게시판 404 Not Found (form validation fail)
     *
     * @return void
     */
    public function test_공지글_수정_실패_잘못된_게시판_404_Not_Found(): void
    {
        $post = Post::factory()
            ->notice()
            ->create();
        $board = Board::factory()->create();

        $formData = [
            "subject" => $this->randomHtmlSentences(rand(1, 3)),
            "content" => $this->randomHtmlSentences(rand(20, 50)),
        ];

        $response = $this->actingAs($post->user)->putJson(
            route("v1.board.post.update", [
                "boardName" => $board->name,
                "postId" => $post->id,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * 공지글 수정 테스트 실패
     * - 공지여부 잘못된 자료형 값 422_Unprocessable_Entity (form validation fail)
     *
     * @return void
     */
    public function test_공지글_수정_실패_공지여부_잘못된값_422_Unprocessable_Entity(): void
    {
        $post = Post::factory()
            ->notice()
            ->create();

        $formData = [
            "subject" => $this->randomHtmlSentences(rand(1, 3)),
            "content" => $this->randomHtmlSentences(rand(20, 50)),
            "notice" => $this->faker->word,
        ];

        $response = $this->actingAs($post->user)->putJson(
            route("v1.board.post.update", [
                "boardName" => $post->board->name,
                "postId" => $post->id,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * 공지글 수정 테스트 실패
     * - 제목 없음 422_Unprocessable_Entity(form validation fail)
     *
     * @return void
     */
    public function test_공지글_수정_실패_제목없음_422_Unprocessable_Entity(): void
    {
        $post = Post::factory()
            ->notice()
            ->create();

        $formData = [
            // "subject" => $this->randomHtmlSentences(rand(1, 3)),
            "content" => $this->randomHtmlSentences(rand(20, 50)),
            "notice" => true,
        ];

        $response = $this->actingAs($post->user)->putJson(
            route("v1.board.post.update", [
                "boardName" => $post->board->name,
                "postId" => $post->id,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * 공지글 수정 테스트 실패
     * - 본문 없음 422_Unprocessable_Entity (form validation fail)
     *
     * @return void
     */
    public function test_공지글_수정_실패_본문없음_422_Unprocessable_Entity(): void
    {
        $post = Post::factory()
            ->notice()
            ->create();

        $formData = [
            "subject" => $this->randomHtmlSentences(rand(1, 3)),
            // "content" => $this->randomHtmlSentences(rand(20, 50)),
            "notice" => true,
        ];

        $response = $this->actingAs($post->user)->postJson(
            route("v1.board.post.store", [
                "boardName" => $post->board->name,
                "postId" => $post->id,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * 문장 목록을 이용해 <p></p> 로 Wrapping된 Html 문자열을 생성한다
     *
     * @param integer $numOfSentences   생성할 문장 개수
     * @return string HTML 문자열
     */
    private function randomHtmlSentences(int $numOfSentences = 1): string
    {
        $paragraphs = $this->faker->sentences($numOfSentences);
        $post = "";

        foreach ($paragraphs as $para) {
            $post .= "<p>{$para}</p>";
        }

        return $post;
    }
}
