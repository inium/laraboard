<?php

namespace Inium\Laraboard\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\Board\BoardUserRoles;

class BoardAccessMiddleware
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
        $board = Board::findByName($request->boardName);
        abort_if(!$board, 404, 'Board not found');

        // $roles = BoardUserRoles::roles($request->boardName);

        return $next($request);
    }
}
