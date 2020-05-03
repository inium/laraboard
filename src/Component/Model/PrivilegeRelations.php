<?php
/**
 * 게시판 권한 정보 Relation 정의 Trait
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\Component\Model;

trait PrivilegeRelations
{
    /**
     * 사용자 권한에 해당하는 게시판 사용자들을 가져오기 위한 관계 정의
     *
     * @return App\Laraboard\User
     */
    public function users()
    {
        return $this->hasMany('App\Laraboard\User');
    }
}
