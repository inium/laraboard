<?php

namespace Tests\Feature;

use App\Models\Laraboard\Board;
use App\Models\Laraboard\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class PostNoticeStoreTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * 공지글 쓰기 테스트
     * - 성공 200 OK
     *
     * @return void
     */
    public function test_공지글_쓰기_성공_201_Created(): void
    {
        $board = Board::factory()->create();
        $user = User::factory()->create();

        $formData = [
            "subject" => $this->randomHtmlSentences(rand(1, 3)),
            "content" => $this->randomHtmlSentences(rand(20, 50)),
            "notice" => true,
        ];

        $response = $this->actingAs($user)->postJson(
            route("v1.board.post.store", [
                "boardName" => $board->name,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_CREATED);

        // 게시글이 저장되었는지 확인
        $isExist =
            Post::where([
                ["wrote_user_id", "=", $user->id],
                ["notice", "=", $formData["notice"]],
                ["subject", "=", strip_tags($formData["subject"])],
            ])->count() > 0;

        $this->assertTrue($isExist);
    }

    /**
     * 공지글 쓰기 테스트 실패 - 작성자 정보 없음 401 Unauthorized
     *
     * @return void
     */
    public function test_공지글_쓰기_실패_작성자_정보_없음_401_Unauthorized(): void
    {
        $board = Board::factory()->create();

        $formData = [
            "subject" => $this->randomHtmlSentences(rand(1, 3)),
            "content" => $this->randomHtmlSentences(rand(20, 50)),
            "notice" => true,
        ];

        $response = $this->postJson(
            route("v1.board.post.store", [
                "boardName" => $board->name,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * 공지글 쓰기 테스트 실패
     * - 공지여부 잘못된 자료형 값 422 Unprocessable Entity (form validation fail)
     *
     * @return void
     */
    public function test_공지글_쓰기_실패_공지여부_잘못된값_422_Unprocessable_Entity(): void
    {
        $board = Board::factory()->create();
        $user = User::factory()->create();

        $formData = [
            "subject" => $this->randomHtmlSentences(rand(1, 3)),
            "content" => $this->randomHtmlSentences(rand(20, 50)),
            "notice" => $this->faker->word,
        ];

        $response = $this->actingAs($user)->postJson(
            route("v1.board.post.store", [
                "boardName" => $board->name,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * 공지글 쓰기 테스트 실패
     * - 제목 없음 422_Unprocessable_Entity (form validation fail)
     *
     * @return void
     */
    public function test_공지글_쓰기_실패_제목없음_422_Unprocessable_Entity(): void
    {
        $board = Board::factory()->create();
        $user = User::factory()->create();

        $formData = [
            "subject" => "",
            "content" => $this->randomHtmlSentences(rand(20, 50)),
            "notice" => true,
        ];

        $response = $this->actingAs($user)->postJson(
            route("v1.board.post.store", [
                "boardName" => $board->name,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * 공지글 쓰기 테스트 실패
     * - 본문 없음 422_Unprocessable_Entity (form validation fail)
     *
     * @return void
     */
    public function test_공지글_쓰기_실패_본문없음_422_Unprocessable_Entity(): void
    {
        $board = Board::factory()->create();
        $user = User::factory()->create();

        $formData = [
            "subject" => $this->randomHtmlSentences(rand(1, 3)),
            "content" => "",
            "notice" => true,
        ];

        $response = $this->actingAs($user)->postJson(
            route("v1.board.post.store", [
                "boardName" => $board->name,
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
