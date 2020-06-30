<?php

namespace Inium\Laraboard\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inium\Laraboard\App\Board\BoardUserRoles;
use Inium\Laraboard\App\Post;
use Inium\Laraboard\App\User;
use Inium\Laraboard\App\Board\RenderTemplateTrait;
use Inium\Laraboard\App\Board\Request\PostRequest;
use Inium\Laraboard\App\Middleware\PostModifyMiddleware;
use Inium\Laraboard\Support\Facades\Agent;

class PostModifyController extends Controller
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
        $this->middleware(PostModifyMiddleware::class);
    }

    /**
     * 게시글 수정 페이지
     * 
     * -------------------------------------------------------------------------
     * GET [/{$prefix}]/board/{boardName}/modify/{postId}
     * 
     * Route params
     * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
     * @param string boardName  게시판 영문 이름.
     * @param integer postId    게시글 ID
     * -------------------------------------------------------------------------
     */
    public function view(Request $request, string $boardName, int $postId)
    {
        $params = [
            'post' => Post::find($postId),
            'roles' => BoardUserRoles::roles($boardName)
        ];

        return $this->render('postModify', $params);
    }

    /**
     * 게시글 수정
     * 
     * -------------------------------------------------------------------------
     * PUT [/{$prefix}]/board/{boardName}/modify/{postId}
     * 
     * subject=lorem
     * content=<p>ipsum</p>
     * 
     * Route params
     * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
     * @param string boardName  게시판 영문 이름.
     * @param integer postId    게시글 ID
     * 
     * Put params
     * @param string subject 게시글 제목
     * @param string content 게시글 내용 (HTML)
     * -------------------------------------------------------------------------
     */
    public function put(PostRequest $request, string $boardName, int $postId)
    {
        // 게시글 갱신
        $updated = $this->putPost($request, $postId);

        // 게시글 저장에 성공한 경우
        if ($updated) {
            // 성공 메시지 추가
            Session::flash('message', '게시글 수정이 완료되었습니다.');
            Session::flash('alert-class', 'alert-success');

            return redirect()->route('board.post.view', [
                            'boardName' => $boardName,
                            'postId' => $id
                        ]);
        }
        // 게시글 저장에 실패한 경우
        else {
            $errorMessage = '게시글 수정에 실패하였습니다. 다시 시도해주세요.';
            return redirect()->route('board.post.modify.view', [
                            'boardName' => $boardName,
                            'postId' => $id
                        ])
                        ->withErrors(array($errorMessage))
                        ->withInput();
        }
    }

    /**
     * 게시글을 수정한다.
     *
     * @param PostRequest $request      Request
     * @param integer $postId           게시글 ID
     * @return boolean
     */
    private function putPost(PostRequest $request, int $postId): bool
    {
        $post = Post::find($postId);
        $user = User::findByUserId(Auth::id());

        // Get User Agent
        $ua = Agent::parse($request->server('HTTP_USER_AGENT'));

        // 공지 여부는 관리자만 적용
        $notice = false;
        if ($user->role->is_admin) {
            $notice = $request->notice ? true : false;
        }

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
        // $post->view_count = 0;
        // $post->point = $board->post_point;
        // $post->board()->associate($board);
        // $post->user()->associate($user);

        $updated = $post->save();

        return $updated;
    }
}
