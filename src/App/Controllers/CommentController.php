<?php

namespace Inium\Laraboard\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\Post;
use Inium\Laraboard\App\Comment;
use Inium\Laraboard\App\User;
use Inium\Laraboard\App\Board\BoardUserRoles;
use Inium\Laraboard\App\Board\RenderTemplateTrait;
use Inium\Laraboard\App\Board\Request\CommentRequest;
use Inium\Laraboard\App\Middleware\CommentWriteMiddleware;
use Inium\Laraboard\App\Middleware\CommentModifyMiddleware;
use Inium\Laraboard\App\Middleware\CommentDeleteMiddleware;
use Inium\Laraboard\Support\Facades\Agent;

class CommentController extends Controller
{
    use RenderTemplateTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // 댓글 쓰기 미들웨어
        $this->middleware(CommentWriteMiddleware::class)->only('store');
        // $this->middleware(CommentModifyMiddleware::class)->only('update');
        $this->middleware(CommentDeleteMiddleware::class)->only('delete');
    }

    /**
     * 댓글 목록을 가져온다.
     *
     * @param Request $request      Request
     * @param string $boardName     게시판 이름
     * @param integer $postId       게시글 ID
     */
    public function index(Request $request, string $boardName, int $postId)
    {
        $groupId = $request->query('group_id', null); // 댓글 그룹 ID

        $post = Post::find($postId);
        // $count = $post->comments()->count();
        $nextComments = $post->getNextCommentsByGroupId($groupId);

        return response()->view('laraboard::components.shared.commentChunk', [
            'comments' => $nextComments,
            'role' => BoardUserRoles::roles($boardName)
        ]);
    }

    /**
     * 댓글을 저장한다.
     *
     * @param CommentRequest $request   Request
     * @param string $boardName         게시판 이름
     * @param integer $postId           게시글 ID
     */
    public function store(CommentRequest $request,
                         string $boardName,
                         int $postId)
    {
        // 댓글 저장
        $commentId = $this->storeComment($request, $boardName, $postId);

         // 댓글 저장에 성공한 경우, 게시글 보기 페이지로 이동
         if ($commentId) {
            return redirect()->route('board.post.view', [
                            'boardName' => $boardName,
                            'postId' => $postId
                        ]);
        }
        // 댓글 저장에 실패한 경우, 게시글 쓰기 페이지로 이동
        else {
            $errorMessage = '댓글 저장에 실패하였습니다. 다시 시도해주세요.';
            return redirect()->route('board.post.view', [
                            'boardName' => $boardName,
                            'postId' => $postId
                        ])
                        ->withErrors(array($errorMessage))
                        ->withInput();
        }
    }

    /**
     * 댓글을 수정한다.
     *
     * @param CommentRequest $request   Request
     * @param string $boardName         게시판 이름
     * @param integer $postId           게시글 ID
     * @param integer $commentId        댓글 ID
     */
    public function update(CommentRequest $request,
                           string $boardName,
                           int $postId,
                           int $commentId)
    {
        // 댓글 수정
        $commentId = $this->updateComment($request,
                                          $boardName,
                                          $postId,
                                          $commentId);

         // 댓글 수정에 성공한 경우, 게시글 보기 페이지로 이동
         if ($commentId) {
            return redirect()->route('board.post.view', [
                            'boardName' => $boardName,
                            'postId' => $postId
                        ]);
        }
        // 댓글 수정에 실패한 경우, 게시글 쓰기 페이지로 이동
        else {
            $errorMessage = '댓글 수정에 실패하였습니다. 다시 시도해주세요.';
            return redirect()->route('board.post.view', [
                            'boardName' => $boardName,
                            'postId' => $postId
                        ])
                        ->withErrors(array($errorMessage))
                        ->withInput();
        }
    }

    /**
     * 댓글을 삭제한다.
     *
     * @param CommentRequest $request   Request
     * @param string $boardName         게시판 이름
     * @param integer $postId           게시글 ID
     * @param integer $commentId        댓글 ID
     */
    public function destroy(Request $request,
                           string $boardName,
                           int $postId,
                           int $commentId)
    {
        // Get comment
        $comment = Comment::find($commentId);

        // 자식 댓글이 있는 경우 삭제 불가
        if ($comment->children()->count() > 0) {
            Session::flash('message', '답글이 있는 댓글은 삭제할 수 없습니다.');
            Session::flash('alert-class', 'alert-danger');

            return redirect()->route('board.post.view', [
                'boardName' => $boardName,
                'postId' => $postId
            ]);
        }

        $comment->delete();

        // 게시글 삭제정보
        $flashMessage = "작성한 댓글을 삭제하였습니다.";
        Session::flash('message', $flashMessage);
        Session::flash('alert-class', 'alert-danger');

        return redirect()->route('board.post.view', [
            'boardName' => $boardName,
            'postId' => $postId
        ]);
    }

    /**
     * 댓글을 저장한다.
     *
     * @param CommentRequest $request   Request
     * @param string $boardName         게시판 이름
     * @param integer $postId           게시글 ID
     * @return integer                  추가된 댓글 ID
     */
    private function storeComment(CommentRequest $request,
                                  string $boardName,
                                  int $postId): int
    {
        $board = Board::findByName($boardName);
        $post = Post::find($postId);
        $user = User::findByUserId(Auth::id());

        // Get User Agent
        $ua = Agent::parse($request->server('HTTP_USER_AGENT'));

        $comment = new Comment();

        $comment->user_agent = $ua->agent;
        $comment->device_type = $ua->device_type;
        $comment->os_name = $ua->os_name;
        $comment->os_version = $ua->os_version;
        $comment->browser_name = $ua->browser_name;
        $comment->browser_version = $ua->browser_version;
        $comment->content = htmlspecialchars($request->content);
        $comment->content_pure = strip_tags($request->content);
        $comment->point = $board->comment_point;
        $comment->board()->associate($board);
        $comment->post()->associate($post);
        $comment->user()->associate($user);

        // 부모 댓글 ID가 있을 경우, 자식 댓글로 추가
        if ($request->parent_comment_id) {
            $parentComment = Comment::find($request->parent_comment_id);
            $comment->parent()->associate($parentComment);
        }

        $comment->save();

        return $comment->id;
    }

    /**
     * 댓글을 수정한다.
     *
     * @param CommentRequest $request   Request
     * @param string $boardName         게시판 이름
     * @param integer $postId           게시글 ID
     * @param integer $commentId        댓글 ID
     * @return boolean                  수정 여부
     */
    private function updateComment(CommentRequest $request,
                                   string $boardName,
                                   int $postId,
                                   int $commentId): bool
    {
        // Get User Agent
        $ua = Agent::parse($request->server('HTTP_USER_AGENT'));

        $comment = Comment::find($commentId);

        $comment->user_agent = $ua->agent;
        $comment->device_type = $ua->device_type;
        $comment->os_name = $ua->os_name;
        $comment->os_version = $ua->os_version;
        $comment->browser_name = $ua->browser_name;
        $comment->browser_version = $ua->browser_version;
        $comment->content = htmlspecialchars($request->content);
        $comment->content_pure = strip_tags($request->content);

        $updated = $comment->save();

        return $updated;
    }
}
