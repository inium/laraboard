<?php
/**
 * 게시판 권한 정보 Relation 정의 Trait
 * 
 * @author inlee <einable@gmail.com>
 */

namespace App\Laraboard\Model\Relation;

trait PrivilegeRelations
{
    /**
     * 사용자 권한에 해당하는 게시판 사용자들을 가져오기 위한 관계 정의
     */
    public function users()
    {
        return $this->hasMany('App\Laraboard\Model\User');
    }
}
