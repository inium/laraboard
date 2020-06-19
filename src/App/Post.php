<?php
/**
 * 게시판 게시글 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;
use Inium\Laraboard\App\Database\PaginationPageResolverTrait;

class Post extends Model
{
    use SoftDeletes, PaginationPageResolverTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = null;

    // protected $commentsTable = null;

    /**
     * Constructor
     */
    public function __construct(array $attributes = array())
    {
        $this->table = config('laraboard.board.table_name.post');
        // $this->commentsTable = config('laraboard.board.table_name.comment');
        parent::__construct($attributes);
    }

    /**
     * 댓글을 2 depth 계층형으로 정렬하여 반환한다.
     * 본 구현의 댓글은 2 depth로 제한.
     *
     * @param integer $page     댓글 페이지 번호
     * @return LengthAwarePaginator
     */
    public function getHierarchicalComments(int $page = 1): LengthAwarePaginator
    {
        /**
         * 2 depth 제한을 해제하기 위해서는 WITH RECURSIVE 사용 혹은
         * group_id, depth_no, order_no, parent_comment_id 별도 정의
         *  - group_id: 댓글 그룹 ID
         *  - depth_no: 댓글 깊이(대댓글)
         *  - order_no: 댓글 그룹 내 순서(대댓글, 대대댓글 정렬을 위해 사용)
         *  - parent_comment_id: 부모 댓글의 ID
         * 
         * WITH RECURSIVE 예
         * ---------------------------------------------------------------------
         * WITH RECURSIVE cte AS (
         *  SELECT 1 AS lv, c1.*, CAST(c1.id AS varchar(255)) path
         *  FROM lb_board_post_comments as c1
         *  WHERE c1.parent_comment_id IS NULL
         *  UNION ALL
         *  SELECT c.lv + 1, c2.*, CONCAT(c.path, '.', c2.id)
         *  FROM lb_board_post_comments AS c2, cte AS c
         *  WHERE c.id = c2.parent_comment_id
         * )
         * SELECT *
         * FROM cte
         * WHERE post_id = 14
         * ORDER BY path
         * ---------------------------------------------------------------------
         */

        /**
         * SELECT IF(c.parent_comment_id IS NULL,
         *           c.id,
         *           c.parent_comment_id) AS group_id,
         *        c.* 
         * FROM `lb_board_post_comments` AS c 
         * WHERE post_id = 14 
         * ORDER BY group_id, id
         */

        $this->setPageNum($page);

        $comments = $this->comments()
                         ->with('user')
                         ->select('*')
                         ->selectSub(function ($query) {
                             $query->selectRaw(
                                'IF(parent_comment_id IS NULL,
                                    id,
                                    parent_comment_id)'
                                );
                         }, 'group_id')
                         ->where('post_id', $this->id)
                         ->orderBy('group_id')
                         ->orderBy('id')
                         ->paginate($this->board->comment_rows_per_page);

        return $comments;
    }

    /**
     * 게시글의 부모 댓글이 없는 댓글 목록을 가져온다.
     *
     * @param integer $page     페이지 번호
     * @return LengthAwarePaginator
     */
    public function getComments(int $page = 1): LengthAwarePaginator
    {
        $this->setPageNum($page);

        $comments = $this->comments()
                         ->with('user')
                         ->withCount('children')
                         ->doesntHave('parent')
                         ->orderBy('id')
                         ->paginate($this->board->comment_rows_per_page);

        return $comments;
    }

    /**
     * 게시판 정보를 가져오기 위한 관계 정의
     */
    public function board()
    {
        return $this->belongsTo('Inium\Laraboard\App\Board', 'board_id');
    }

    /**
     * 게시글 작성한 사용자 정보를 가져오기 위한 관계 정의
     */
    public function user()
    {
        return $this->belongsTo('Inium\Laraboard\App\User', 'wrote_user_id');
    }

    /**
     * 게시글의 댓글 정보를 가져오기 위한 관계 정의
     */
    public function comments()
    {
        return $this->hasMany('Inium\Laraboard\App\Comment');
    }
}
