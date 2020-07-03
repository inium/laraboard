<?php

namespace Inium\Laraboard\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inium\Laraboard\App\Board\BoardUserRoles;

class CommentStoreMiddleware
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
        // 댓글을 작성 가능한지 사용자 역할 확인. 사용할 수 없으면 401 반환.
        $roles = BoardUserRoles::roles($request->boardName);
        abort_if(!$roles->comment->canWrite, 401, 'Can\'t write a comment.');

        return $next($request);
    }
}
