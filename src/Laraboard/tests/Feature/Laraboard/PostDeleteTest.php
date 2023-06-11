<?php

namespace Tests\Feature\Laraboard;

use App\Models\Laraboard\Board;
use App\Models\Laraboard\Comment;
use App\Models\Laraboard\Post;
use App\Models\User;
use Inium\Laraboard\Support\Traits\Tests\RecursiveRefreshDatabaseTrait as RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class PostDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 게시글 삭제 테스트 성공 - 204 No Content
     * - 작성자 본인글 삭제, 게시글 댓글 없음
     *
     * @return void
     */
    public function test_게시글_삭제_성공_204_No_Content(): void
    {
        $post = Post::factory()->create();

        $response = $this->actingAs($post->user)->deleteJson(
            route("v1.board.post.destroy", [
                "boardName" => $post->board->name,
                "postId" => $post->id,
            ])
        );

        $response->assertNoContent();

        // 게시글 삭제되었는지 확인
        $found = Post::find($post->id);
        $this->assertEmpty($found);
    }

    /**
     * 게시글 삭제 테스트 실패 - 401 Unauthorized
     * - 게시글 작성자가 아닌 다른 사용자가 삭제할 경우
     *
     * @return void
     */
    public function test_게시글_삭제_실패_작성자_정보_불일치_401_Unauthorized(): void
    {
        User::factory(10)->create();
        $post = Post::factory()->create();

        $user = User::where("id", "<>", $post->user->id)
            ->inRandomOrder()
            ->first();

        $response = $this->actingAs($user)->deleteJson(
            route("v1.board.post.destroy", [
                "boardName" => $post->board->name,
                "postId" => $post->id,
            ])
        );

        $response->assertUnauthorized();
    }

    /**
     * 게시글 삭제 테스트 실패 - 401 Unauthorized
     * - 게시글 작성자 없이 삭제를 시도할 경우
     *
     * @return void
     */
    public function test_게시글_삭제_실패_작성자가_없는경우_401_Unauthorized(): void
    {
        $post = Post::factory()->create();

        $response = $this->deleteJson(
            route("v1.board.post.destroy", [
                "boardName" => $post->board->name,
                "postId" => $post->id,
            ])
        );

        $response->assertUnauthorized();
    }

    /**
     * 게시글 삭제 테스트 실패 - 404 Not Found
     * - 잘못된 게시판
     *
     * @return void
     */
    public function test_게시글_삭제_실패_잘못된_게시판_404_Not_Found(): void
    {
        $comment = Comment::factory()->create();
        $board = Board::factory()->create();

        $response = $this->actingAs($comment->post->user)->deleteJson(
            route("v1.board.post.destroy", [
                "boardName" => $board->name,
                "postId" => $comment->post->id,
            ])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * 게시글 삭제 테스트 실패 - 409 Conflict
     * - 댓글이 존재하는 게시글인 경우 삭제 불가
     *
     * @return void
     */
    public function test_게시글_삭제_실패_댓글이_있는경우_409_Conflict(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->actingAs($comment->post->user)->deleteJson(
            route("v1.board.post.destroy", [
                "boardName" => $comment->board->name,
                "postId" => $comment->post->id,
            ])
        );

        $response->assertStatus(Response::HTTP_CONFLICT);
    }
}
