<?php
/**
 * 게시판 댓글 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace App\Laraboard\Model;

use Illuminate\Database\Eloquent\Model;
use Inium\Laraboard\Component\CommentRelations;

class Comment extends Model
{
    use CommentRelations;

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
