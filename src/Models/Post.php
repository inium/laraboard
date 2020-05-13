<?php
/**
 * 게시판 게시글 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Inium\Laraboard\Core\Relations\PostRelationsTrait;

class Post extends Model
{
    use SoftDeletes, PostRelationsTrait;

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
}
