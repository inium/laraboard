<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Inium\Laraboard\App\Comment;
use Inium\Laraboard\App\Post;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\User;
use Inium\Laraboard\Support\Detect\Agent;
use Inium\Laraboard\Support\Faker\RandomWysiwygFragment;
use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;

$fakerKo = FakerFactory::create('ko_KR');

$factory->define(Comment::class, function (Faker $faker) use ($fakerKo) {
    // 게시글 존재 여부 확인
    // 게시글 없을 경우, 게시글 생성 후 댓글을 저장할 게시글 랜덤 선택
    $postCount = Post::count();
    $post = null;
    if ($postCount == 0) {
        factory(Post::class, 50)->create();
    }
    $post = Post::inRandomOrder()->first();
    $board = $post->board;

    // 댓글이 1개 이상일 경우, 자식 댓글 추가를 위한 조건 추가
    $parentCommentId = null;
    if ($post->comments()->count() > 0) {
        // 자식 댓글 추가 여부를 랜덤 확률로 결정
        if ($faker->boolean(10)) {
            // 자식 댓글로 추가할 경우, 부모 댓글 결정
            $parentCommentId = $post->comments()->inRandomOrder()->first()->id;
        }
    }

    // 사용자 정보 Get
    $user = User::inRandomOrder()->first();

    // faker로 생성한 user agent 분석
    $ua = Agent::parse($faker->userAgent);

    $content = RandomWysiwygFragment::generate($fakerKo, (rand() % 3) + 2);

    return [
        'ip_address'        => encrypt($faker->ipv4),
        'user_agent'        => $ua->agent,
        'device_type'       => $ua->device_type,
        'os_name'           => $ua->os_name,
        'os_version'        => $ua->os_version,
        'browser_name'      => $ua->browser_name,
        'browser_version'   => $ua->browser_version,
        'content'           => htmlspecialchars($content),
        'content_pure'      => strip_tags($content),
        'point'             => $board->comment_point,
        'parent_comment_id' => $parentCommentId,
        'board_id'          => $board->id,
        'post_id'           => $post->id,
        'wrote_user_id'     => $user->id,
        'updated_at'        => null // 추가 시 updated_at 무시
    ];
});
