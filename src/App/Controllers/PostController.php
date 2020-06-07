<?php
namespace Inium\Laraboard\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request as RequestFacade;
use Inium\Laraboard\App\Board\PostTrait;
use Inium\Laraboard\App\Board\PostListTrait;
use Inium\Laraboard\App\Board\PostSearchTrait;
// use Inium\Laraboard\Core\Board\PostDeleteTrait;
// use Inium\Laraboard\Core\Board\CommentListTrait;
use Inium\Laraboard\App\Board\RenderTemplateTrait;
use Inium\Laraboard\App\Board\SearchFormTrait;
use Inium\Laraboard\App\Middleware\PostAccessMiddleware;

class PostController extends Controller
{
    use PostListTrait, PostTrait, RenderTemplateTrait, SearchFormTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(PostAccessMiddleware::class);
    }

    /**
     * 게시글을 출력한다.
     * -------------------------------------------------------------------------
     * GET [/{$prefix}]/board/{boardName}/{id}?page=1&query=lorem&type=subcon
     * 
     * Route params
     * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
     * @param string boardName  게시판 영문 이름.
     * @param integer id        게시글 ID
     * 
     * Query params (Optional)
     * @param int    page       페이지 번호. 기본 1.
     * @param string query      검색어
     * @param string type       검색 유형. LaraboardBoard 모델 참조.
     * -------------------------------------------------------------------------
     * 
     * @param Request $request  Request
     * @param string $boardName 게시판 이름
     * @param integer $id       게시글 ID
     */
    public function index(Request $request, string $boardName, int $id)
    {
        $query = $request->query('query', null); // 검색어
        $page = $request->query('page', 1);      // 페이지 번호
        $type = $request->query('type', null);   // 검색 유형

        $viewParams = [
            'post' => $this->post($boardName, $id),
            // 'comment' => $comment,
            'list' => $this->listOrSearch($boardName, $page, $type, $query)
        ];

        return $this->render('post', $viewParams);
    }

    /**
     * 게시글 목록 혹은 검색 결과를 반환한다.
     *
     * @param string $boardName     게시판 이름
     * @param integer $page         페이지 번호
     * @param string $type          검색 유형
     * @param string $query         검색어
     * @return array
     */
    private function listOrSearch(string $boardName,
                                  int $page = 1,
                                  string $type = null,
                                  string $query = null): array
    {
        $params = null;

         // 검색어가 존재하는 경우: 검색결과 반환
        if ($query) {
            if (is_null($type)) {
                $type = $this->getDefaultSearchType();
            }

            $params = $this->search($boardName, $query, $type, $page);
        }
        // 그 외: 게시글 목록 반환
        else {
            $params = $this->list($boardName, $page);
        }

        $viewParams = $this->getViewParams($boardName);

        return array_merge($params, $viewParams);
    }

    /**
     * 추가로 View에 표시할 정보를 반환한다.
     *
     * @param Request $request      Request
     * @param string $boardName     게시판 이름
     * @return array
     */
    private function getViewParams(string $boardName): array
    {
        // 미들웨어(Middleware)에서 저장한 사용자 역할(Role) 정보를 가져온다.
        $canReadPost = RequestFacade::get('canReadPost');
        $canWritePost = RequestFacade::get('canWritePost');

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
