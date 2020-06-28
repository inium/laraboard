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

    /**
     * 게시글을 가져온다.
     *
     * @param string $boardName             게시판 이름
     * @param integer $id                   게시글 ID
     * @param boolean $incrementViewCount   조회수 증가여부.
     * @return Post|null
     */
    private function getPost(string $boardName,
                             int $id,
                             bool $incrementViewCount = true): ?Post
    {
        $board = Board::findByName($boardName);
        if (!$board) {
            return null;
        }

        $post = $board->getPost($id);
        if (!$post) {
            return null;
        }

        // 조회수 1 증가
        if ($incrementViewCount) {
            $post->incrementViewCount();
        }

        return $post;
    }
}
