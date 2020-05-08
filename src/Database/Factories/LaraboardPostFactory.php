<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Inium\Laraboard\Models\Post as LaraboardPost;
use Inium\Laraboard\Models\Board as LaraboardBoard;
use Inium\Laraboard\Models\User as LaraboardUser;
use Inium\Laraboard\Facade\Agent as LaraboardAgent;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(LaraboardPost::class, function (Faker $faker) {
    // 게시판 존재 여부 확인
    // 게시판이 없을 경우, 게시판 생성 후 게시글을 저장할 게시판 랜덤 선택
    $boardCount = LaraboardBoard::count();
    $board = null;
    if ($boardCount == 0) {
        factory(LaraboardBoard::class, 1)->create();
    }
    $board = LaraboardBoard::inRandomOrder()->first();

    // 공지글 여부를 위해 관리자 정보 가져올지 여부 설정 후 선택
    $isAdmin = $faker->boolean(10);
    $boardUser = LaraboardUser::whereHas('privilege',
        function ($q) use ($isAdmin) {
            $q->where('is_admin', $isAdmin);
        }
    )->inRandomOrder()->first();

    // faker로 생성한 user agent 분석
    $ua = LaraboardAgent::parse($faker->userAgent);

    // faker를 이용해 생성한 파일 업로드 정보 생성
    $fileExt = $faker->fileExtension;
    $originalFileName = $faker->safeColorName;
    $uploadFilename = Str::random(20);

    // 첨부파일 여부를 결정하여 첨부파일 정보 저장
    $attachmentJson = null;
    if ($faker->boolean(30)) {
        $attachmentJson = json_encode([
            'mime_type' => $faker->mimeType,
            'ext' => $fileExt,
            'original_name' => "{$originalFileName}.{$fileExt}",
            'path' => "/public/{$uploadFilename}.{$fileExt}",
            'size' => $faker->numberBetween(1000, 50000),
            'destination' => '/public'
        ]);
    }

    $content = $faker->text;

    return [
        'user_agent' => $ua['agent'],
        'device_type' => $ua['device_type'],
        'os_name' => $ua['os_name'],
        'os_version' => $ua['os_version'],
        'browser_name' => $ua['browser_name'],
        'browser_version' => $ua['browser_version'],
        'notice' => $isAdmin,
        'subject' => $faker->sentence(10),
        'content' => htmlspecialchars($content),
        'content_pure' => strip_tags($content),
        'attachment_json' => $attachmentJson,
        'view_count' => $faker->numberBetween(1, 3000),
        'point' => $board->post_point,
        'board_id' => $board->id,
        'wrote_user_id' => $boardUser->id,
    ];
});
