<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Laraboard\Model\User as LaraboardUser;
use App\Laraboard\Model\Privilege as LaraboardPrivilege;
use App\User as AuthUser;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

$factory->define(LaraboardUser::class, function (Faker $faker) {
    // 사용자 생성
    $user = factory(AuthUser::class)->create();

    // 테스트용 닉네임 생성
    $nickname = Str::slug($user->name, '_');
    $hash = Str::random(5);

    // 게시판 사용자 권한 정보
    $count = LaraboardPrivilege::count();
    $boardUserPrivilegeId = 0;

    // 게시판 사용자 권한 정보가 존재하지 않는 경우
    // 사용자 권한 생성 후 앞서 생성한 사용자에게 관리자 권한 추가
    if ($count == 0) {
        $collections = [
            ['name' => 'admin', 'description' => 'admin', 'is_admin' => true],
            ['name' => 'lv10',  'description' => 'lv10',  'is_admin' => false],
            ['name' => 'lv9',   'description' => 'lv9',   'is_admin' => false],
            ['name' => 'lv8',   'description' => 'lv8',   'is_admin' => false],
            ['name' => 'lv7',   'description' => 'lv7',   'is_admin' => false],
            ['name' => 'lv6',   'description' => 'lv6',   'is_admin' => false],
            ['name' => 'lv5',   'description' => 'lv5',   'is_admin' => false],
            ['name' => 'lv4',   'description' => 'lv4',   'is_admin' => false],
            ['name' => 'lv3',   'description' => 'lv3',   'is_admin' => false],
            ['name' => 'lv2',   'description' => 'lv2',   'is_admin' => false],
            ['name' => 'lv1',   'description' => 'lv1',   'is_admin' => false]
        ];

        $privileges = collect($collections)->map(function ($elem) {
            return factory(LaraboardPrivilege::class)->create($elem);
        });

        foreach($privileges as $elem) {
            if ($elem->is_admin == true) {
                $boardUserPrivilegeId = $elem->id;
                break;
            }
        }
    }
    // 사용자 권한 정보가 존재하는 경우
    // 관리자를 제외한 나머지 사용자의 권한 중 하나를 사용자에게 추가
    else {
        $privileges = LaraboardPrivilege::where('is_admin', false)
                                        ->orderBy('id', 'DESC')
                                        ->take(3)
                                        ->get();

        $privilege = Arr::random($privileges->toArray());

        $boardUserPrivilegeId = $privilege['id'];
    }

    return [
        'nickname' => "{$nickname}_{$hash}",
        'introduce' => $faker->realText,

        // faker의 image는 lorempixel을 이용하나 lorempixel이 Shutdown 되었음.
        // 그래서 아래와 같이 loremflickr.com의 static 경로 사용.
        // 'thumbnail_path' => $faker->imageUrl('storage/app/public'),
        'thumbnail_path' => 'https://loremflickr.com/320/240',

        'user_id' => $user->id,
        'board_user_privilege_id' => $boardUserPrivilegeId
    ];
});
