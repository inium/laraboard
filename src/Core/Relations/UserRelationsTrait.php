<?php
/**
 * 게시판 사용자 정보 Relation 정의 Trait
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\Core\Relations;

trait UserRelationsTrait
{
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
    public function privilege()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Privilege',
                                'board_user_privilege_id');
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
