<?php

namespace Inium\Laraboard\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;
use Inium\Laraboard\App\Middleware\PostsAccessMiddleware;
use Inium\Laraboard\App\Board\PostsTrait;
use Inium\Laraboard\App\Board\PostSearchTrait;
use Inium\Laraboard\App\Board\RenderTemplateTrait;
use Inium\Laraboard\App\Board\SearchFormTrait;

class PostsController extends Controller
{
    use PostsTrait, PostSearchTrait, RenderTemplateTrait, SearchFormTrait {
        PostsTrait::fetchBoardInfo insteadof PostSearchTrait;
    }

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(PostsAccessMiddleware::class);
    }

    /**
     * 게시글 목록, 검색 페이지
     *
     * -------------------------------------------------------------------------
     * GET [/{$prefix}]/board/{boardName}?page=1&query=lorem&type=subcon
     * 
     * Route params
     * @param Request $request  Request
     * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
     * @param string boardName  게시판 영문 이름.
     * 
     * Query params (Optional)
     * @param int    page       페이지 번호. 기본 1.
     * @param string query      검색어.
     * @param string type       검색 유형. Board::$searchTypes 참조.
     * -------------------------------------------------------------------------
     * 
     * @param Request $request      Request
     * @param string $boardName     게시판 이름
     */
    public function index(Request $request, string $boardName)
    {
        $query = $request->query('query', null); // 검색어
        $page = $request->query('page', 1); // 페이지 번호

        $viewName = 'list';
        $params = null;

        // 검색어가 존재하는 경우: 검색결과 반환
        if ($query) {
            $type = $request->query('type', $this->getSearchTypes());

            $viewName = 'search';
            $params = $this->search($boardName, $query, $type, $page);
        }
        // 그 외: 게시글 목록 반환
        else {
            $viewName = 'list';
            $params = $this->posts($boardName, $page);
        }

        $viewParams = $this->getViewParams($request, $boardName);

        return $this->render($viewName, array_merge($params, $viewParams));
    }

    /**
     * 추가로 View에 표시할 정보를 반환한다.
     *
     * @param Request $request      Request
     * @param string $boardName     게시판 이름
     * @return array
     */
    private function getViewParams(Request $request, string $boardName): array
    {
        // 미들웨어(Middleware)에서 저장한 사용자 역할(Role) 정보를 가져온다.
        $canReadPost = $request->get('canReadPost');
        $canWritePost = $request->get('canWritePost');

        // View에 추가로 표시할 정보
        return [
            'searchForm' => $this->searchForm($boardName),
            'routes' => [
                'list'  => (!$canReadPost)  ? null : RequestFacade::url(),
                'write' => (!$canWritePost) ? null : '#'
            ]
        ];
    }
}
