<?php

namespace Inium\Laraboard\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\Post;

use Inium\Laraboard\App\Board\BoardUserRoles;
use Inium\Laraboard\App\Board\CommentTrait;
use Inium\Laraboard\App\Board\PostTrait;
use Inium\Laraboard\App\Board\SearchTrait;
use Inium\Laraboard\App\Board\RenderTemplateTrait;
use Inium\Laraboard\App\Middleware\PostViewMiddleware;
use Inium\Laraboard\App\Middleware\PostDeleteMiddleware;

class PostController extends Controller
{
    use CommentTrait, PostTrait, SearchTrait, RenderTemplateTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // 게시글 보기 미들웨어
        $this->middleware(PostViewMiddleware::class)->only('view');

        // 게시글 삭제 미들웨어
        $this->middleware(PostDeleteMiddleware::class)->only('delete');
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
    public function view(Request $request, string $boardName, int $id)
    {
        $query = $request->query('query', null); // 검색어
        $page = $request->query('page', 1);      // 페이지 번호

        // 게시글 Get
        $post = Board::findByName($boardName)->getPost($id);

        // 조회수 1 증가
        $post->incrementViewCount();

        $params = [
            'post'     => $post,
            'comments' => $this->getComments($boardName, $id),
            'list'     => $this->listOrSearch($boardName, $page, $query),
            'query'    => $query,
            'page'     => $page,
            'role'     => BoardUserRoles::roles($boardName),
        ];

        return $this->render('post', $params);
    }

    /**
     * 게시글 삭제
     * 
     * -------------------------------------------------------------------------
     * DELETE [/{$prefix}]/board/{boardName}/{id}
     * 
     * Route params
     * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
     * @param string boardName  게시판 영문 이름.
     * @param integer id        게시글 ID.
     * -------------------------------------------------------------------------
     *
     * @param Request $request  Request
     * @param string boardName  게시판 이름
     * @param integer id        게시글 ID
     */
    public function delete(Request $request, string $boardName, int $id)
    {
        // Get post
        $post = Post::find($id);
        $post->delete();

        // 게시글 삭제정보
        $flashMessage = "게시글 \"{$post->subject}\" 이(가) 삭제되었습니다.";

        Session::flash('message', $flashMessage);
        Session::flash('alert-class', 'alert-danger');

        return redirect()->route('board.postListSearch.view', [
            'boardName' => $boardName
        ]);
    }


    /**
     * 게시글 목록 or 검색 결과 목록을 가져온다.
     *
     * @param string $boardName     게시판 름름
     * @param integer $page         페이지 번호
     * @param string $query         검색어
     * @return array
     */
    private function listOrSearch(string $boardName,
                                  int $page = 1,
                                  string $query = null): array
    {
        // Get 검색 결과 or 게시글 목록
        $params = $query ? $this->getSearchResult($boardName, $query, $page) :
                           $this->getPostList($boardName, $page);

        $viewParams = [
            'role'  => BoardUserRoles::roles($boardName),
            'query' => $query,
            'page'  => $page
        ];

        return array_merge($params, $viewParams);
    }
}
