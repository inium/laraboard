<?php

namespace Inium\Laraboard\App\Board;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\Post;
use Inium\Laraboard\App\User;
use Inium\Laraboard\App\Board\BoardRoute;
// use Inium\Laraboard\App\Board\BoardUserRolesTrait;
// use Inium\Laraboard\App\Board\RenderTemplateTrait;
// use Inium\Laraboard\App\Board\RouteTrait;

trait PostTrait
{
    /**
     * Undocumented function
     *
     * @param string $boardName
     * @param integer $postId
     * @param string $query
     * @param integer $page
     * @return array
     */
    public function post(string $boardName,
                         int $postId,
                         string $query = null,
                         int $page = 1): array
    {
        $board = Board::findByName($boardName);
        $post = $board->getPost($id);

        return [
            'board' => $this->postBoard($board),
            'post' => $this->postContents($post),
            'route' => [
                
            ]
        ];
    }

    /**
     * 페이지에 표시할 게시판 정보를 반환한다.
     *
     * @param Board $board  게시판 정보
     * @return array
     */
    private function postBoard(Board $board): array
    {
        return $board->only(['name', 'name_ko']);
    }

    private function postContents(Post $post): array
    {
        $user = User::findByUserId(Auth::id());
        $postOwner = ($user->id == $post->user->id) ? true : false;

        return [
            'id' => $post->id,
            'notice' => $post->notice,
            'subject' => $post->subject,
            'content' => $post->content,
            'view_count' => $post->view_count,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
            'comments_count' => $post->comments_count,
            'user' => [
                'nickname' => $post->user->nickname,
                'thumbnail_path' => $post->user->thumbnail_path
            ],
            'board' => [
                'name' => $post->board->name,
                'name_ko' => $post->board->name_ko
            ],
            'modify_url' => $postOwner ? '#' : null,
            'delete_url' => $postOwner ? '#' : null
        ];
    }
}
