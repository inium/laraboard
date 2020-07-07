<?php
/**
 * 회원가입 완료 후 게시판 사용을 위한 설정 진행하는 Mixin
 *
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\Support\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inium\Laraboard\App\User;
use Inium\Laraboard\App\Role;

trait AuthRegisteredTrait
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
        $hash = Str::random(5);
        $nickname = Str::slug($user->name, '_');
        $nickname = "{$nickname}_{$hash}";

        // 게시판 사용자 추가
        $ret = $this->addBoardUser($nickname, $user);

        // 게시판 사용자 추가 실패를 할 경우, 500 Internal Server Error 반환
        if (!$ret) {
            abort(500, 'Fail to laraboard user add.');
        }
        // 게시판 사용자 추가에 성공한 경우
        // RegisterController에서 설정한 페이지(Dashboard page)로 이동
        else {
            return redirect($this->redirectPath());
        }
    }

    /**
     * 게시판 사용자를 추가한다.
     *
     * @param string $nickname  사용할 닉네임
     * @param mixed $user
     * @return mixed
     */
    private function addBoardUser(string $nickname, $user)
    {
        // 사용자 권한 중 가장 마지막 권한 검색
        // 해당 권한을 게시판 사용자에게 부여
        $privilege = Role::all()->last();

        // 게시판 사용자 정보 설정
        $boardUser = new User();

        $boardUser->nickname = $nickname;
        $boardUser->thumbnail_path = null;
        $boardUser->user()->associate($user);
        $boardUser->role()->associate($privilege);

        //  게시판 사용자 정보 저장
        $ret = $boardUser->save();

        return $ret;
    }
}
