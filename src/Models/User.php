<?php
/**
 * 게시판 사용자 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Inium\Laraboard\Core\Relations\UserRelationsTrait;

class User extends Model
{
    use SoftDeletes, UserRelationsTrait;

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
        $this->table = config('laraboard.board.table_name.user');
        parent::__construct($attributes);
    }

    /**
     * 사용자 ID를 이용해 게시판 사용자 정보를 가져온다.
     *
     * @param int $userId  사용자 ID
     */
    public static function findByUserId(int $userId)
    {
        return (new static)::where('user_id', $userId)->first();
    }
}
