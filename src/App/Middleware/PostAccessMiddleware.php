<?php

namespace Inium\Laraboard\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inium\Laraboard\App\Post;
use Inium\Laraboard\App\Board\BoardUserRoles;

class PostAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\http\request  $request
     * @param \Closure  $next
     * @param string $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role = null)
    {
        // $post = Post::find($request->postId);
        // abort_if(!$post, 404, 'Post not found');

        // 본인 체크


        // if ($role) {
        //     $roles = BoardUserRoles::roles($request->boardName);
        // }

        return $next($request);
    }
}
