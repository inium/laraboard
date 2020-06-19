<?php

namespace Inium\Laraboard\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\User;

class PostDeleteMiddleware
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
        /**
         * 게시글 삭제관련 사용자 확인 절차
         * 
         * 1. 사용자 로그인 확인
         *      - 로그인 하지 않았을 경우, 401 Unauthorized.
         * 2. 게시판 정보가 존재하는지 확인
         *      - 게시판 정보가 졵재하지 않을 경우, 404 Not found.
         * 3. 게시글 존재 확인
         *      - 게시글이 없으면 404 Not found.
         *      - 게시글에 댓글이 존재하면 게시글 보기 페이지로 이동.
         * 4. 게시글 사용자가 본인인지 확인
         *      - 게시글이 본인이 작성한 글이 맞을 경우, next
         *      - 게시글이 본인이 작성한 글이 아닐 경우, 401 Unauthorized
         */

        // 사용자가 로그인 하지 않았을 경우 401 예외 발생.
        abort_if(!Auth::check(), 401, 'Invalid auth check.');

        // 게시판 정보 Get. 없으면 404 예외 발생.
        $board = Board::findByName($request->boardName);
        abort_if(!$board, 404, 'Board not found.');

        // 게시글 정보가 존재하지 않으면 404 예외 발생.
        $post = $board->posts()->find($request->id);
        abort_if(!$post, 404, 'Post not found.');

        // 댓글 정보가 존재할 경우 삭제 불가.
        if ($post->comments()->count() > 0) {

            $redirectMessage = '댓글이 있을경우 삭제할 수 없습니다.';

            return redirect()->route('laraboard.post.view', [
                                'boardName' => $request->boardName,
                                'id' => $request->id,
                                'type' => $request->query('type', null),
                                'query' => $request->query('query', null),
                                'page' => $request->query('page', null)
                            ])->with('message', $redirectMessage);
        }

        // 게시글 작성 사용자 확인
        if ($post->user()->id == Auth::id()) {
            return $next($request);
        }
        else {
            abort(401, 'Invalid post owner.');
        }
    }
}
