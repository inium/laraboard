<?php

namespace Tests\Feature\Laraboard;

use App\Models\Laraboard\Comment;
use App\Models\Laraboard\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Inium\Laraboard\Support\Traits\Tests\RecursiveRefreshDatabaseTrait as RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class CommentStoreTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * 댓글 작성 시 허용할 tag
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
        $this->allowedTags = config("laraboard.allow_comment_content_tags");
    }

    /**
     * 댓글 쓰기 테스트
     * - 성공 200 OK
     *
     * @return void
     */
    public function test_댓글_쓰기_성공_201_Created(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $formData = [
            "content" => $this->randomHtmlSentences(rand(20, 50)),
        ];

        $response = $this->actingAs($user)->postJson(
            route("v1.board.post.comment.store", [
                "boardName" => $post->board->name,
                "postId" => $post->id,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_CREATED);

        // 댓글이 저장되었는지 확인
        $isExist =
            Comment::where([
                ["wrote_user_id", "=", $user->id],
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
     * 자식댓글 쓰기 테스트
     * - 성공 201 Created
     *
     * @return void
     */
    public function test_자식댓글_쓰기_성공_201_Created(): void
    {
        $parent = Comment::factory()->create();
        $user = User::factory()->create();

        $formData = [
            "content" => $this->randomHtmlSentences(rand(20, 50)),
            "parent_comment_id" => $parent->id,
        ];

        $response = $this->actingAs($user)->postJson(
            route("v1.board.post.comment.store", [
                "boardName" => $parent->board->name,
                "postId" => $parent->post->id,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_CREATED);

        // 댓글이 저장되었는지 확인
        $isExist =
            Comment::where([
                ["wrote_user_id", "=", $user->id],
                [
                    "content",
                    "=",
                    htmlspecialchars(
                        strip_tags(
                            $formData["content"],
                            strip_tags($formData["content"], $this->allowedTags)
                        )
                    ),
                ],
            ])->count() > 0;

        $this->assertTrue($isExist);
    }

    /**
     * 댓글 쓰기 테스트 실패
     * - 사용자 정보 없음 (401 Unauthorized)
     *
     * @return void
     */
    public function test_댓글_쓰기_실패_작성자_정보_없음_401_Unauthorized(): void
    {
        $post = Post::factory()->create();

        $formData = [
            "content" => $this->randomHtmlSentences(rand(20, 50)),
        ];

        $response = $this->postJson(
            route("v1.board.post.comment.store", [
                "boardName" => $post->board->name,
                "postId" => $post->id,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * 댓글 쓰기 테스트 실패
     * - 본문 없음 422 Unprocessable Entity (form validation fail)
     *
     * @return void
     */
    public function test_댓글_쓰기_실패_본문없음_422_Unprocessable_Entity(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $formData = [];

        $response = $this->actingAs($user)->postJson(
            route("v1.board.post.comment.store", [
                "boardName" => $post->board->name,
                "postId" => $post->id,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * 자식댓글 쓰기 테스트 실패
     * - 부모댓글 ID 없음 404 Not Found
     *
     * @return void
     */
    public function test_자식댓글_쓰기_실패_부모댓글_ID_없음_404_Not_Found(): void
    {
        // TBD
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
