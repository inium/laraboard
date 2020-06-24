<?php

namespace Inium\Laraboard\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inium\Laraboard\App\Board\BoardUserRoles;
use Inium\Laraboard\App\Board\PostTrait;
use Inium\Laraboard\App\Board\SearchTrait;
use Inium\Laraboard\App\Board\RenderTemplateTrait;
use Inium\Laraboard\App\Middleware\PostListSearchMiddleware;

class PostListSearchController extends Controller
{
    use PostTrait, SearchTrait, RenderTemplateTrait;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(PostListSearchMiddleware::class);
    }

    /**
     * 게시글 목록, 검색 페이지
     *
     * -------------------------------------------------------------------------
     * GET [/{$prefix}]/board/{boardName}?query=lorem&page=1
     * 
     * Route params
     * @param Request $request  Request
     * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
     * @param string boardName  게시판 영문 이름.
     * 
     * Query params (Optional)
     * @param string query      검색어.
     * @param int    page       페이지 번호. 기본 1.
     * -------------------------------------------------------------------------
     * 
     * @param Request $request      Request
     * @param string $boardName     게시판 이름
     */
    public function view(Request $request, string $boardName)
    {
        $query = $request->query('query', null); // 검색어
        $page = $request->query('page', 1); // 페이지 번호

        $viewName = null;
        $params = null;

        // 검색어가 존재할 경우
        if ($query) {
            $viewName = 'search';
            $params = $this->getSearchResult($boardName, $query, $page);
        }
        // 게시글 목록
        else {
            $viewName = 'postList';
            $params = $this->getPostList($boardName, $page);
        }

        $viewParams = [
            'role'  => BoardUserRoles::roles($boardName),
            'query' => $query,
            'page'  => $page
        ];

        return $this->render($viewName, array_merge($params, $viewParams));
    }
}
