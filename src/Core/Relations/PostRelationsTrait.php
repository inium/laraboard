<?php
/**
 * 게시판 게시글 Relation 정의 Trait
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\Core\Relations;

trait PostRelationsTrait
{
    /**
     * 게시판 정보를 가져오기 위한 관계 정의
     */
    public function board()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Board', 'board_id');
    }

    /**
     * 게시글 작성한 사용자 정보를 가져오기 위한 관계 정의
     */
    public function user()
    {
        return $this->belongsTo('Inium\Laraboard\Models\User', 'wrote_user_id');
    }

    /**
     * 게시글의 댓글 정보를 가져오기 위한 관계 정의
     */
    public function comments()
    {
        return $this->hasMany('Inium\Laraboard\Models\Comment');
    }
}
