<?php
/**
 * 게시판 게시글 Relation 정의 Trait
 * 
 * @author inlee <einable@gmail.com>
 */

namespace App\Laraboard\Model\Component;

trait PostRelations
{
    /**
     * 게시판 정보를 가져오기 위한 관계 정의
     * 
     * @return App\Laraboard\Board
     */
    public function board()
    {
        return $this->belongsTo('App\Laraboard\Board');
    }

    /**
     * 게시글 작성한 사용자 정보를 가져오기 위한 관계 정의
     * 
     * @return App\Laraboard\User
     */
    public function user()
    {
        return $this->belongsTo('App\Laraboard\User');
    }

    /**
     * 게시글의 댓글 정보를 가져오기 위한 관계 정의
     * 
     * @return App\Laraboard\Comment
     */
    public function comments()
    {
        return $this->hasMany('App\Laraboard\Comment');
    }
}
