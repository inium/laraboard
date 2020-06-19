<?php
/**
 * 게시판 사용자 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\Post;

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
     * @param integer|null $userId 사용자 ID
     */
    public static function findByUserId(?int $userId)
    {
        if (!$userId) {
            return null;
        }
        return static::where('user_id', $userId)->first();
    }

    /**
     * 사용자가 게시글 읽기 권한이 있는지 여부를 체크한다.
     *
     * @param Board $board      게시판
     * @param User $user        사용자
     * @return boolean
     */
    public function canReadPost(Board $board): bool
    {
        return ($board->minPostReadRole->id >= $this->role->id);
    }

    /**
     * 사용자가 게시글 쓰기 권한이 있는지 여부를 체크한다.
     *
     * @param Board $board      게시판
     * @param User $user        사용자
     * @return boolean
     */
    public function canWritePost(Board $board): bool
    {
        return ($board->minPostWriteRole->id >= $this->role->id);
    }

    /**
     * 사용자가 댓글 읽기 권한이 있는지 여부를 체크한다.
     *
     * @param Board $board      게시판
     * @param User $user        사용자
     * @return boolean
     */
    public function canReadComment(Board $board): bool
    {
        return ($board->minCommentReadRole->id >= $this->role->id);
    }

    /**
     * 사용자가 댓글 쓰기 권한이 있는지 여부를 체크한다.
     *
     * @param Board $board      게시판
     * @param User $user        사용자
     * @return boolean
     */
    public function canWriteComment(Board $board): bool
    {
        return ($board->minCommentWriteRole->id >= $this->role->id);
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
        return $this->belongsTo('Inium\Laraboard\App\Role',
                                'board_user_role_id');
    }

    /**
     * 게시판 사용자가 생성한 게시판들을 가져오기 위한 관계 정의
     */
    public function boards()
    {
        return $this->hasMany('Inium\Laraboard\App\Board');
    }

    /**
     * 게시판 사용자가 작성한 게시글을 가져오기 위한 관계 정의
     */
    public function posts()
    {
        return $this->hasMany('Inium\Laraboard\App\Post');
    }

    /**
     * 게시판 사용자가 작성한 댓글을 가져오기 위한 관계 정의
     */
    public function comments()
    {
        return $this->hasMany('Inium\Laraboard\App\Comment');
    }
}
