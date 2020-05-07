<?php
/**
 * 게시판 게시글 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace App\Laraboard\Model;

use Illuminate\Database\Eloquent\Model;
use App\Laraboard\Model\Relation\PostRelations;

class Post extends Model
{
    use PostRelations;

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
