<?php

namespace Inium\Laraboard\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\User;

class PostsAccessMiddleware
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
         * 1. 게시판 정보 확인. 없을 경우 404 예외 출력.
         * 2. 게시판 게시글 목록 읽기 권한 확인.
         *   2-1. 게시글 목록 읽기 권한이 null일 경우, 로그인 상관없이 모두공개.
         *   2-2. 게시글 목록 읽기 권한이 존재할 경우, 사용자 권한 확인.
         *     - 로그인 사용자가 존재하지 않을 경우, abort.
         *     - 로그인 사용자가 존재할 경우. 게시판 사용자 권한 확인.
         *       - 사용자 권한이 게시글 목록 읽기 권한보다 낮으면(숫자) next.
         *       - 사용자 권한이 게시글 목록 읽기 권한보다 높으면(숫자) abort.
         */

        // 게시판 정보 Get. 없으면 404 예외 발생.
        $board = Board::findByName($request->boardName);
        abort_if(!$board, 404, 'Board not found.');

        // 사용자 역할 중 읽기, 쓰기 가능 여부 저장.
        $userRoles = [
            'canReadPost' => true,
            'canWritePost' => false
        ];

        // 게시글 목록 읽기 권한이 존재하는 경우 사용자 로그인 정보 & 권한 확인
        if ($board->minPostReadRole) {
            // 사용자가 로그인 하지 않았을 경우 로그인 페이지로 Redirect
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            // 게시판 사용자 정보가 없으면 401 예외 발생.
            $user = User::findByUserId(Auth::id());
            abort_if(!$user, 401, 'No board user exists.');

            // 게시글 읽기 권한 비교
            $canReadPost = $user->canReadPost($board);
            if (!$canReadPost) {
                abort(401, 'Can\'t read board posts.');
            }

            // 사용자 글 읽기, 쓰기 권한 체크.
            $userRoles['canReadPost']  = $canReadPost;
            $userRoles['canWritePost'] = $user->canWritePost($board);
        }

        // Controller에서 사용할 사용자 Role 정보 저장
        $request->attributes->add($userRoles);

        return $next($request);
    }
}
