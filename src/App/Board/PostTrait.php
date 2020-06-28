<?php

namespace Inium\Laraboard\App\Board;

use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\Post;
use Inium\Laraboard\App\User;
use Inium\Laraboard\Support\Facades\Agent;

trait PostTrait
{
    /**
     * 게시글 목록을 반환한다.
     *
     * @param string $boardName     게시판 이름
     * @param integer $page         페이지 번호
     * @return array
     */
    private function getPostList(string $boardName, int $page = 1): array
    {
        $board = Board::findByName($boardName);
        $notices = $board->getNotices();
        $posts = $board->getPosts($page);

        return [
            'board' => $board,
            'notices' => $notices,
            'posts' => $posts
        ];
    }
}
