<?php
namespace Inium\Laraboard\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
// use Inium\Laraboard\Core\Board\PostTrait;
// use Inium\Laraboard\Core\Board\PostListTrait;
// use Inium\Laraboard\Core\Board\PostSearchTrait;
// use Inium\Laraboard\Core\Board\PostDeleteTrait;
// use Inium\Laraboard\Core\Board\CommentListTrait;
use Inium\Laraboard\App\Middleware\PostAccessMiddleware;
use Inium\Laraboard\App\Board\RenderTemplateTrait;
// use Inium\Laraboard\Models\Board as LaraboardBoard;
// use Inium\Laraboard\Models\Post as LaraboardPost;

class PostController extends Controller
{
    // use PostTrait, PostListTrait, PostSearchTrait, PostDeleteTrait,
    //     CommentListTrait,
    //     RenderTemplateTrait;

    use RenderTemplateTrait;

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
        dd('qwer');
        $query = $request->query('query', null); // 검색어
        $page = $request->query('page', 1);      // 페이지 번호
        $type = $request->query('type', null);   // 검색 유형

        // $board = LaraboardBoard::findByName($boardName);
        // $post = $board->getPost($id);

        // $params = [
        //     'board' => $board,
        //     'post' => $post,
        //     'postList' => [
        //         'notices' => $board->notices(),
        //         'posts' => $board->postList($page)
        //     ],
        //     'comments' => $post->getHierarchicalComments(),
        //     'query' => $query,
        //     'type' => $type,
        //     'page' => ($page == 1 ? null : $page),
        //     'searchTypes' => LaraboardBoard::getSearchTypes()
        // ];

        // return $this->render('post', $params);
    }
}
