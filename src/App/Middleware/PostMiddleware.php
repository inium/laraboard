<?php

namespace Inium\Laraboard\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Inium\Laraboard\App\Board;
// use Inium\Laraboard\App\User;
use Inium\Laraboard\App\Board\BoardUserRolesTrait;

class PostMiddleware
{
    use BoardUserRolesTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\http\request  $request
     * @param \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // 게시판 사용자 권한 Get
            $boardUserRoles = $this->boardUserRoles($request->boardName);

            // 게시글을 읽을 수 없을 경우
            if (!$boardUserRoles->post->canRead) {

                // 사용자가 로그인 하지 않았을 경우 로그인 페이지로 Redirect
                if (!Auth::check()) {
                    return redirect()->route('login');
                }

                // 그 외: 게시글 읽기 권한 없음.
                throw new \Exception('Can\'t read board posts.', 401);
            }

            return $next($request);
        }
        catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }

        /**
         * 게시판 게시글 확인 절차
         * 
         * 1. 게시판 정보 확인.
         * 2. 게시글 존재여부 확인.
         * 3. 게시글 읽기 권한 확인.
         *   3-1. 게시글 읽기 권한이 null일 경우, 로그인 상관없이 모두공개.
         *   3-2. 게시글 목록 읽기 권한이 존재할 경우, 사용자 권한 확인.
         *     - 로그인 사용자가 존재하지 않을 경우, abort.
         *     - 로그인 사용자가 존재할 경우.
         *       - 본인 글이라면 권한 상관없이 볼 수 있음.
         *       - 본인 글이 아닐 경우, 게시글 보기 권한 확인.
         *         - 사용자 권한이 게시글 읽기 권한보다 낮으면(숫자) next.
         *         - 사용자 권한이 게시글 읽기 권한보다 높으면(숫자) abort.
         */





        // // 게시판 정보 Get. 없으면 404 예외 발생.
        // $board = Board::findByName($request->boardName);
        // abort_if(!$board, 404, 'Board not found.');

        // // 게시글 정보가 존재하지 않으면 404 예외 발생.
        // $post = $board->posts()->find($request->id);
        // abort_if(!$post, 404, 'Post not found.');

        // // // 사용자 역할 중 읽기, 쓰기 가능 여부 저장.
        // // $userRoles = [
        // //     'canReadPost' => true,
        // //     'canWritePost' => false,
        // //     'canDeletePost' => false
        // // ];

        // // 게시글 읽기 권한 확인
        // // $privilege = $board->minPostReadRole;
        // // if (!is_null($privilege)) {
        // if ($board->minPostReadRole) {
        //     // 사용자가 로그인 하지 않았을 경우 로그인 페이지로 Redirect
        //     if (!Auth::check()) {
        //         return redirect()->route('login');
        //     }

        //     // 게시판 사용자 정보가 없으면 401 예외 발생.
        //     $user = User::findByUserId(Auth::id());
        //     abort_if(!$user, 401, 'No board user exists.');

        //      $canReadPost = $user->canReadPost($board);

        //     if ($post->user->id != Auth::id()) {
        //         if (!$canReadPost) {
        //             abort(401, 'Can\'t read board posts.');
        //         }
        //     }
        //     // 본인 글은 next.
        //     else {
        //         $userRoles['canDeletePost'] = true;
        //     }

        //     // // 사용자 글 읽기, 쓰기 권한 체크.
        //     // $userRoles['canReadPost']  = $canReadPost;
        //     // $userRoles['canWritePost'] = $user->canWritePost($board);
        // }

        // // // Controller에서 사용할 사용자 Role 정보 저장
        // // $request->attributes->add($userRoles);

        // return $next($request);
    }
}
