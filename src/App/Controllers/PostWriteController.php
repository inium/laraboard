<?php

namespace Inium\Laraboard\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\Post;
use Inium\Laraboard\App\User;
use Inium\Laraboard\App\Board\BoardUserRoles;
use Inium\Laraboard\App\Board\RenderTemplateTrait;
use Inium\Laraboard\App\Board\Request\PostRequest;
use Inium\Laraboard\App\Middleware\PostWriteMiddleware;
use Inium\Laraboard\Support\Facades\Agent;

class PostWriteController extends Controller
{
    use RenderTemplateTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // 게시글 쓰기 미들웨어
        $this->middleware(PostWriteMiddleware::class);
    }

    /**
     * 게시글 쓰기 페이지
     * 
     * -------------------------------------------------------------------------
     * GET [/{$prefix}]/board/{boardName}/write
     * 
     * Route params
     * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
     * @param string boardName  게시판 영문 이름.
     * -------------------------------------------------------------------------
     */
    public function view(Request $request, string $boardName)
    {
        $params = [
            'board' => Board::findByName($boardName),
            'roles' => BoardUserRoles::roles($boardName)
        ];

        return $this->render('postWrite', $params);
    }

    /**
     * 게시글 저장
     * 
     * -------------------------------------------------------------------------
     * POST [/{$prefix}]/board/{boardName}/write
     * 
     * subject=lorem
     * content=<p>dolor</p>
     * 
     * Route params
     * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
     * @param string boardName  게시판 영문 이름.
     * 
     * Post params
     * @param string subject 게시글 제목
     * @param string content 게시글 내용 (HTML)
     * -------------------------------------------------------------------------
     */
    public function post(PostRequest $request, string $boardName)
    {
        // 게시글 저장
        $postId = $this->storePost($request, $boardName);

        // 게시글 저장에 성공한 경우, 게시글 보기 페이지로 이동
        if ($postId) {
            return redirect()->route('board.post.view', [
                            'boardName' => $boardName,
                            'id' => $postId
                        ]);
        }
        // 게시글 저장에 실패한 경우, 게시글 쓰기 페이지로 이동
        else {
            $errorMessage = '게시글 저장에 실패하였습니다. 다시 시도해주세요.';
            return redirect()->route('board.post.write.view', [
                            'boardName' => $boardName
                        ])
                        ->withErrors(array($errorMessage))
                        ->withInput();
        }
    }

    /**
     * 게시글을 저장한다.
     *
     * @param PostRequest $request  Request
     * @param string $boardName     게시판 이름
     * @return integer              게시글 ID
     */
    private function storePost(PostRequest $request, string $boardName): int
    {
        $board = Board::findByName($boardName);
        $user = User::findByUserId(Auth::id());

        // Get User Agent
        $ua = Agent::parse($request->server('HTTP_USER_AGENT'));

        // 공지 여부는 관리자만 적용
        $notice = false;
        if ($user->role->is_admin) {
            $notice = $request->notice ? true : false;
        }

        $post = new Post();

        $post->user_agent = $ua->agent;
        $post->device_type = $ua->device_type;
        $post->os_name = $ua->os_name;
        $post->os_version = $ua->os_version;
        $post->browser_name = $ua->browser_name;
        $post->browser_version = $ua->browser_version;
        $post->notice = $notice;
        $post->subject = strip_tags($request->subject);
        $post->content = htmlspecialchars($request->content);
        $post->content_pure = strip_tags($request->content);
        $post->view_count = 0;
        $post->point = $board->post_point;
        $post->board()->associate($board);
        $post->user()->associate($user);

        $post->save();

        return $post->id;
    }
}
