<?php

namespace Inium\Laraboard\App\Board;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inium\Laraboard\App\Board;

trait PostsTrait
{
    /**
     * 게시글 목록 페이지에 표시할 정보들을 반환온다.
     *
     * @param string $boardName     게시판 이름
     * @param integer $page         페이지 번호
     * @return array
     */
    protected function posts(string $boardName, int $page = 1): array
    {
        $board = Board::findByName($boardName);
        $notices = $board->getNotices();
        $posts = $board->getPosts($page);

        // 게시글 표시 정보
        return [
            'board' => $this->fetchBoardInfo($board),
            'notices' => $this->fetchPostInfo($notices, $page),
            'posts' => $this->fetchPostInfo($posts->getCollection(), $page),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'base_path' => $posts->path()
            ]
        ];
    }

    /**
     * 페이지에 표시할 게시글 목록 정보들을 반환한다.
     *
     * @param Collection $post      게시글 목록
     * @param integer $page         페이지 번호
     * @return Collection
     */
    private function fetchPostInfo(Collection $posts, int $page = 1): Collection
    {
        $pageNum = ($page == 1) ? null : $page;

        return $posts->map(function ($item) use ($pageNum) {
            return [
                'subject' => $item->subject,
                'created_at'=> $item->created_at,
                'view_count' => $item->view_count,
                'notice' => $item->notice,
                'comments_count' => $item->comments_count,
                'user' => [
                    'nickname' => $item->user->nickname
                ],
                'post_url' => route('laraboard.post.view', [
                    'boardName' => $item->board->name,
                    'id' => $item->id,
                    'page' => $pageNum
                ])
            ];
        });
    }

    /**
     * 출력할 게시판 정보를 반환한다.
     *
     * @param Board $board  게시판 정보
     * @return array
     */
    private function fetchBoardInfo(Board $board): array
    {
        return $board->only(['name', 'name_ko']);
    }
}
