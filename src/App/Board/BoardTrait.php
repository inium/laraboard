<?php

namespace Inium\Laraboard\App\Board;

use Inium\Laraboard\App\Board;

trait BoardTrait
{
    /**
     * 게시판 이름으로 게시판 정보를 반환한다.
     *
     * @param string $boardName     게시판 이름
     * @return Board
     */
    public function getBoardByName(string $boardName): Board
    {
        return Board::findByName($boardName);
    }
}
