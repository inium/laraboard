<?php

namespace Tests\Feature\Laraboard;

use App\Models\Laraboard\Board;
use App\Models\Laraboard\Comment;
use App\Models\Laraboard\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Inium\Laraboard\Support\Traits\Tests\RecursiveRefreshDatabaseTrait as RefreshDatabase;
use Tests\TestCase;

class CommentDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 댓글 삭제 성공 - 204 No Content
     *
     * @return void
     */
    public function test_댓글_삭제_204_No_Content()
    {
        $comment = Comment::factory()->create();

        $response = $this->actingAs($comment->user)->deleteJson(
            route("v1.board.post.comment.destroy", [
                "boardName" => $comment->board->name,
                "postId" => $comment->post->id,
                "commentId" => $comment->id,
            ])
        );

        $response->assertNoContent();

        // 게시글 삭제되었는지 확인
        $found = Comment::find($comment->id);
        $this->assertEmpty($found);
    }

    /**
     * 댓글 삭제 실패 - 401 Unauthorized
     * - 본인 댓글이 아닌 경우
     *
     * @return void
     */
    public function test_댓글_삭제_실패_작성자_정보_불일치_401_Unauthorized(): void
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson(
            route("v1.board.post.comment.destroy", [
                "boardName" => $comment->board->name,
                "postId" => $comment->post->id,
                "commentId" => $comment->id,
            ])
        );

        $response->assertUnauthorized();
    }

    /**
     * 댓글 삭제 실패 - 401 Unauthorized
     * - 댓글 작성자 정보가 없을 때 삭제를 시도한 경우
     *
     * @return void
     */
    public function test_댓글_삭제_실패_작성자가_없는경우_401_Unauthorized(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->deleteJson(
            route("v1.board.post.comment.destroy", [
                "boardName" => $comment->board->name,
                "postId" => $comment->post->id,
                "commentId" => $comment->id,
            ])
        );

        $response->assertUnauthorized();
    }

    /**
     * 댓글 삭제 실패 - 404 Not found
     * - 댓글이 소속된 잘못된 게시판 정보
     *
     * @return void
     */
    public function test_댓글_삭제_실패_잘못된_게시판_404_Not_Found(): void
    {
        $comment = Comment::factory()->create();
        $board = Board::factory()->create();

        $response = $this->actingAs($comment->user)->deleteJson(
            route("v1.board.post.comment.destroy", [
                "boardName" => $board->name,
                "postId" => $comment->post->id,
                "commentId" => $comment->id,
            ])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * 댓글 삭제 실패 - 404 Not found
     * - 댓글이 소속된 잘못된 게시글 정보
     *
     * @return void
     */
    public function test_댓글_삭제_실패_잘못된_게시글_404_Not_Found(): void
    {
        $comment = Comment::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($comment->user)->deleteJson(
            route("v1.board.post.comment.destroy", [
                "boardName" => $comment->board->name,
                "postId" => $post->id,
                "commentId" => $comment->id,
            ])
        );

        $response->assertNotFound();
    }

    /**
     * 댓글 삭제 실패 - 409
     * - 댓글이 소속된 잘못된 게시글 정보
     *
     * @return void
     */
    public function test_댓글_삭제_실패_자식댓글이_있는경우_409_Conflict(): void
    {
        $numOfComments = mt_rand(3, 152);

        $parent = Comment::factory()->create();
        Comment::factory($numOfComments)
            ->children($parent->id, $parent->post->id)
            ->create();

        $response = $this->actingAs($parent->user)->deleteJson(
            route("v1.board.post.comment.destroy", [
                "boardName" => $parent->board->name,
                "postId" => $parent->post->id,
                "commentId" => $parent->id,
            ])
        );

        $response->assertStatus(Response::HTTP_CONFLICT);
    }
}
