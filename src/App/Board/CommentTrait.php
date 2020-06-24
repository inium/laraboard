<?php

namespace Inium\Laraboard\App\Board;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board;

trait CommentTrait
{
    /**
     * 댓글 목록을 가져온다.
     *
     * @param string $boardName             게시판 이름
     * @param integer $postId               게시글 ID
     * @param integer $commentPage          댓글 페이지 번호
     * @param integer $parentCommentId      부모 댓글 ID
     * @return Collection
     */
    private function getComments(string $boardName,
                                 int $postId,
                                 int $commentPage = 1): LengthAwarePaginator
    {
        $board = Board::findByName($boardName);
        return  $board->getPost($postId)->getHierarchicalComments($commentPage);
    }
}
