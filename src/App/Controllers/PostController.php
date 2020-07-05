<?php

namespace Inium\Laraboard\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\Post;
use Inium\Laraboard\App\User;
use Inium\Laraboard\App\Board\BoardUserRoles;
use Inium\Laraboard\App\Board\RenderTemplateTrait;
use Inium\Laraboard\App\Board\Request\PostRequest;
use Inium\Laraboard\App\Middleware\PostAccessMiddleware;
use Inium\Laraboard\App\Middleware\PostStoreMiddleware;
use Inium\Laraboard\App\Middleware\PostOwnerMiddleware;
use Inium\Laraboard\Support\Detect\Agent;

class PostController extends Controller
{
    use RenderTemplateTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // 게시글 목록 / 검색결과, 게시글 읽기 가능한지 여부 체크
        $this->middleware(PostAccessMiddleware::class)
             ->only(['index', 'show']);

        // 게시글 쓰기 가능한지 여부 체크
        $this->middleware(PostStoreMiddleware::class)
             ->only(['create', 'store']);

        // 게시글 수정, 삭제 가능한지 여부 체크
        $this->middleware(PostOwnerMiddleware::class)
             ->only(['edit', 'update', 'destroy']);
    }

    /**
     * 게시글 목록 or 검색 결과 페이지
     *
     * @param  Request $request     Request
     * @param  string $boardName    게시판 이름
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $boardName)
    {
        $query = $request->query('query', null); // 검색어
        $page = $request->query('page', 1); // 페이지 번호

        $params = $this->listOrSearch($boardName, $page, $query);

        $viewParams = [
            'role'  => BoardUserRoles::roles($boardName),
            'query' => $query,
            'page'  => $page
        ];

        return $this->render('posts', array_merge($params, $viewParams));
    }

    /**
     * 게시글 쓰기 페이지
     *
     * @param Request $request      Request
     * @param string $boardName     게시판 이름
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, string $boardName)
    {
        $params = [
            'board' => Board::findByName($boardName),
            'roles' => BoardUserRoles::roles($boardName)
        ];

        return $this->render('postWrite', $params);
    }

    /**
     * 게시글을 저장한다.
     *
     * @param  PostRequest $request     Request
     * @param  string $boardName        게시판 이름
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request, string $boardName)
    {
        // 게시글 저장
        $postId = $this->storePost($request, $boardName);

        // 게시글 저장에 성공한 경우, 게시글 보기 페이지로 이동
        if ($postId) {
            return redirect()->route('board.post.show', [
                            'boardName' => $boardName,
                            'postId' => $postId
                        ]);
        }
        // 게시글 저장에 실패한 경우, 게시글 쓰기 페이지로 이동
        else {
            $errorMessage = '게시글 저장에 실패하였습니다. 다시 시도해주세요.';
            return redirect()->route('board.post.create', [
                            'boardName' => $boardName
                        ])
                        ->withErrors(array($errorMessage))
                        ->withInput();
        }
    }

    /**
     * 게시글을 출력한다.
     *
     * @param  Request $request     Request
     * @param  string $boardName    게시판 이름
     * @param  int  $postId         게시글 ID
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $boardName, int $postId)
    {
        $query = $request->query('query', null); // 검색어
        $page = $request->query('page', 1); // 페이지 번호

        // 게시글 Get
        $board = Board::findByName($boardName);
        $post = $board->getPost($postId);

        // 조회수 1 증가
        $post->incrementViewCount();

        // 사용자 Role
        $params = [
            'post'     => $post,
            'list'     => $this->listOrSearch($boardName, $page, $query),
            'query'    => $query,
            'page'     => $page,
            'role'     => BoardUserRoles::roles($boardName)
        ];

        return $this->render('post', $params);
    }

    /**
     * 게시글 수정 페이지
     *
     * @param  Request $request     Request
     * @param  string $boardName    게시판 이름
     * @param  int $postId          게시글 ID
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, string $boardName, int $postId)
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
     * @param  PostRequest $request     Request
     * @param  string $boardName        게시판 이름
     * @param  int $postId              게시글 ID
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, string $boardName, int $postId)
    {
        // 게시글 갱신
        $updated = $this->updatePost($request, $postId);

        // 게시글 저장에 성공한 경우
        if ($updated) {
            // 성공 메시지 추가
            Session::flash('message', '게시글 수정이 완료되었습니다.');
            Session::flash('alert-class', 'alert-success');

            return redirect()->route('board.post.show', [
                            'boardName' => $boardName,
                            'postId' => $postId
                        ]);
        }
        // 게시글 저장에 실패한 경우
        else {
            $errorMessage = '게시글 수정에 실패하였습니다. 다시 시도해주세요.';
            return redirect()->route('board.post.edit', [
                            'boardName' => $boardName,
                            'postId' => $postId
                        ])
                        ->withErrors(array($errorMessage))
                        ->withInput();
        }
    }

    /**
     * 게시글 삭제
     *
     * @param  Request $request     Request
     * @param  string $boardName    게시판 이름
     * @param  int $postId          게시글 ID
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, string $boardName, int $postId)
    {
        // Get post
        $post = Post::find($postId);
        $post->delete();
 
        // 게시글 삭제정보
        $flashMessage = "게시글 \"{$post->subject}\" 이(가) 삭제되었습니다.";
 
        Session::flash('message', $flashMessage);
        Session::flash('alert-class', 'alert-danger');
 
        return redirect()->route('board.post.index', [
            'boardName' => $boardName
        ]);
    }

    /**
     * 게시글 목록을 반환한다.
     *
     * @param  string $boardName    게시판 이름
     * @param  integer $page        페이지 번호
     * @return array
     */
    private function list(string $boardName, int $page = 1): array
    {
        $board = Board::findByName($boardName);
        $notices = $board->getNotices();
        $posts = $board->getPosts($page);

        return [
            'board' => $board,
            'notices' => $notices,
            'posts' => $posts
        ];
    }

    /**
     * 검색 결과를 반환한다.
     *
     * @param  string $boardName    게시판 이름
     * @param  string $query        검색어
     * @param  integer $page        페이지 번호
     * @return array
     */
    private function search(string $boardName,
                            string $query,
                            int $page = 1): array
    {
        $board = Board::findByName($boardName);
        $search = $board->search($query, $page);

        return [
            'board' => $board,
            'search' => $search
        ];
    }

    /**
     * 게시글 목록 혹은 검색 결과를 반환한다.
     *
     * @param  string $boardName    게시판 이름
     * @param  integer $page        페이지 번호
     * @param  string $query        검색어
     * @return array
     */
    private function listOrSearch(string $boardName,
                                  int $page = 1,
                                  string $query = null): array
    {
        return $query ? $this->search($boardName, $query, $page)
                      : $this->list($boardName, $page);
    }

    /**
     * 게시글을 저장한다.
     *
     * @param  PostRequest $request Request
     * @param  string $boardName    게시판 이름
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

        $post->ip_address = encrypt($request->ip());
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
        $post->updated_at = null; // 글 추가 시 updated_at 무시
        $post->board()->associate($board);
        $post->user()->associate($user);

        $post->save();

        return $post->id;
    }

    /**
     * 게시글을 수정한다.
     *
     * @param PostRequest $request      Request
     * @param integer $postId           게시글 ID
     * @return boolean
     */
    private function updatePost(PostRequest $request, int $postId): bool
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

        $post->ip_address = encrypt($request->ip());
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

        $updated = $post->save();

        return $updated;
    }
}
