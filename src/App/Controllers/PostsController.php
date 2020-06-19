<?php

namespace Inium\Laraboard\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board\BoardRoute;
use Inium\Laraboard\App\Board\BoardUserRoles;
use Inium\Laraboard\App\Board\PostsTrait;
use Inium\Laraboard\App\Board\RenderTemplateTrait;
use Inium\Laraboard\App\Middleware\PostsMiddleware;

class PostsController extends Controller
{
    use PostsTrait, RenderTemplateTrait;

    // private $boardAttrs = [
    //     'name', 'name_ko'
    // ];

    // private $postsAttrs = [
    //     'id', 'subject', 'created_at', 'view_count', 'notice', 'comments_count',
    //     'user.nickname', 'user.thumbnail_path'
    // ];

    // private $paginationAttrs = [
    //     'current_page', 'last_page', 'path', 'total', 'per_page'
    // ];

    // private $includeUrlOnPostsAttrs = true;


    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(PostsMiddleware::class);
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
    public function get(Request $request, string $boardName)
    {
        $query = $request->query('query', null); // 검색어
        $page = $request->query('page', 1); // 페이지 번호

        // 검색어가 존재하는 경우 검색 결과. 없을 경우 게시글 목록.
        $params = $query ? $this->search($boardName, $query, $page)
                         : $this->posts($boardName, $page);

        // 추가로 View에 사용할 정보
        $viewParams = $this->viewParams($boardName);

        // $params['notices']->map(function (&$item) {
        //     $item['asdf'] = 'qwer';
        //     return $item;
        // });

        // dd($params);
        // foreach ($params as &$p) {
        //     dd($p['notices']);
        // }

        return $this->render('posts', array_merge($params, $viewParams));
    }

    /**
     * 추가로 View에서 사용할 정보를 반환한다.
     *
     * @param string $boardName     게시판 이름
     * @return array
     */
    private function viewParams(string $boardName): array
    {
        $bur = BoardUserRoles::roles($boardName);
        $canWrite = $bur->post->canWrite;

        // 목록, 글 쓰기 Route
        $routes = [
            'post' => null,
            'list' => BoardRoute::routeListSearch($boardName),
            'write' => $canWrite ? BoardRoute::routePostWrite($boardName) : null
        ];

        // 검색 form. 로그인 한 사용자에게만 허용.
        $form = [
            'search' => Auth::check() ? $this->searchForm($boardName) : null
        ];

        return [
            'routes' => $routes,
            'form' => $form
        ];
    }
}
