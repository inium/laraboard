<?php
/**
 * 게시판 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Inium\Laraboard\App\Database\PaginationPageResolverTrait;

class Board extends Model
{
    use PaginationPageResolverTrait, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = null;

    /**
     * Constructor
     */
    public function __construct(array $attributes = array())
    {
        $this->table = config('laraboard.board.table_name.board');
        parent::__construct($attributes);
    }

    /**
     * 게시판 영문 이름으로 게시판 정보를 가져온다.
     *
     * @param string|null $boardName     게시판 영문이름
     * @return Inium\Laraboard\Models\Board
     */
    public static function findByName(?string $boardName)
    {
        if (!$boardName) {
            return null;
        }

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
                    //   ->latest()
                      ->orderBy('id', 'DESC')
                      ->paginate($this->post_rows_per_page);

        return $posts;
    }

    /**
     * 게시글을 가져온다.
     *
     * @param integer $postId               게시글 ID
     * @return Inium\Laraboard\Models\Post
     */
    public function getPost(int $postId)
    {
        $post = $this->posts()
                     ->with('user')
                     ->withCount('comments')
                     ->find($postId);

        return $post;
    }

    /**
     * 게시판 게시글 제목, 게시글 내용, 댓글 내용 검색결과를 반환한다.
     *
     * @param string $query     검색어.
     * @param string $type      검색 타입.
     * @param integer $page     페이지 번호.
     * @return Illuminate\Support\Collection
     */
    public function search(string $query, int $page = 1)
    {
        $this->setPageNum($page);

        $ret = $this->posts()
                ->with('user')
                ->withCount('comments')
                ->where('subject', 'LIKE', "%{$query}%")
                ->orWhere('content_pure', 'LIKE', "%{$query}%")
                ->orWhereHas('comments', function (Builder $q) use ($query) {
                    $q->where('content_pure', 'LIKE', "%{$query}%");
                        // // 댓글 작성자
                        // ->orWhereHas('user',
                        //     function (Builder $qq) use ($query) {
                        //         $qq->where('nickname', 'LIKE', "%{$query}%");
                        //     }
                        // );
                })
                // ->orWhereHas('user', function (Builder $q) use ($query) {
                //     // 작성자
                //     $q->where('nickname', 'LIKE', "%{$query}%");
                // })
                // ->latest()
                ->orderBy('id', 'DESC')
                ->paginate($this->post_rows_per_page);

        return $ret;
    }

    /**
     * 게시판 생성 사용자 정보를 가져오기 위한 관계 정의
     */
    public function user()
    {
        return $this->belongsTo('Inium\Laraboard\App\User',
                                'create_user_id');
    }

    /**
     * 게시판 게시글 정보를 가져오기 위한 관계 정의
     */
    public function posts()
    {
        return $this->hasMany('Inium\Laraboard\App\Post');
    }

    /**
     * 게시판 게시글의 댓글 정보를 가져오기 위한 관계 정의
     */
    public function comments()
    {
        return $this->hasMany('Inium\Laraboard\App\Comment');
    }

    /**
     * 최소 게시글 읽기 권한 정보를 가져오기 위한 관계 정의
     */
    public function minPostReadRole()
    {
        return $this->belongsTo('Inium\Laraboard\App\Role',
                                'min_post_read_role_id');
    }

    /**
     * 최소 게시글 쓰기, 수정, 삭제 권한 정보를 가져오기 위한 관계 정의
     */
    public function minPostWriteRole()
    {
        return $this->belongsTo('Inium\Laraboard\App\Role',
                                'min_post_write_role_id');
    }

    /**
     * 최소 댓글 읽기 권한 정보를 가져오기 위한 관계 정의
     */
    public function minCommentReadRole()
    {
        return $this->belongsTo('Inium\Laraboard\App\Role',
                                'min_comment_read_role_id');
    }

    /**
     * 최소 댓글 쓰기, 수정, 삭제 권한 정보를 가져오기 위한 관계 정의
     */
    public function minCommentWriteRole()
    {
        return $this->belongsTo('Inium\Laraboard\App\Role',
                                'min_comment_write_role_id');
    }
}
