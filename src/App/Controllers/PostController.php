<?php
namespace Inium\Laraboard\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inium\Laraboard\App\Board\BoardUserRolesTrait;
// use Inium\Laraboard\App\Board\PostListTrait;
use Inium\Laraboard\App\Board\PostsTrait;
use Inium\Laraboard\App\Board\PostTrait;
// use Inium\Laraboard\App\Board\SearchTrait;
// use Inium\Laraboard\App\Board\RouteTrait;
use Inium\Laraboard\App\Board\RenderTemplateTrait;
use Inium\Laraboard\App\Middleware\PostMiddleware;

class PostController extends Controller
{
    use PostsTrait, PostTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(PostMiddleware::class)->only('index');
    }

    /**
     * 게시글을 출력한다.
     * -------------------------------------------------------------------------
     * GET [/{$prefix}]/board/{boardName}/{id}?page=1&query=lorem
     * 
     * Route params
     * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
     * @param string boardName  게시판 영문 이름.
     * @param integer id        게시글 ID
     * 
     * Query params (Optional)
     * @param int    page       페이지 번호. 기본 1.
     * @param string query      검색어
     * -------------------------------------------------------------------------
     * 
     * @param Request $request  Request
     * @param string $boardName 게시판 이름
     * @param integer $id       게시글 ID
     */
    public function get(Request $request, string $boardName, int $id)
    {
        $query = $request->query('query', null); // 검색어
        $page = $request->query('page', 1);      // 페이지 번호

        dd('qwer');

    //     // $temp= $this->isPostOwner($boardName, $id);
    //     // dd($temp);

    //     $viewParams = [
    //         'post' => $this->post($boardName, $id),
    //         'list' => $this->listOrSearch($boardName, $query, $page)
    //     ];

    //     dd($viewParams);

    //     // return $this->render('post', $viewParams);
    }

    public function delete(string $boardName, int $id)
    {

    }

    // /**
    //  * 게시글 목록 혹은 검색 결과를 반환한다.
    //  *
    //  * @param string $boardName     게시판 이름
    //  * @param string $query         검색어
    //  * @param integer $page         페이지 번호
    //  * @return array
    //  */
    // private function listOrSearch(string $boardName,
    //                               string $query = null,
    //                               int $page = 1): array
    // {
    //     $params = null;

    //     // 검색어가 존재할 경우, 검색결과 반환
    //     if ($query) {
    //         $params = $this->search($boardName, $query, $page);
    //     }
    //     // 게시글 목록 반환
    //     else {
    //         $params = $this->postList($boardName, $page);
    //     }

    //     $viewParams = $this->viewListSearchParams($boardName, $query, $page);

    //     return array_merge($params, $viewParams);
    // }

    // /**
    //  * View에 사용할 사용자 Role에 따른 Route 정보, 검색 form 정보를 반환한다.
    //  *
    //  * @param string $boardName     게시판 이름
    //  * @param string $query         검색어
    //  * @param int $page             게시글 페이지 번호
    //  * @return array
    //  */
    // private function viewListSearchParams(string $boardName,
    //                                       string $query = null,
    //                                       int $page = 1
    //                                       ): array
    // {
    //     $boardUserRoles = $this->boardUserRoles($boardName);
    //     $canWrite = $boardUserRoles->post->canWrite;

    //     $routes = [
    //         'list' => $this->routeListSearch($boardName, $page, $query),
    //         'write' => $canWrite ? $this->routePostWrite($boardName) : null,
    //         'modify' => null,
    //         'delete' => null
    //     ];

    //     return [
    //         'routes' => $routes,
    //         'searchForm' => $this->searchForm($boardName)// 로그인 사용자만 가능
    //     ];
    // }
}
