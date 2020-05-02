<?php

namespace App\Laraboard;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
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
     * 게시판 사용자의 Auth User 정보를 가져오기 위한 관계 정의
     */
    public function authUser()
    {
        $authUserClass = config('laraboard.auth.model_name');
        return $this->belongsTo($authUserClass);
    }

    /**
     * 게시판 사용자가 속한 권한 정보를 가져오기 위한 관계 정의
     */
    public function privilege()
    {
        return $this->belongsTo('App\Laraboard\Privilege',
                                'board_user_privilege_id');
    }

    /**
     * 게시판 사용자가 생성한 게시판들을 가져오기 위한 관계 정의
     */
    public function boards()
    {
        return $this->hasMany('App\Laraboard\Board');
    }

    /**
     * 게시판 사용자가 작성한 게시글을 가져오기 위한 관계 정의
     */
    public function posts()
    {
        return $this->hasMany('App\Laraboard\Post');
    }

    /**
     * 게시판 사용자가 작성한 댓글을 가져오기 위한 관계 정의
     */
    public function comments()
    {
        return $this->hasMany('App\Laraboard\Comments');
    }
}
