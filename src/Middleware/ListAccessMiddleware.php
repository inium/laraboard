<?php

namespace Inium\Laraboard\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\Models\Board as LaraboardBoard;
use Inium\Laraboard\Models\User as LaraboardUser;

class ListAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \illuminate\http\request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /**
         * 게시판 게시글 목록 보기 권한 확인 절차
         * 
         * 1. 게시판 정보 확인.
         * 2. 게시판 게시글 목록 읽기 권한 확인.
         *   2-1. 게시글 목록 읽기 권한이 null일 경우, 로그인 상관없이 모두공개.
         *   2-2. 게시글 목록 읽기 권한이 존재할 경우, 사용자 권한 확인.
         *     - 로그인 사용자가 존재하지 않을 경우, abort.
         *     - 로그인 사용자가 존재할 경우. 게시판 사용자 권한 확인.
         *       - 사용자 권한이 게시글 목록 읽기 권한보다 낮으면(숫자) next.
         *       - 사용자 권한이 게시글 목록 읽기 권한보다 높으면(숫자) abort.
         */

        // 게시판 정보가 없으면 404 예외 발생.
        $board = LaraboardBoard::findByName($request->boardName);
        abort_if(!$board, 404, 'Board not found.');

        $privilege = $board->minPostListReadPrivilege;

        // 게시글 목록 읽기 권한 정보가 있을경우 로그인한 사용자에게만 공개.
        // 게시글 목록 읽기 권한 정보가 없으면 모두에게 공개.
        if (!is_null($privilege)) {

            // 사용자 로그인을 하지 않았으면 401 예외 발생.
            abort_if(!Auth::check(), 401, 'Auth check fail.');

            // 게시판 사용자 정보가 없으면 401 예외 발생.
            $boardUser = LaraboardUser::findByUserId(Auth::id());
            abort_if(!$boardUser, 401, 'No board user exists.');

            // 사용자 권한이 게시글 목록 읽기 권한보다 크면(숫자) 접근불가.
            if ($boardUser->privilege->id > $privilege->id) {
                abort(401, 'Can\'t have a access privilege.');
            }
        }

        return $next($request);
    }
}
