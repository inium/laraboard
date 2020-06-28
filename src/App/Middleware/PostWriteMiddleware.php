<?php

namespace Inium\Laraboard\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\Board\BoardUserRoles;

class PostWriteMiddleware
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

        // 게시글을 작성 가능한지 사용자 역할 확인. 사용할 수 없으면 401 반환.
        $roles = BoardUserRoles::roles($request->boardName);
        abort_if(!$roles->post->canRead, 401, 'Can\'t write a post.');

        return $next($request);
    }
}
