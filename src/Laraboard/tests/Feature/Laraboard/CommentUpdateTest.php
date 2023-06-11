<?php

namespace Tests\Feature\Laraboard;

use App\Models\Laraboard\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Inium\Laraboard\Support\Traits\Tests\RecursiveRefreshDatabaseTrait as RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class CommentUpdateTest extends TestCase
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
     * 댓글 수정 테스트
     * - 성공 200 OK
     *
     * @return void
     */
    public function test_댓글_수정_성공_200_Ok(): void
    {
        $comment = Comment::factory()->create();

        $formData = [
            "content" => $this->randomHtmlSentences(rand(20, 50)),
        ];

        $response = $this->actingAs($comment->user)->putJson(
            route("v1.board.post.comment.update", [
                "boardName" => $comment->board->name,
                "postId" => $comment->post->id,
                "commentId" => $comment->id,
            ]),
            $formData
        );

        $response->assertOk();

        // 댓글이 수정되었는지 확인
        $isExist =
            Comment::where([
                ["wrote_user_id", "=", $comment->user->id],
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
     * 댓글 수정 테스트 실패
     * - 사용자 정보 없음 (401 Unauthorized)
     *
     * @return void
     */
    public function test_댓글_수정_실패_작성자_정보_없음_401_Unauthorized(): void
    {
        $comment = Comment::factory()->create();

        $formData = [
            "content" => $this->randomHtmlSentences(rand(20, 50)),
        ];

        $response = $this->putJson(
            route("v1.board.post.comment.update", [
                "boardName" => $comment->board->name,
                "postId" => $comment->post->id,
                "commentId" => $comment->id,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * 댓글 수정 테스트 실패
     * - 본문 없음 422 Unprocessable Entity (form validation fail)
     *
     * @return void
     */
    public function test_댓글_수정_실패_본문없음_422_Unprocessable_Entity(): void
    {
        $comment = Comment::factory()->create();

        $formData = [];

        $response = $this->actingAs($comment->user)->putJson(
            route("v1.board.post.comment.update", [
                "boardName" => $comment->board->name,
                "postId" => $comment->post->id,
                "commentId" => $comment->id,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * 댓글 수정 테스트 실패
     * - 작성자 / 수정자 정보 불일치 (401 Unauthorized)
     *
     * @return void
     */
    public function test_댓글_수정_실패_댓글_작성자_정보_불일치_401_Unauthorized(): void
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create();

        $formData = [
            "content" => $this->randomHtmlSentences(rand(20, 50)),
        ];

        $response = $this->actingAs($user)->putJson(
            route("v1.board.post.comment.update", [
                "boardName" => $comment->board->name,
                "postId" => $comment->post->id,
                "commentId" => $comment->id,
            ]),
            $formData
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
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
