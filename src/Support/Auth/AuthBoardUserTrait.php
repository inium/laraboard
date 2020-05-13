<?php

namespace Inium\Laraboard\Support\Auth;

trait AuthBoardUserTrait
{
    /**
     * 게시판 사용자 정보를 가져오기 위한 관계 정의
     */
    public function boardUser()
    {
        return $this->hasOne('Inium\Laraboard\Models\User', 'user_id');
    }
}
