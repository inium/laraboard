<?php

namespace Inium\Laraboard\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board\BoardUserRoles;

class PostListSearchMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\http\request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 게시판 사용자 권한 Get
        $boardUserRoles = BoardUserRoles::roles($request->boardName);

        // 게시글을 읽을 수 없을 경우
        if (!$boardUserRoles->post->canRead) {

            // 사용자가 로그인 하지 않았을 경우 로그인 페이지로 Redirect
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            // 그 외: 게시글 읽기 권한 없음.
            abort(401, 'Can\'t read board posts.');
        }

        return $next($request);
    }
}
