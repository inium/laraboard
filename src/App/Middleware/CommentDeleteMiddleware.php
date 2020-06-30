<?php

namespace Inium\Laraboard\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inium\Laraboard\App\Board;

class CommentDeleteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\http\request  $request
     * @param \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 사용자가 로그인 하지 않았을 경우 로그인 페이지로 Redirect
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 게시판이 없을 경우 404 반환
        $board = Board::findByName($request->boardName);
        abort_if(!$board, 404, 'Board not found');

        // 게시글이 없을 경우 404 반환
        $post = $board->getPost($request->postId);
        abort_if(!$post, 404, 'Post not found');

        // 댓글이 없을 경우 404 반환
        $comment = $post->comments()->find($request->commentId);
        abort_if(!$comment, 404, 'Comment not found');

        // 댓글 작성자가 본인이 아닌 경우, 401 반환
        abort_if($comment->user->user->id !== Auth::id(), 401, 'Unauthorized');

        return $next($request);
    }
}
