<?php
namespace Inium\Laraboard\Support\Collection;

class LaraboardPostListBrief
{
    public function __invoke()
    {
        /**
         * 조회한 게시글 목록에서 페이지에 출력할 항목들만을 정리하여 반환한다.
         * 
         * @return array
         */
        return function (int $page = 1) {

            $pageNum = ($page == 1) ? null : $page;

            return $this->transform(function ($item) use ($pageNum) {
                return [
                    'id'=> $item->id,
                    'subject' => $item->subject,
                    'created_at'=> $item->created_at,
                    'updated_at' => $item->updated_at,
                    'view_count' => $item->view_count,
                    'notice' => $item->notice,
                    'comments_count' => $item->comments_count,
                    'user' => [
                        'id' => $item->user->id,
                        'nickname' => $item->user->nickname
                    ],
                    'postUrl' => route('laraboard.post.view', [
                        'boardName' => $item->board->name,
                        'id' => $item->id,
                        'page' => $pageNum
                    ], false)
                ];
            });

        };
    }
}
