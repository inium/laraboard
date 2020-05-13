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
use Inium\Laraboard\Core\Relations\BoardRelationsTrait;

class Board extends Model
{
    use SoftDeletes, BoardRelationsTrait;

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
     * @param string $name  게시판 영문이름
     * @return mixed
     */
    public static function findByName(string $name)
    {
        return (new static)::where('name', $name)->first();
    }
}
