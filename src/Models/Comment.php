<?php
/**
 * 게시판 댓글 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Inium\Laraboard\Core\Relations\CommentRelationsTrait;

class Comment extends Model
{
    use SoftDeletes, CommentRelationsTrait;

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
        $this->table = config('laraboard.board.table_name.comment');
        parent::__construct($attributes);
    }
}
