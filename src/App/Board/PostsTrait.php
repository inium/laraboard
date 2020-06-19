<?php

namespace Inium\Laraboard\App\Board;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\Board\BoardRoute;

trait PostsTrait
{
    /**
     * 게시글 목록을 반환한다.
     *
     * @param string $boardName     게시판 이름
     * @param integer $page         페이지 번호
     * @return array
     */
    private function posts(string $boardName, int $page = 1): array
    {
        $board = Board::findByName($boardName);
        $notices = $board->getNotices();
        $posts = $board->getPosts($page);

        return [
            'board' => $board,
            'notices' => $notices,
            'posts' => $posts
        ];

        // $boardAttrs = property_exists($this, 'boardAttrs') ? $this->boardAttrs : null;
        // $postsAttrs = property_exists($this, 'postsAttrs') ? $this->postsAttrs : null;
        // $paginationAttrs = property_exists($this, 'paginationAttrs') ? $this->paginationAttrs : null;
        // // $includePostsUrl = true;

        // $ret = [
        //     'board' => $board->onlyOrAll($boardAttrs),
        //     'notices' => $notices->onlyOrAll($postsAttrs),
        //     // 'notices' => $notices->map(function ($item) use ($postsAttrs) {
        //     //                 $dot = Arr::dot($item->toArray());
        //     //                 return Arr::only($dot, $postsAttrs);
        //     //                 // $item['qwer'] = 'asdf';
        //     //                 // return $item;
        //     //             }),
        //     'posts' => $posts->getCollection()->onlyOrAll($postsAttrs),
        //     'pagination' => Arr::only($posts->toArray(), $paginationAttrs)
        // ];

        // return $ret;

        // dd($ret);

        // return [
        //     'board' => $this->postsBoard($board),
        //     'notices' => $this->postsContents($notices),
        //     'posts' => $this->postsContents($posts->getCollection(), $page),
        //     'pagination' => $this->pagination($posts, $boardName)
        // ];
    }

    /**
     * 검색 결과 목록을 반환한다.
     *
     * @param string $boardName     게시판 이름
     * @param string $query         검색어
     * @param integer $page         페이지 번호
     * @return array
     */
    private function search(string $boardName,
                            string $query,
                            int $page = 1): array
    {
        $board = Board::findByName($boardName);
        $search = $board->search($query, $page);

        $queryParams = [
            'query' => $query
        ];

        return [
            'board' => $this->postsBoard($board),
            'searchBrief' => $this->searchBrief($search, $query),
            'posts' => $this->postsContents($search->getCollection(),
                                            $page,
                                            $query),
            'pagination' => $this->pagination($search, $boardName, $queryParams)
        ];
    }

    /**
     * 페이지에 표시할 게시판 정보를 반환한다.
     *
     * @param Board $board  게시판 정보
     * @return array
     */
    private function postsBoard(Board $board): array
    {
        return $board->only(['name', 'name_ko']);
    }

    /**
     * 페이지에 표시할 게시글 목록 정보를 반환한다.
     *
     * @param Collection $contents  게시글 목록
     * @param integer $page         페이지 번호
     * @param string $query         검색어
     * @return Collection
     */
    private function postsContents(Collection $contents,
                                   int $page = 1,
                                   string $query = null): Collection
    {
        return $contents->map(function ($item) use ($page, $query) {
            return [
                'id' => $item->id,
                'subject' => $item->subject,
                'created_at'=> $item->created_at,
                'view_count' => $item->view_count,
                'notice' => $item->notice,
                'comments_count' => $item->comments_count,
                'user' => [
                    'nickname' => $item->user->nickname,
                    'thumbnail_path' => $item->user->thumbnail_path
                ],
                'board' => [
                    'name' => $item->board->name,
                    'name_ko' => $item->board->name_ko,
                ],
                'post_url' => BoardRoute::routePost($item->board->name,
                                                    $item->id,
                                                    $page,
                                                    $query)
            ];
        });
    }

    /**
     * 게시글 목록 페이지네이션을 반환한다.
     *
     * @param LengthAwarePaginator $posts   게시글 목록 정보
     * @param string $boardName             게시판 이름
     * @param array $queryParams            페이지네이션에 사용할 Query Params
     * @param string $pageName              페이지 숫자 Query Param 이름
     * @return array
     */
    private function pagination(LengthAwarePaginator $posts,
                                string $boardName,
                                array $queryParams = null,
                                string $pageName = 'page'): array
    {
        $pagination = [
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'base_path' => BoardRoute::routeListSearch($boardName),
            'page_name' => $pageName
        ];

        if ($queryParams) {
            $pagination['query_params'] = $queryParams;
        }

        return $pagination;
    }

    /**
     * 검색결과 요약 정보를 반환한다.
     * - 검색시간, 검색 결과 개수, 검색어 반환
     *
     * @param Collection $search    검색 결과 목록
     * @param string $query         검색어
     * @return array
     */
    private function searchBrief(LengthAwarePaginator $search,
                                 string $query): array
    {
        return [
            'time' => Carbon::now(),        // 검색 시간
            'count' => $search->total(),    // 검색 결과 개수
            'query' => $query               // 검색어 
        ];
    }

    /**
     * 검색 Form 정보를 반환한다
     *
     * @param string $boardName     검색할 게시판 영문이름
     * @return array|null
     */
    private function searchForm(string $boardName): ?array
    {
        return [
            'action' => BoardRoute::routeListSearch($boardName)
        ];
    }
}
