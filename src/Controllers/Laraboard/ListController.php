<?php

namespace App\Http\Controllers\Laraboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inium\Laraboard\Core\Board\ListArticlesTrait;
use Inium\Laraboard\Middleware\ListArticlesMiddleware;

class PostListController extends Controller
{
    use PostListTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(ListArticlesMiddleware::class);
    }
}
