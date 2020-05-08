<?php
/**
 * 게시판 게시글 댓글 Relation 정의 Trait
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\Traits;

trait CommentRelations
{
    /**
     * 댓글 소속 게시판 정보를 가져오기 위한 관계 정의
     */
    public function board()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Board');
    }

    /**
     * 댓글 작성 사용자 정보를 가져오기 위한 관계 정의
     */
    public function user()
    {
        return $this->belongsTo('Inium\Laraboard\Models\User');
    }

    /**
     * 댓글의 게시글 정보를 가져오기 위한 관계 정의
     */
    public function post()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Post');
    }

    /**
     * 부모 댓글 정보 가져오기 위한 관계 정의
     */
    public function parent()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Comment');
    }

    /**
     * 자식 댓글 정보 가져오기 위한 관계 정의
     */
    public function children()
    {
        return $this->hasMany('Inium\Laraboard\Models\Comment');
    }
}
