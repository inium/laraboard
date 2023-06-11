<?php

namespace Tests\Feature\Laraboard;

use App\Models\Laraboard\Board;
use App\Models\Laraboard\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Inium\Laraboard\Support\Traits\Tests\RecursiveRefreshDatabaseTrait as RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class PostStoreTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * 게시글 작성 시 허용할 tag
     *
     * @var array
     */
    private array $allowedTags = [];

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->allowedTags = config("laraboard.allow_post_content_tags");
    }

    /**
     * 게시글 쓰기 테스트
     * - 성공 200 OK
     *
     * @return void
     */
    public function test_게시글_쓰기_성공_201_Created(): void
    {
        $board = Board::factory()->create();
        $user = User::factory()->create();

        $formData = [
            "subject" => $this->randomHtmlSentences(rand(1, 3)),
            "content" => $this->randomHtmlSentences(rand(20, 50)),
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
                ["notice", "=", false],
                ["subject", "=", strip_tags($formData["subject"])],
                [
                    "content",
                    "=",
                    htmlspecialchars(
                        strip_tags($formData["content"], $this->allowedTags)
                    ),
                ],
            ])->count() > 0;

        $this->assertTrue($isExist);
    }

    /**
     * 게시글 쓰기 테스트 실패
     * - 사용자 정보 없음 (401 Unauthorized)
     *
     * @return void
     */
    public function test_게시글_쓰기_실패_작성자_정보_없음_401_Unauthorized(): void
    {
        $board = Board::factory()->create();

        $formData = [
            "subject" => $this->randomHtmlSentences(rand(1, 3)),
            "content" => $this->randomHtmlSentences(rand(20, 50)),
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
     * 게시글 쓰기 테스트 실패
     * - 공지여부 잘못된 자료형 값 422 Unprocessable Entity (form validation fail)
     *
     * @return void
     */
    public function test_게시글_쓰기_실패_공지여부_잘못된값_422_Unprocessable_Entity(): void
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
     * 게시글 쓰기 테스트 실패
     * - 제목 없음 422 Unprocessable Entity (form validation fail)
     *
     * @return void
     */
    public function test_게시글_쓰기_실패_제목없음_422_Unprocessable_Entity(): void
    {
        $board = Board::factory()->create();
        $user = User::factory()->create();

        $formData = [
            // "subject" => $this->randomHtmlSentences(rand(1, 3)),
            "content" => $this->randomHtmlSentences(rand(20, 50)),
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
     * 게시글 쓰기 테스트 실패
     * - 본문 없음 422 Unprocessable Entity (form validation fail)
     *
     * @return void
     */
    public function test_게시글_쓰기_실패_본문없음_422_Unprocessable_Entity(): void
    {
        $board = Board::factory()->create();
        $user = User::factory()->create();

        $formData = [
            "subject" => $this->randomHtmlSentences(rand(1, 3)),
            // "content" => $this->randomHtmlSentences(rand(20, 50)),
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
