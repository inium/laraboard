<?php

namespace Inium\Laraboard\App\Board;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inium\Laraboard\App\Board;

trait PostListTrait
{
    /**
     * 게시글 목록 페이지에 표시할 정보들을 반환온다.
     *
     * @param string $boardName     게시판 이름
     * @param integer $page         페이지 번호
     * @return array
     */
    protected function list(string $boardName, int $page = 1): array
    {
        $board = Board::findByName($boardName);
        $notices = $board->getNotices();
        $posts = $board->getPosts($page);

        // 게시글 표시 정보
        return [
            'board' => $this->fetchBoardInfo($board),
            'notices' => $this->fetchContentInfo($notices, $page),
            'posts' => $this->fetchContentInfo($posts->getCollection(), $page),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'base_path' => route('laraboard.postList.view', [
                    'boardName' => $boardName
                ]),
            ]
        ];
    }

    /**
     * 게시글을 검색한 후 검색 페이지에 표시할 정보들을 반환한다.
     *
     * @param string $boardName     게시판 이름
     * @param string $query         검색어
     * @param string $type          검색타입
     * @param integer $page         페이지 번호
     * @return array
     */
    protected function search(string $boardName,
                              string $query,
                              string $type,
                              int $page = 1
                              ): array
    {
        $board = Board::findByName($boardName);
        $search = $board->search($query, $type, $page);

        // 게시글 표시 정보
        return [
            'board' => $this->fetchBoardInfo($board),
            'search' => [
                'count' => $search->total(),
                'query' => $query,
                'type' => $type,
                'type_value' => Board::getSearchType($type)
            ],
            'posts' => $this->fetchContentInfo($search->getCollection(),
                                               $page,
                                               $query,
                                               $type
                                               ),
            'pagination' => [
                'current_page' => $search->currentPage(),
                'last_page' => $search->lastPage(),
                'base_path' => route('laraboard.postList.view', [
                    'boardName' => $boardName
                ]),
                'query_params' => [
                    'type' => $type,
                    'query' => $query
                ]
            ],
        ];
    }

    /**
     * 페이지에 표시할 게시글 목록 정보들을 반환한다.
     *
     * @param Collection $post      게시글 목록
     * @param integer $page         페이지 번호
     * @return Collection
     */
    private function fetchContentInfo(Collection $posts,
                                     int $page = 1,
                                     string $query = null,
                                     string $type = null): Collection
    {
        $pageNum = ($page == 1) ? null : $page;

        return $posts->map(function ($item) use ($pageNum, $query, $type) {
            return [
                'subject' => $item->subject,
                'created_at'=> $item->created_at,
                'view_count' => $item->view_count,
                'notice' => $item->notice,
                'comments_count' => $item->comments_count,
                'tag' => json_decode($item->tag_json),
                'user' => [
                    'nickname' => $item->user->nickname
                ],
                // 'board' => [
                //     'name' => $item->board->name,
                //     'name_ko' => $item->board->name_ko,
                // ],
                'post_url' => route('laraboard.post.view', [
                    'boardName' => $item->board->name,
                    'id' => $item->id,
                    'type' => $type,
                    'query' => $query,
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
