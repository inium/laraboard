<?php

namespace Inium\Laraboard\App\Board;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board;

trait PostSearchTrait
{
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
                              int $page = 1): array
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
            'posts' => $this->fetchSearchInfo($search->getCollection(),
                                              $query,
                                              $type,
                                              $page),
            'pagination' => [
                'current_page' => $search->currentPage(),
                'last_page' => $search->lastPage(),
                'base_path' => $search->path(),
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
     * @param string $query         검색어
     * @param string $type          검색타입
     * @param integer $page         페이지 번호
     * @return Collection
     */
    private function fetchSearchInfo(Collection $posts,
                                     string $query,
                                     string $type,
                                     int $page = 1): Collection
    {
        $pageNum = ($page == 1) ? null : $page;

        return $posts->map(function ($item) use ($pageNum, $query, $type) {
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

    // /**
    //  * 페이지에 표시할 검색 결과 목록 정보들을 반환한다.
    //  *
    //  * @param Collection $post      검색 결과 목록
    //  * @param integer $page         페이지 번호
    //  * @return Collection
    //  */
    // private function searchForList(Collection $posts, int $page = 1): Collection
    // {
    //     $pageNum = ($page == 1) ? null : $page;

    //     return $posts->transform(function ($item) use ($pageNum) {
    //         return [
    //             'subject' => $item->subject,
    //             'created_at'=> $item->created_at,
    //             'view_count' => $item->view_count,
    //             'comments_count' => $item->comments_count,
    //             'user' => [
    //                 'nickname' => $item->user->nickname
    //             ],
    //             'post_url' => route('laraboard.post.view', [
    //                 'boardName' => $item->board->name,
    //                 'id' => $item->id,
    //                 'page' => $pageNum
    //             ])
    //         ];
    //     });
    // }

    // /**
    //  * 검색 Form 정보를 반환한다.
    //  * 사용자가 로그인 했을 경우에만 사용 가능하며, 그렇지 않으면 null을 반환.
    //  *
    //  * @param string $boardName     게시판 이름
    //  * @param string $query         검색어
    //  * @param string $type          검색타입
    //  * @param integer $page         페이지 번호
    //  * @return array|null
    //  */
    // private function searchForm(string $boardName,
    //                             string $query,
    //                             string $type,
    //                             int $page = 1): ?array
    // {
    //     $searchForm = null;

    //     // 로그인 한 사용자만 검색 가능
    //     if (Auth::check()) {
    //         $searchForm = [
    //             'types' => Board::searchTypes(),
    //             'action' => route('laraboard.list.view', [
    //                 'boardName' => $boardName,
    //                 'page' => ($page == 1) ? null : $page
    //             ]),
    //             'type' => $type,
    //             'query' => $query
    //         ];
    //     }

    //     return $searchForm;
    // }

    // /**
    //  * 페이지에서 사용할 Route 정보를 반환한다.
    //  *
    //  * @return array
    //  */
    // private function routes3(string $boardName): array
    // {
    //     $userRoles = $this->userRoles();

    //     $writeRoute = null;
    //     if ($userRoles['can_write_post']) {
    //         $writeRoute = '#';
    //         // $writeRoute = route('laraboard.write.view', [
    //         //     'boardName' => $boardName
    //         // ]);
    //     }

    //     return [
    //         'list' => Request::url(),
    //         'write' => $writeRoute
    //     ];
    // }
}
