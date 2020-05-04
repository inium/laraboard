<?php
/**
 * 회원가입 완료 후 게시판 사용을 위한 설정 진행하는 Mixin
 * 
 * @author inlee <einable@gmail.com>
 */

namespace App\Laraboard\Component;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Laraboard\Model\User as LaraboardUser;

trait AuthRegistered
{
    /**
     * The user has been registered.
     *
     * @param Request \Illuminate\Http\Request  $request
     * @param mixed $user
     * @return mixed
     */
    public function registered(Request $request, $user)
    {
        // 닉네임 생성
        $nickname = Str::slug($user->name, '_');
        if (config('larabord.board.nickname_unique')) {
            $hash = Str::random(5);
            $nickname = "{$nickname}_{$hash}";
        }


        // $user->id;

        return redirect($this->redirectPath());
    }
}
