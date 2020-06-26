<?php

namespace Inium\Laraboard\App\Board;

use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\Comment;
use Inium\Laraboard\App\Post;
use Inium\Laraboard\App\Role;
use Inium\Laraboard\App\User;

class BoardUserRoles
{
    /**
     * 게시판의 로그인한 사용자의 역할을 반환한다.
     *
     * @param string $boardName     게시판 이름
     * @return object
     */
    public static function roles(string $boardName): object
    {
        try {
            $board = Board::findByName($boardName);
            if (!$board) {
                throw new \Exception('Board not found', 404);
            }

            $user = User::findByUserId(Auth::id());

            $roles = [
                'admin' => $user->is_admin ? true : false,      // 관리자 여부
                'post' => self::postUserRoles($board, $user),   // 게시글 권한
                'comment' => self::commentUserRoles($board, $user)  // 댓글 권한
            ];

            return (object)$roles;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 게시글 사용자 읽기, 쓰기 역할(Role) 정보를 반환한다.
     *
     * @param Board $board      게시판
     * @param User|null $user   게시판 사용자
     * @return Object
     */
    public static function postUserRoles(Board $board, User $user = null): Object
    {
        $canRead = false;
        $canWrite = false;

        // 게시판 게시글 읽기 사용자 권한이 null일 경우 누구나 접근 가능.
        if (!is_null($board->minPostReadRole)) {
            if (!is_null($user)) {
                $canRead = self::greaterOrEqual($board->minPostReadRole,
                                                $user->role);
            }
            else {
                $canRead = false;
            }
        }
        else {
            $canRead = true;
        }

        // 게시판 글쓰기 권한 체크에 사용자 정보 참조
        // 사용자 정보가 없을 경우 쓰기 불가
        if (!is_null($user)) {
            $canWrite = self::greaterOrEqual($board->minPostWriteRole,
                                             $user->role);
        }
        else {
            $canWrite = false;
        }

        return (object)[
            'canRead' => $canRead,
            'canWrite' => $canWrite
        ];
    }

    /**
     * 댓글 사용자 읽기, 쓰기 역할(Role) 정보를 반환한다.
     *
     * @param Board $board      게시판
     * @param User|null $user   게시판 사용자
     * @return Object
     */
    public static function commentUserRoles(Board $board, User $user = null): Object
    {
        $canRead = false;
        $canWrite = false;

        // 게시판 게시글 읽기 사용자 권한이 null일 경우 누구나 접근 가능.
        if (!is_null($board->minCommentReadRole)) {
            if (!is_null($user)) {
                $canRead = self::greaterOrEqual($board->minCommentReadRole,
                                                $user->role);
            }
            else {
                $canRead = false;
            }
        }
        else {
            $canRead = true;
        }

        // 게시판 글쓰기 권한 체크에 사용자 정보 참조
        // 사용자 정보가 없을 경우 쓰기 불가
        if (!is_null($user)) {
            $canWrite = self::greaterOrEqual($board->minCommentWriteRole,
                                             $user->role);
        }
        else {
            $canWrite = false;
        }

        return (object)[
            'canRead' => $canRead,
            'canWrite' => $canWrite
        ];
    }

    /**
     * A greater than or equal to B
     * 
     * 게시판 - 사용자 역할을 비교한다. 게시판 사용자 역할 숫자가 크거나 같으면
     * (사용자 역할 숫자가 작거나 같을 경우) true, 그 외 false 반환.
     *
     * @param Role|null $boardRole  게시판 대상 Role. A.
     * @param Role|null $userRole   사용자 Role. B.
     * @return boolean
     */
    private static function greaterOrEqual(Role $boardRole = null,
                                           Role $userRole = null): bool
    {
        if (is_null($boardRole) || is_null($userRole)) {
            return false;
        }
        else {
            return $boardRole->id >= $userRole->id;
        }
    }
}
