<?php

namespace Inium\Laraboard\App\Board;

use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\Post;

trait PostTrait
{
    /**
     * 게시글 정보를 가져온다.
     *
     * @param string $boardName     게시판 이름
     * @param integer $id           게시글 ID
     * @return array
     */
    protected function post(string $boardName, int $id): array
    {
        $board = Board::findByName($boardName);
        $post = $board->getPost($id);

        return [
            'notice' => $post->notice,
            'subject' => $post->subject,
            'content' => $post->content,
            'view_count' => $post->view_count,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
            'comments_count' => $post->comments_count,
            'tags' => json_decode($post->tag_json),
            'user' => [
                'nickname' => $post->user->nickname,
                'introduce' => $post->user->introduce
            ],
            'board' => [
                'name' => $post->board->name,
                'name_ko' => $post->board->name_ko
            ]
        ];
    }
}
