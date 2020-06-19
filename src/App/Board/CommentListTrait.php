<?php

namespace Inium\Laraboard\App\Board;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board;

trait CommentListTrait
{
    /**
     * 댓글 목록을 가져온다.
     *
     * @param string $boardName             게시판 이름
     * @param integer $postId               게시글 ID
     * @param integer $page                 페이지 번호
     * @param integer $parentCommentId      부모 댓글 ID
     * @return array
     */
    protected function comments(string $boardName,
                             int $postId,
                             int $page = 1,
                             int $parentCommentId = 0): array
    {
        $board = Board::findByName($boardName);
        $post = $board->getPost($postId);
        $comments = $post->getComments($page);

        return [
            'comments' => $this->pageCommentList($comments->getCollection()),
            'pagination' => $this->commentPagination($comments)
        ];
    }

    // protected function children()
    // {

    // }

    /**
     * 페이지에 표시할 댓글 정보들을 반환한다.
     *
     * @param Collection $comments  댓글 목록
     * @return Collection
     */
    private function pageCommentList(Collection $comments): Collection
    {
        return $comments->map(function ($item) {
            return [
                'id' => $item->id,
                'content' => $item->content,
                'parent_comment_id' => $item->parent_comment_id,
                'children_count' => $item->children_count,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'user' => [
                    'nickname' => $item->user->nickname,
                    'thumbnail_path' => $item->user->thumbnail_path
                ]
            ];
        });
    }

    /**
     * 댓글 페이지네이션 정보를 반환한다.
     *
     * @param Collection $comments  댓글 목록
     * @return array
     */
    private function commentPagination(LengthAwarePaginator $comments): array
    {
        return [
            'current_page' => $comments->currentPage(),
            'last_page' => $comments->lastPage(),
            // 'base_path' => route('laraboard.postList.view', [
            //     'boardName' => $boardName
            // ]),
        ];
    }
}
