<?php

namespace App\Http\Controllers\Laraboard;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\Support\Detect\Agent;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Laraboard\Post;
use App\Models\Laraboard\Board;
use App\Http\Controllers\Controller;
use App\Http\Requests\Laraboard\Post\ListPostRequest;
use App\Http\Requests\Laraboard\Post\StorePostRequest;
use App\Http\Requests\Laraboard\Post\UpdatePostRequest;

class PostController extends Controller
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
     * @param  \App\Http\Requests\Laraboard\ListPostRequest  $request
     * @param  string $boardName    게시판 이름
     * @return \Illuminate\Http\Response
     */
    public function index(ListPostRequest $request, string $boardName)
    {
        // 200 OK (게시글 목록 반환, 없을 경우 empty 반환)
        // 404 Not Found (게시판이 존재하지 않을 경우)
        // 422 Unprocessable Entity (Form Validation Fail)
        try {
            // 게시판 정보 Get
            $board = Board::where("name", "{$boardName}")->firstOrFail();

            $v = $request->all();

            // 게시글 목록 조회
            $coll = Post::withCount(["comments"])
                ->with([
                    "board" => fn($q) => $q->select("id", "name", "name_ko"),
                    "user" => fn($q) => $q->select("id", "name"),
                ])
                ->whereHas("board", fn($q) => $q->where("name", $boardName))
                ->where("notice", $v["notice"])
                ->where(
                    fn($q) => $q
                        ->where("subject", "LIKE", "%{$v["query"]}%")
                        ->orWhere("stripped_content", "LIKE", "%{$v["query"]}%")
                )
                ->orderBy("id", "DESC")
                ->paginate($board->posts_per_page);

            // 게시글 목록 + 페이지네이션 반환
            return $this->json([
                "items" => $coll->items(),
                "total" => $coll->total(),
                "current_page" => $coll->currentPage(),
                "last_page" => $coll->lastPage(),
                "per_page" => $coll->perPage(),
            ]);
        } catch (ValidationException $e) {
            $err = $e->errors();
            return $this->json($err, Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ModelNotFoundException $e) {
            return $this->json("Not Found", Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Laraboard\StorePostRequest  $request
     * @param  string $boardName    게시판 이름
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request, string $boardName)
    {
        // 201 Created (게시글 등록 완료)
        // 401 Unauthorized (인증한 사용자만 작성 가능)
        // 422 Unprocessable Entity (Form Validation Fail)
        try {
            $board = Board::where("name", "{$boardName}")->firstOrFail();
            $user = Auth::user();

            $v = $request->all();
            $tags = config("laraboard.allow_post_content_tags"); // Allowed tags
            $ua = Agent::parse(
                config("laraboard.collect_user_info") // 사용자 UA 수집여부
                    ? $request->server("HTTP_USER_AGENT")
                    : null
            );

            // 게시글 추가
            $post = Post::create([
                "ip_address" => encrypt($request->ip()),
                "user_agent" => encrypt($ua->agent),
                "device_type" => $ua->device_type,
                "os_name" => $ua->os_name,
                "os_ver" => $ua->os_version,
                "browser_name" => $ua->browser_name,
                "browser_ver" => $ua->browser_version,
                "notice" => $v["notice"],
                "subject" => strip_tags($v["subject"]),
                "content" => htmlspecialchars(strip_tags($v["content"], $tags)),
                "stripped_content" => strip_tags($v["content"]),
                "view_count" => 0,
                "points" => $board->post_points,
                "board_id" => $board->id,
                "wrote_user_id" => $user->id,
            ]);

            // 성공: 201 Created
            return response()->noContent(Response::HTTP_CREATED, [
                "Location" => action(
                    [PostController::class, "show"],
                    [
                        "boardName" => $board->name,
                        "postId" => $post->id,
                    ]
                ),
            ]);
        } catch (ValidationException $e) {
            $err = $e->errors();
            return $this->json($err, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $boardName   게시판 이름
     * @param  int  $postId         게시글 ID
     * @return \Illuminate\Http\Response
     */
    public function show(string $boardName, int $postId)
    {
        // 200 OK (게시판 조회 성공)
        // 404 Not Found (게시판 없음)
        try {
            // 게시글 조회
            $post = Post::withCount(["comments"])
                ->with([
                    "board" => fn($q) => $q->select("id", "name", "name_ko"),
                    "user" => fn($q) => $q->select("id", "name"),
                ])
                ->whereHas("board", fn($q) => $q->where("name", $boardName))
                ->findOrFail($postId);

            // view count 증가
            $post->view_count++;
            $post->timestamps = false; // 조회수 증가시 updated_at 갱신 안함
            $post->save();
            $post->timestamps = true; // update_at 사용 복구

            return $this->json($post);
        } catch (ModelNotFoundException $e) {
            return $this->json("Not Found", Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Laraboard\UpdatePostRequest  $request
     * @param  string  $boardName   게시판 이름
     * @param  int  $postId         게시글 ID
     * @return \Illuminate\Http\Response
     */
    public function update(
        UpdatePostRequest $request,
        string $boardName,
        int $postId
    ) {
        // 200 OK (갱신 성공)
        // 401 Unauthorized (권한 없음 - 작성자만 갱신 가능)
        // 404 Not found (갱신 대상 게시글 없음)
        try {
            $user = Auth::user();

            // 게시글 정보 조회
            $post = Post::with([
                "board" => fn($q) => $q->select("id", "name", "name_ko"),
                "user" => fn($q) => $q->select("id", "name"),
            ])
                ->whereHas("board", fn($q) => $q->where("name", $boardName))
                ->findOrFail($postId);

            // 게시글 작성자가 다른 경우 갱신 불가
            if ($post->user->id != $user->id) {
                throw new UnauthorizedException("Unauthorized");
            }

            $v = $request->all();
            $tags = config("laraboard.allow_post_content_tags"); // Allowed tags
            $ua = Agent::parse(
                config("laraboard.collect_user_info") // 사용자 UA 수집여부
                    ? $request->server("HTTP_USER_AGENT")
                    : null
            );

            // 게시글 갱신
            $affected = $post->update([
                "ip_address" => encrypt($request->ip()),
                "user_agent" => encrypt($ua->agent),
                "device_type" => $ua->device_type,
                "os_name" => $ua->os_name,
                "os_ver" => $ua->os_version,
                "browser_name" => $ua->browser_name,
                "browser_ver" => $ua->browser_version,
                "notice" => $v["notice"],
                "subject" => strip_tags($v["subject"]),
                "content" => htmlspecialchars(strip_tags($v["content"], $tags)),
                "stripped_content" => strip_tags($v["content"]),
            ]);

            // 업데이트 성공: 200 OK
            return $this->json(["updated" => $affected]);
        } catch (ModelNotFoundException $e) {
            return $this->json("Not Found", Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $boardName   게시판 이름
     * @param  int  $postId         게시글 ID
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $boardName, int $postId)
    {
        // 204 No Content (삭제 완료)
        // 401 Unauthorized (권한 없음 - 작성자만 삭제 가능)
        // 404 Not Found (게시글 없음)
        // 409 Conflict (댓글이 존재하여 삭제 불가)
        try {
            $user = Auth::user();

            // 게시글 정보 조회
            $post = Post::withCount(["comments"])
                ->with([
                    "board" => fn($q) => $q->select("id", "name", "name_ko"),
                    "user" => fn($q) => $q->select("id", "name"),
                ])
                ->whereHas("board", fn($q) => $q->where("name", $boardName))
                ->findOrFail($postId);

            // 게시글 작성자가 다른 경우 삭제 불가
            if ($post->user->id != $user->id) {
                throw new UnauthorizedException("Unauthorized");
            }

            // 댓글이 존재할 경우 게시글 삭제 불가
            if ($post->comments_count > 0) {
                $message = "Comments exist";
                throw new HttpException(Response::HTTP_CONFLICT, $message);
            }

            // 게시글 삭제
            $post->delete();

            // 삭제 성공: 204 No Content 반환
            return response()->noContent();
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
