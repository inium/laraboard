<?php

namespace Inium\Laraboard\App\Board;

use Inium\Laraboard\App\Board;

trait SearchTrait
{
    /**
     * 검색 결과 목록을 반환한다.
     *
     * @param string $boardName     게시판 이름
     * @param string $query         검색어
     * @param integer $page         페이지 번호
     * @return array
     */
    private function getSearchResult(string $boardName,
                                     string $query,
                                     int $page = 1): array
    {
        $board = Board::findByName($boardName);
        $search = $board->search($query, $page);
    
        return [
            'board' => $board,
            'search' => $search
        ];
    }
}
