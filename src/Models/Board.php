<?php
/**
 * 게시판 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Inium\Laraboard\Traits\PaginationPageResolverTrait;

class Board extends Model
{
    use SoftDeletes, PaginationPageResolverTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = null;

    /**
     * 검색 유형.
     * - {type => 설명} 으로 구성.
     *
     * @var array
     */
    private static $searchTypes = [
        'subcon'   => '제목 + 본문',
        'subject'  => '제목',
        'content'  => '본문',
        'tag'      => '태그',
        'comment'  => '댓글',
        'nickname' => '닉네임'
    ];

    /**
     * 기본 검색 유형
     *
     * @var string
     */
    private static $defaultSearchType = 'subcon';


    /**
     * Constructor
     */
    public function __construct(array $attributes = array())
    {
        $this->table = config('laraboard.board.table_name.board');
        parent::__construct($attributes);
    }

    /**
     * 검색 유형을 반환한다.
     *
     * @return array
     */
    public static function searchTypes(): array
    {
        return static::$searchTypes;
    }

    /**
     * 기본 검색 유형을 반환한다.
     *
     * @return string
     */
    public static function defaultSearchType(): string
    {
        return static::$defaultSearchType;
    }

    /**
     * 검색 type이 검색 유형에 존재하는지 검사한 결과를 반환한다.
     *
     * @param string $type  검사 대상 검색 유형 type.
     * @return boolean
     */
    public static function validSearchType(string $type): bool
    {
        return array_key_exists($type, static::searchTypes());
    }

    /**
     * 검색 type에 따른 {type => 설명} 정보를 반환한다.
     *
     * @param string $type  검사 대상 검색 유형 type.
     * @return array|null
     */
    public static function getSearchType(string $type): ?array
    {
        if (static::validSearchType($type)) {
            return $this->searchTypes[$type];
        }
        else {
            return null;
        }
    }

    /**
     * 게시판 영문 이름으로 게시판 정보를 가져온다.
     *
     * @param string $boardName     게시판 영문이름
     * @param array $columns    검색대상 필드
     * @return Inium\Laraboard\Models\Board
     */
    public static function findByName(string $boardName)
    {
        // return static::with('posts')->where('name', $boardName)->first();
        return static::where('name', $boardName)->first();
    }

    /**
     * 게시판의 공지글들을 가져온다.
     * - 공지글은 최신순으로 정렬
     *
     * @return Illuminate\Support\Collection
     */
    public function getNotices()
    {
        $notices = $this->posts()
                        ->with('user')
                        ->withCount('comments')
                        ->where('notice', 1)
                        ->latest()
                        ->get();

        return $notices;
    }

    /**
     * 페이지 단위 게시글 목록을 가져온다.
     *
     * @param integer $page     페이지 번호
     * @return Illuminate\Support\Collection
     */
    public function getPosts(int $page = 1)
    {
        $this->setPageNum($page);

        $posts = $this->posts()
                      ->with('user')
                      ->withCount('comments')
                      ->where('notice', 0)
                      ->latest()
                      ->paginate($this->post_rows_per_page);

        return $posts;
    }

    // /**
    //  * 게시글을 가져온다.
    //  *
    //  * @param integer $postId               게시글 ID
    //  * @param boolean $incrementViewCount   조회수 1 증가 여부.
    //  * @return Inium\Laraboard\Models\Post
    //  */
    // public function getPost(int $postId, bool $incrementViewCount = true)
    // {
    //     $post = $this->posts()
    //                  ->with('user')
    //                  ->withCount('comments')
    //                  ->find($postId);

    //     // 조회수 1 증가
    //     if ($incrementViewCount) {
    //         $post->view_count++;
    //         $post->timestamps = false;  // 조회수 증가시 updated_at 추가 안함
    //         $post->save();

    //         $post->timestamps = true;   // update_at 사용 복구
    //     }

    //     return $post;
    // }

    /**
     * 게시판 검색한 결과를 반환한다.
     *
     * @param string $query     검색어.
     * @param string $type      검색 타입.
     * @param integer $page     페이지 번호.
     * @return Illuminate\Support\Collection
     */
    public function search(string $query, string $type, int $page = 1)
    {
        $searchResult = null;

        if (static::validSearchType($type)) {
            $type = Str::title($type);
            $searchResult = $this->{"searchBy{$type}"}($query, $page);
        }
        else {
            $ret = $this->searchBySubcon($query, $page);
        }

        return $searchResult;
    }

    /**
     * 제목 + 본문 검색한 결과를 반환한다.
     *
     * @param string $query     검색어.
     * @param integer $page     페이지 번호.
     * @return Illuminate\Support\Collection
     */
    public function searchBySubcon(string $query, int $page = 1)
    {
        $this->setPageNum($page);

        $search = $this->posts()
                       ->with('user')
                       ->withCount('comments')
                       ->where('subject', 'LIKE', "%{$query}%")
                       ->orWhere('content_pure', 'LIKE', "%{$query}%")
                       ->latest()
                       ->paginate($this->post_rows_per_page);

        return $search;
    }

    /**
     * 제목 검색한 결과를 반환한다.
     *
     * @param string $query     검색어.
     * @param integer $page     페이지 번호.
     * @return Illuminate\Support\Collection
     */
    public function searchBySubject(string $query, int $page = 1)
    {
        $this->setPageNum($page);

        $search = $this->posts()
                       ->with('user')
                       ->withCount('comments')
                       ->where('subject', 'LIKE', "%{$query}%")
                       ->latest()
                       ->paginate($this->post_rows_per_page);

        return $search;
    }

    /**
     * 본문 검색한 결과를 반환한다.
     *
     * @param string $query     검색어.
     * @param integer $page     페이지 번호.
     * @return Illuminate\Support\Collection
     */
    public function searchByContent(string $query, int $page = 1)
    {
        $this->setPageNum($page);

        $search = $this->posts()
                       ->with('user')
                       ->withCount('comments')
                       ->where('content_pure', 'LIKE', "%{$query}%")
                       ->latest()
                       ->paginate($this->post_rows_per_page);

        return $search;
    }

    /**
     * 본문 검색한 결과를 반환한다.
     *
     * @param string $query     검색어.
     * @param integer $page     페이지 번호.
     * @return Illuminate\Support\Collection
     */
    public function searchByTag(string $query, int $page = 1)
    {
        $this->setPageNum($page);

        $search = $this->posts()
                       ->with('user')
                       ->withCount('comments')
                       ->where('tag', 'LIKE', "%{$query}%")
                       ->latest()
                       ->paginate($this->post_rows_per_page);

        return $search;
    }

    /**
     * 댓글 검색한 결과를 반환한다.
     *
     * @param string $query     검색어.
     * @param integer $page     페이지 번호.
     * @return Illuminate\Support\Collection
     */
    public function searchByComment(string $query, int $page = 1)
    {
        $this->setPageNum($page);

        $search = $this->posts()
                       ->with('user')
                       ->withCount('comments')
                       ->where('tag', 'LIKE', "%{$query}%")
                       ->whereHas('comments',
                            function (Builder $q) use ($query) {
                                $q->where('content_pure', 'LIKE', "%{$query}%");
                            }
                       )
                       ->latest()
                       ->paginate($this->post_rows_per_page);

        return $search;
    }

    /**
     * 작성자 닉네임을 검색한 결과를 반환한다.
     *
     * @param string $query     검색어.
     * @param integer $page     페이지 번호.
     * @return Illuminate\Support\Collection
     */
    public function searchByNickname(string $query, int $page = 1)
    {
        $this->setPageNum($page);

        $search = $this->posts()
                       ->with('user')
                       ->withCount('comments')
                       ->where('tag', 'LIKE', "%{$query}%")
                       ->whereHas('user', function (Builder $q) use ($query) {
                            $q->where('nickname', 'LIKE', "%{$query}%");
                       })
                       ->latest()
                       ->paginate($this->post_rows_per_page);

        return $search;
    }

    /**
     * 게시판 생성 사용자 정보를 가져오기 위한 관계 정의
     */
    public function user()
    {
        return $this->belongsTo('Inium\Laraboard\Models\User',
                                'create_user_id');
    }

    /**
     * 게시판 게시글 정보를 가져오기 위한 관계 정의
     */
    public function posts()
    {
        return $this->hasMany('Inium\Laraboard\Models\Post');
    }

    /**
     * 게시판 게시글의 댓글 정보를 가져오기 위한 관계 정의
     */
    public function comments()
    {
        return $this->hasMany('Inium\Laraboard\Models\Comment');
    }

    /**
     * 최소 게시글 목록 읽기 권한 정보를 가져오기 위한 관계 정의
     */
    public function minPostListReadRole()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Role',
                                'min_post_list_read_role_id');
    }

    /**
     * 최소 게시글 읽기 권한 정보를 가져오기 위한 관계 정의
     */
    public function minPostReadRole()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Role',
                                'min_post_read_Role_id');
    }

    /**
     * 최소 게시글 쓰기, 수정, 삭제 권한 정보를 가져오기 위한 관계 정의
     */
    public function minPostWriteRole()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Role',
                                'min_post_write_role_id');
    }

    /**
     * 최소 댓글 읽기 권한 정보를 가져오기 위한 관계 정의
     */
    public function minCommentReadRole()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Role',
                                'min_comment_read_role_id');
    }

    /**
     * 최소 댓글 쓰기, 수정, 삭제 권한 정보를 가져오기 위한 관계 정의
     */
    public function minCommentWriteRole()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Role',
                                'min_comment_write_role_id');
    }
}
