<?php

namespace App\Http\Controllers\Laraboard;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Inium\Laraboard\Support\Detect\Agent;
use App\Models\Laraboard\Post;
use App\Models\Laraboard\Comment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Laraboard\Comment\ListCommentRequest;
use App\Http\Requests\Laraboard\Comment\StoreCommentRequest;
use App\Http\Requests\Laraboard\Comment\UpdateCommentRequest;

class CommentController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Authorization: Basic bGFyYWJvYXJkQGV4YW1wbGUubmV0OnBhc3N3b3Jk
        $this->middleware("auth.basic")->only(["store", "update", "destroy"]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\Laraboard\Comment\ListCommentRequest  $request
     * @param  string $boardName    게시판 이름
     * @param  int $postId          게시글 ID
     * @return \Illuminate\Http\Response
     */
    public function index(
        ListCommentRequest $request,
        string $boardName,
        int $postId
    ) {
        // 200 OK (댓글 목록 반환, 없을 경우 empty 반환)
        // 404 Not Found (게시판에 게시글이 존재하지 않을 경우)
        // 422 Unprocessable Entity (Form Validation Fail)
        try {
            // 게시글 조회
            $post = Post::whereHas(
                "board",
                fn($q) => $q->where("name", $boardName)
            )->findOrFail($postId);

            $v = $request->all();

            // 댓글 목록
            $coll = Comment::withCount(["parent", "children"])
                ->with([
                    "user" => fn($q) => $q->select("id", "email", "name"),
                    "board" => fn($q) => $q->select("id", "name", "name_ko"),
                    "post" => fn($q) => $q->select("id", "subject"),
                    "parent" => fn($q) => $q->select("id"),
                ])
                ->where(
                    fn($q) => $q
                        ->where("stripped_content", "LIKE", "%{$v["query"]}%")
                        ->where("parent_comment_id", $v["parent"])
                )
                ->whereHas("post", fn($q) => $q->where("id", $postId))
                ->orderBy("id", "ASC")
                ->paginate($post->board->comments_per_page);

            // 댓글 목록 + 페이지네이션 반환
            return $this->json([
                "items" => $coll->items(),
                "total" => $coll->total(),
                "current_page" => $coll->currentPage(),
                "last_page" => $coll->lastPage(),
                "per_page" => $coll->perPage(),
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->json($e->getMessage(), $e->status);
        } catch (ValidationException $e) {
            return $this->json($e->errors(), $e->status);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Laraboard\Comment\StoreCommentRequest $request
     * @param  string $boardName    게시판 이름
     * @param  int $postId          게시글 ID
     * @return \Illuminate\Http\Response
     */
    public function store(
        StoreCommentRequest $request,
        string $boardName,
        int $postId
    ) {
        // 201 Created (댓글 등록 완료)
        // 401 Unauthorized (인증한 사용자만 작성 가능)
        // 422 Unprocessable Entity (Form Validation Fail)
        try {
            // 게시글 정보 조회
            $post = Post::whereHas(
                "board",
                fn($q) => $q->where("name", $boardName)
            )->findOrFail($postId);

            $user = Auth::user();

            $v = $request->all();
            $tags = config("laraboard.allow_comment_content_tags");
            $ua = Agent::parse(
                config("laraboard.collect_user_info") // 사용자 UA 수집여부
                    ? $request->server("HTTP_USER_AGENT")
                    : null
            );

            // 댓글 추가
            $comment = Comment::create([
                "ip_address" => encrypt($request->ip()),
                "user_agent" => encrypt($ua->agent),
                "device_type" => $ua->device_type,
                "os_name" => $ua->os_name,
                "os_ver" => $ua->os_version,
                "browser_name" => $ua->browser_name,
                "browser_ver" => $ua->browser_version,
                "content" => htmlspecialchars(strip_tags($v["content"], $tags)),
                "stripped_content" => strip_tags($v["content"]),
                "points" => $post->board->comment_points,
                "parent_comment_id" => $v["parent_comment_id"],
                "post_id" => $post->id,
                "board_id" => $post->board->id,
                "wrote_user_id" => $user->id,
            ]);

            // 성공: 201 Created
            return response()->noContent(Response::HTTP_CREATED, [
                "Location" => action(
                    [CommentController::class, "show"],
                    [
                        "boardName" => $post->board->name,
                        "postId" => $post->id,
                        "commentId" => $comment->id,
                    ]
                ),
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->json("Not Found", Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $boardName   게시판 이름
     * @param  int  $postId         게시글 ID
     * @param  int  $commentId      댓글 ID
     * @return \Illuminate\Http\Response
     */
    public function show(string $boardName, int $postId, int $commentId)
    {
        // 200 OK (댓글 조회 성공)
        // 404 Not Found (댓글 없음)
        try {
            // 댓글 조회
            $comment = Comment::withCount("children")
                ->with([
                    "user" => fn($q) => $q->select("id", "email", "name"),
                    "board" => fn($q) => $q->select("id", "name", "name_ko"),
                    "post" => fn($q) => $q->select("id", "subject"),
                    "parent" => fn($q) => $q->select("id"),
                ])
                ->whereHas("board", fn($q) => $q->where("name", $boardName))
                ->whereHas("post", fn($q) => $q->where("id", $postId))
                ->findOrFail($commentId);

            return $this->json($comment);
        } catch (ModelNotFoundException $e) {
            return $this->json("Not Found", Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Laraboard\Comment\UpdateCommentRequest $request
     * @param  string $boardName    게시판 이름
     * @param  int $postId          게시글 ID
     * @param  int $commentId       댓글 ID
     * @return \Illuminate\Http\Response
     */
    public function update(
        UpdateCommentRequest $request,
        string $boardName,
        int $postId,
        int $commentId
    ) {
        // 200 OK (수정완료)
        // 401 Unauthorized (수정권한 없음)
        // 404 Not Found (댓글 없음),
        try {
            $user = Auth::user();

            // 댓글 정보 Get
            $comment = Comment::withCount("children")
                ->with(["user" => fn($q) => $q->select("id", "email")])
                ->whereHas("board", fn($q) => $q->where("name", $boardName))
                ->whereHas("post", fn($q) => $q->where("id", $postId))
                ->findOrFail($commentId);

            // 댓글 작성자와 로그인한 사용자가 다를 경우 수정 불가
            if ($user->id !== $comment->user->id) {
                throw new UnauthorizedException("Unauthorized");
            }

            $v = $request->all();
            $tags = config("laraboard.allow_comment_content_tags");
            $ua = Agent::parse(
                config("laraboard.collect_user_info") // 사용자 UA 수집여부
                    ? $request->server("HTTP_USER_AGENT")
                    : null
            );

            // 댓글 갱신
            $affectedRows = $comment->update([
                "ip_address" => encrypt($request->ip()),
                "user_agent" => encrypt($ua->agent),
                "device_type" => $ua->device_type,
                "os_name" => $ua->os_name,
                "os_ver" => $ua->os_version,
                "browser_name" => $ua->browser_name,
                "browser_ver" => $ua->browser_version,
                "content" => htmlspecialchars(strip_tags($v["content"], $tags)),
                "stripped_content" => strip_tags($v["content"]),
            ]);

            // 업데이트 성공: 200 OK
            return $this->json($affectedRows);
        } catch (ModelNotFoundException $e) {
            return $this->json("Not Found", Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $boardName     게시판 이름
     * @param int $postId           게시글 ID
     * @param int $commentId        삭제 대상 댓글 ID
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $boardName, int $postId, int $commentId)
    {
        // 204 No Content(삭제완료)
        // 401 Unauthorized
        // 404 Not Found(게시글 없음),
        // 409 Conflict(자식 댓글 존재 시 삭제 불가)
        try {
            // 댓글 정보 Get
            $comment = Comment::withCount("children")
                ->with(["user" => fn($q) => $q->select("id", "email")])
                ->whereHas("board", fn($q) => $q->where("name", $boardName))
                ->whereHas("post", fn($q) => $q->where("id", $postId))
                ->findOrFail($commentId);

            // 댓글 작성자와 로그인한 사용자가 다를 경우 삭제 불가
            if (Auth::user()->id !== $comment->user->id) {
                throw new UnauthorizedException("Unauthorized");
            }

            // 대댓글이 존재하는 경우 삭제 불가
            if ($comment->children_count > 0) {
                $message = "Child comment exists";
                throw new HttpException(Response::HTTP_CONFLICT, $message);
            }

            // 댓글 삭제
            $comment->delete();

            // 삭제 성공: 204 No Content 반환
            return response()->noContent(Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            return $this->json("Not Found", Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Response 결과를 반환한다.
     *
     * @param string|array $data    반환할 데이터
     * @param integer $statusCode   상태 코드
     * @return \Illuminate\Http\Response
     */
    private function json($data, int $statusCode = 200)
    {
        $body = $data;
        if (!is_array($data)) {
            $body = ["message" => $data];
        }

        return response()->json($body, $statusCode);
    }
}
