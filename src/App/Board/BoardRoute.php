<?php

namespace Inium\Laraboard\App\Board;

class BoardRoute
{
    /**
     * 게시글 목록 & 검색결과 Route를 반환한다.
     *
     * @param string $boardName     게시판 이름
     * @param integer $page         페이지 번호
     * @param string $query         검색어
     * @return string
     */
    public static function routeListSearch(string $boardName,
                                           int $page = 1,
                                           string $query = null): string
    {
        $pageNum = ($page == 1) ? null : $page;

        return route('laraboard.posts.view', [
                        'boardName' => $boardName,
                        'page' => $pageNum,
                        'query' => $query
                    ]);
    }

    /**
     * 게시글 보기 페이지 Route를 반환한다.
     *
     * @param string $boardName     게시판 이름
     * @param integer $postId       게시글 ID
     * @param integer $page         페이지 번호
     * @param string $query         검색어
     * @return string
     */
    public static function routePost(string $boardName,
                                     int $postId = 1,
                                     int $page = 1,
                                     string $query = null): string
    {
        $pageNum = ($page == 1) ? null : $page;

        return route('laraboard.post.view', [
                        'boardName' => $boardName,
                        'id' => $postId,
                        'page' => $pageNum,
                        'query' => $query
                    ]);
    }

    /**
     * 게시글 쓰기 페이지 Route를 반환한다.
     *
     * @param string $boardName     게시판 이름
     * @return string
     */
    public static function routePostWrite(string $boardName): string
    {
        return '#';
    }
}
