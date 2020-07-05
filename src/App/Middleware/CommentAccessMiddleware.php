<?php

namespace Inium\Laraboard\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board\BoardUserRoles;

class CommentAccessMiddleware
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

        if (!$roles->comment->canRead) {

            // 사용자가 로그인 하지 않았을 경우 로그인 페이지로 Redirect
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            abort(401, 'Can\'t read a comment.');
        }

        return $next($request);
    }
}
