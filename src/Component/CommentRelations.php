<?php
/**
 * 게시판 게시글 댓글 Relation 정의 Trait
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\Component;

trait CommentRelations
{
    /**
     * 댓글 소속 게시판 정보를 가져오기 위한 관계 정의
     * 
     * @return App\Laraboard\Board
     */
    public function board()
    {
        return $this->belongsTo('App\Laraboard\Board');
    }

    /**
     * 댓글 작성 사용자 정보를 가져오기 위한 관계 정의
     * 
     * @return App\Laraboard\User
     */
    public function user()
    {
        return $this->belongsTo('App\Laraboard\User');
    }

    /**
     * 댓글의 게시글 정보를 가져오기 위한 관계 정의
     * 
     * @return App\Laraboard\Post
     */
    public function post()
    {
        return $this->belongsTo('App\Laraboard\Post');
    }

    /**
     * 부모 댓글 정보 가져오기 위한 관계 정의
     * 
     * @return App\Laraboard\Comment
     */
    public function parent()
    {
        return $this->belongsTo('App\Laraboard\Comment');
    }

    /**
     * 자식 댓글 정보 가져오기 위한 관계 정의
     * 
     * @return App\Laraboard\Comment
     */
    public function children()
    {
        return $this->hasMany('App\Laraboard\Comment');
    }
}
