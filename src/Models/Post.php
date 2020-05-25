<?php
/**
 * 게시판 게시글 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Inium\Laraboard\Core\Pagination\PaginationPageResolverTrait;
use Inium\Laraboard\Core\Relations\PostRelationsTrait;

class Post extends Model
{
    use SoftDeletes, PostRelationsTrait, PaginationPageResolverTrait;

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
        $this->table = config('laraboard.board.table_name.post');
        parent::__construct($attributes);
    }

    /**
     * 댓글을 계층형으로 정렬하여 반환한다.
     * 본 구현의 댓글은 2 depth로 제한.
     *
     * @param integer $page     페이지 번호
     */
    public function getHierarchicalComments(int $page = 1)
    {
        $this->setPageNum($page);

        $commentRowsPerPage = $this->board->comment_rows_per_page;

        $comments = $this->comments()
                         ->with('user')
                         ->with('parent')
                        //  ->withCount('children')
                         ->orderByRaw(
                            'CASE WHEN ISNULL(parent_comment_id) THEN id
                                  ELSE parent_comment_id
                             END'
                         )
                         ->orderBy('id')
                         ->paginate($commentRowsPerPage);
        return $comments;
    }
}
