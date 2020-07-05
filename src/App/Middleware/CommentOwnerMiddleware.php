<?php

namespace Inium\Laraboard\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inium\Laraboard\App\Comment;

class CommentOwnerMiddleware
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
        // 댓글 존재 여부 확인
        $comment = Comment::find($request->commentId);
        abort_if(!$comment, 404, 'Comment not found');

        // 댓글 작성자가 본인이 아닌 경우, 401 반환
        abort_if($comment->user->user->id !== Auth::id(), 401, 'Unauthorized');

        return $next($request);
    }
}
