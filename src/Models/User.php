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
use Inium\Laraboard\Models\Board;

class User extends Model
{
    use SoftDeletes;

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
        return static::where('user_id', $userId)->first();
    }

    /**
     * 게시판 사용자의 Auth User 정보를 가져오기 위한 관계 정의
     * 
     * @return  php artisan make:auth로 생성된 Auth User 모델
     */
    public function user()
    {
        $authUserClass = config('auth.providers.users.model');
        return $this->belongsTo($authUserClass);
    }

    /**
     * 게시판 사용자가 속한 권한 정보를 가져오기 위한 관계 정의
     */
    public function role()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Role',
                                'board_user_role_id');
    }

    /**
     * 게시판 사용자가 생성한 게시판들을 가져오기 위한 관계 정의
     */
    public function boards()
    {
        return $this->hasMany('Inium\Laraboard\Models\Board');
    }

    /**
     * 게시판 사용자가 작성한 게시글을 가져오기 위한 관계 정의
     */
    public function posts()
    {
        return $this->hasMany('Inium\Laraboard\Models\Post');
    }

    /**
     * 게시판 사용자가 작성한 댓글을 가져오기 위한 관계 정의
     */
    public function comments()
    {
        return $this->hasMany('Inium\Laraboard\Models\Comment');
    }
}
