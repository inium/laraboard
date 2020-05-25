<?php

namespace App\Http\Controllers\Laraboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inium\Laraboard\Core\Templates\RenderTemplateTrait;
use Inium\Laraboard\Middleware\ListAccessMiddleware;
use Inium\Laraboard\Models\Board as LaraboardBoard;
// use Inium\Laraboard\Support\Collection;

class ListController extends Controller
{
    use RenderTemplateTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(ListAccessMiddleware::class);
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
     * @param string type       검색 유형. LaraboardBoard 모델 참조.
     * -------------------------------------------------------------------------
     * 
     * @param Request $request      Request
     * @param string $boardName     게시판 이름
     * @return void
     */
    public function index(Request $request, string $boardName)
    {
        $query = $request->query('query', null); // 검색어
        $page = $request->query('page', 1); // 페이지 번호

        // 검색어가 존재하는 경우: 검색결과 반환
        if ($query) {
            // 검색 유형. 없을 경우 기본 타입 지정.
            $type = $request->query('type', $this->defaultSearchType);

            return $this->search($boardName, $query, $type, $page);
        }
        // 그 외: 게시글 목록 반환
        else {
            return $this->list($boardName, $page);
        }
    }

    /**
     * 게시글 목록을 렌더링 한다.
     *
     * @param string $boardName     게시판 이름
     * @param integer $page         페이지 번호
     */
    private function list(string $boardName, int $page = 1)
    {
        $board = LaraboardBoard::findByName($boardName);
        $notices = $board->getNotices();
        $posts = $board->getPosts($page);
        $pagination = $posts->toArray();

        // Pagination에 포함된 Data 제거
        unset($pagination['data']);

        $params = [
            'board' => [
                'name' => $board->name,
                'name_ko' => $board->name_ko,
                'description' => $board->description,
                // 'privilege' => [
                //     'can_write' => $board->min_
                // ]
            ],
            'notices' => $notices->laraboardPostListBrief($page),
            'posts' => $posts->laraboardPostListBrief($page),
            'paginate' => $posts->toArray(),
            'searchTypes' => LaraboardBoard::getSearchTypes(),
            'query' => [
                'page' => ($page == 1 ? null : $page)
            ]
        ];

        return $this->render('list', $params);
    }

    /**
     * 게시글 검색 결과를 렌더링 한다.
     *
     * @param string $boardName     게시판 이름
     * @param string $query         검색어
     * @param string $type          검색 유형. PostSearchTrait 참조.
     * @param integer $page         페이지 번호
     */
    private function search(string $boardName,
                            string $query,
                            string $type,
                            int $page = 1)
    {
        // 검색 유형이 올바르게 입력되었는지 확인
        // 올바르지 않으면 기본 타입(subcon) 으로 설정
        $isValidType = LaraboardBoard::isValidSearchType($type);
        if (!$isValidType) {
            $type = key(LaraboardBoard::getDefaultSearchType());
        }

        $board = LaraboardBoard::findByName($boardName);
        // $search = $board->search($query, $type, $page);







    
    //     $validType = $this->validSearchTypes($type);
    //     if (!$validType) {
    //         $type = $this->defaultSearchType;
    //     }

    //     $search = $this->getPostSearch($boardName, $query, $page);

    //     $params = [
    //         'board' => $search['board'],
    //         'posts' => $search['posts'],
    //         'query' => $query,
    //         'page' => ($page == 1 ? null : $page),
    //         'searchTypes' => $this->getSearchTypes()
    //     ];

    //     return $this->render('search', $params);
    }
}
