<?php
/**
 * 게시판 사용자 정보 Relation 정의 Trait
 * 
 * @author inlee <einable@gmail.com>
 */

namespace App\Laraboard\Model\Component;

trait UserRelations
{
    /**
     * 게시판 사용자의 Auth User 정보를 가져오기 위한 관계 정의
     * 
     * @return App\User     php artisan make:auth로 생성된 Auth User 모델
     */
    public function user()
    {
        $authUserClass = config('laraboard.auth.model_name');
        return $this->belongsTo($authUserClass);
    }

    /**
     * 게시판 사용자가 속한 권한 정보를 가져오기 위한 관계 정의
     * 
     * @return App\Laraboard\Privilege
     */
    public function privilege()
    {
        return $this->belongsTo('App\Laraboard\Privilege',
                                'board_user_privilege_id');
    }

    /**
     * 게시판 사용자가 생성한 게시판들을 가져오기 위한 관계 정의
     * 
     * @return App\Laraboard\Board
     */
    public function boards()
    {
        return $this->hasMany('App\Laraboard\Board');
    }

    /**
     * 게시판 사용자가 작성한 게시글을 가져오기 위한 관계 정의
     * 
     * @return App\Laraboard\Post
     */
    public function posts()
    {
        return $this->hasMany('App\Laraboard\Post');
    }

    /**
     * 게시판 사용자가 작성한 댓글을 가져오기 위한 관계 정의
     * 
     * @return App\Laraboard\Comment
     */
    public function comments()
    {
        return $this->hasMany('App\Laraboard\Comment');
    }
}
