<?php

namespace Inium\Laraboard\App\Board;

use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\Post;
use Inium\Laraboard\App\User;
use Inium\Laraboard\Support\Facades\Agent;

trait PostTrait
{
    /**
     * 게시글 목록을 반환한다.
     *
     * @param string $boardName     게시판 이름
     * @param integer $page         페이지 번호
     * @return array
     */
    private function getPostList(string $boardName, int $page = 1): array
    {
        $board = Board::findByName($boardName);
        $notices = $board->getNotices();
        $posts = $board->getPosts($page);

        return [
            'board' => $board,
            'notices' => $notices,
            'posts' => $posts
        ];
    }

    /**
     * 게시글을 가져온다.
     *
     * @param string $boardName             게시판 이름
     * @param integer $id                   게시글 ID
     * @param boolean $incrementViewCount   조회수 증가여부.
     * @return Post
     */
    private function getPost(string $boardName,
                             int $id,
                             bool $incrementViewCount = true): Post
    {
        $board = Board::findByName($boardName);
        $post = $board->getPost($id);

        // 조회수 1 증가
        if ($incrementViewCount) {
            $post->incrementViewCount();
        }

        return $post;
    }

    /**
     * 게시글을 추가한다.
     *
     * @param string $userAgent     User Agent String
     * @param string $boardName     게시판 이름
     * @param string $subject       제목
     * @param string $content       게시글
     * @param boolean $notice       공지사항 여부. true / false.
     * @return integer              삽입된 게시글 ID
     */
    private function submitPost(string $userAgent,
                                string $boardName,
                                string $subject,
                                string $content,
                                bool $notice): int
    {
        $board = Board::findByName($boardName);
        $user = User::findByUserId(Auth::id());
        $ua = Agent::parse($userAgent); 

        $notice = $user->role->is_admin ? $notice : false;

        $post = new Post();

        $post->user_agent = $ua->agent;
        $post->device_type = $ua->device_type;
        $post->os_name = $ua->os_name;
        $post->os_version = $ua->os_version;
        $post->browser_name = $ua->browser_name;
        $post->browser_version = $ua->browser_version;
        $post->notice = $notice;
        $post->subject = strip_tags($subject);
        $post->content = htmlspecialchars($content);
        $post->content_pure = strip_tags($content);
        $post->view_count = 0;
        $post->point = $board->post_point;
        $post->board_id = $board->id;
        $post->wrote_user_id = $user->id;

        $post->save();

        return $post->id;
    }

    /**
     * 게시글을 수정한다.
     *
     * @param string $userAgent     User Agent String
     * @param string $boardName     게시판 이름
     * @param integer $id           게시글 ID
     * @param string $subject       제목
     * @param string $content       게시글
     * @param boolean $notice       공지사항 여부. true / false.
     * @return boolean
     */
    private function putPost(string $userAgent,
                             string $boardName,
                             int $id,
                             string $subject,
                             string $content,
                             bool $notice): bool
    {
        $post = $this->getPost($boardName, $id);
        $user = User::findByUserId(Auth::id());
        $ua = Agent::parse($userAgent); 

        $notice = $user->role->is_admin ? $notice : false;

        $post->user_agent = $ua->agent;
        $post->device_type = $ua->device_type;
        $post->os_name = $ua->os_name;
        $post->os_version = $ua->os_version;
        $post->browser_name = $ua->browser_name;
        $post->browser_version = $ua->browser_version;
        $post->notice = $notice;
        $post->subject = strip_tags($subject);
        $post->content = htmlspecialchars($content);
        $post->content_pure = strip_tags($content);
        // $post->view_count = 0;
        // $post->point = $board->post_point;
        // $post->board_id = $board->id;
        // $post->wrote_user_id = $user->id;

        $updated = $post->save();

        return $updated;
    }

    /**
     * 게시글을 삭제한다.
     *
     * @param integer $id   게시글 ID
     * @return boolean
     */
    private function deletePostById(int $id): bool
    {
        return Post::destroy($id);
    }
}
