<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Inium\Laraboard\App\Post;
use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\User;
use Inium\Laraboard\Support\Detect\Agent;
use Inium\Laraboard\Support\Faker\RandomWysiwygFragment;
use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Str;

$fakerKo = FakerFactory::create('ko_KR');

$factory->define(Post::class, function (Faker $faker) use ($fakerKo) {
    // 게시판 존재 여부 확인
    // 게시판이 없을 경우, 게시판 생성 후 게시글을 저장할 게시판 랜덤 선택
    if (Board::count() == 0) {
        factory(Board::class, 1)->create();
    }
    $board = Board::inRandomOrder()->first();

    // 공지글 여부를 위해 관리자 정보 가져올지 여부 설정 후 선택
    $isAdmin = $faker->boolean(10);
    $user = User::whereHas('role', function ($q) use ($isAdmin) {
        $q->where('is_admin', $isAdmin);
    })->inRandomOrder()->first();

    // faker로 생성한 user agent 분석
    $ua = Agent::parse($faker->userAgent);

    $tagWords = $faker->words(rand() % 5);

    $content = RandomWysiwygFragment::generate($fakerKo, (rand() % 5) + 10);

    return [
        'ip_address'      => encrypt($faker->ipv4),
        'user_agent'      => $ua->agent,
        'device_type'     => $ua->device_type,
        'os_name'         => $ua->os_name,
        'os_version'      => $ua->os_version,
        'browser_name'    => $ua->browser_name,
        'browser_version' => $ua->browser_version,
        'notice'          => $isAdmin,
        'subject'         => $fakerKo->realText($fakerKo->numberBetween(50,85)),
        'content'         => htmlspecialchars($content),
        'content_pure'    => strip_tags($content),
        'view_count'      => $faker->numberBetween(1, 3000),
        'point'           => $board->post_point,
        'board_id'        => $board->id,
        'wrote_user_id'   => $user->id,
        'updated_at'      => null // 추가 시 updated_at 무시
    ];
});
