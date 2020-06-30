<?php

namespace Inium\Laraboard\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Post;
use Inium\Laraboard\App\Board\BoardUserRoles;

class PostModifyMiddleware
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
        $post = Post::find($request->postId);
        abort_if(!$post, 404, 'Post not found.');

        // 본인 확인. 게시글 작성자가 본인이 아닌 경우 401 출력.
        abort_if($post->user->user->id !== Auth::id(), 401, 'Unauthorized');

        return $next($request);
    }
}
