<?php

namespace Inium\Laraboard\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inium\Laraboard\App\Post;

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
        // 사용자가 로그인 하지 않았을 경우 로그인 페이지로 Redirect
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 게시글 확인. 없을 경우, 404 출력.
        $post = Post::find($request->id);
        abort_if(!$post, 404, 'Post not found.');

        // 본인 확인. 게시글 작성자가 본인이 아닌 경우 401 출력.
        abort_if($post->user->user->id !== Auth::id(), 401, 'Unauthorized');

        // 댓글이 있는 경우 삭제 불가
        if ($post->comments->count() > 0) {
            Session::flash('message', '댓글이 있는 글은 삭제할 수 없습니다.');
            Session::flash('alert-class', 'alert-danger');

            return redirect()->route('board.post.view', [
                'boardName' => $request->boardName,
                'id' => $request->id,
                'page' => $request->get('page', null)
            ]);
        }

        return $next($request);
    }
}
