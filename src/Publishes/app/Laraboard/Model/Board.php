<?php
/**
 * 게시판 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace App\Laraboard;

use Illuminate\Database\Eloquent\Model;
use App\Laraboard\Model\Component\BoardRelations;

class Board extends Model
{
    use BoardRelations;

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
}
