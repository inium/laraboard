<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Laraboard\Board as LaraboardBoard;
use App\Laraboard\User as LaraboardUser;
use App\Laraboard\Privilege as LaraboardPrivilege;
use Faker\Generator as Faker;

$factory->define(LaraboardBoard::class, function (Faker $faker) {
    // 게시판 사용자가 존재하지 않는 경우, 생성 후 사용
    $count = LaraboardUser::count();
    if ($count == 0) {
        factory(LaraboardUser::class, 10)->create();
    }

    // 게시판을 생성할 관리자 정보를 가져온다.
    $boardUser = LaraboardUser::whereHas('privilege', function($q) {
        $q->where('is_admin', true);
    })->inRandomOrder()->first();

    $privilege = LaraboardPrivilege::where('is_admin', false)->first();

    return [
        'name' => $faker->unique()->safeColorName,
        'name_ko' => $faker->unique()->realText(20),
        'description' => $faker->realText,
        'post_point' => $faker->randomElement(array(10, 15, 20)),
        'comment_point' => $faker->randomElement(array(1, 5)),
        'page_post_num' => $faker->randomElement(array(20, 25, 30)),
        'page_comment_num' => $faker->randomElement(array(20, 30, 50, 100)),
        'min_list_read_privilege_id' => $privilege->id,
        'min_post_read_privilege_id' => $privilege->id,
        'min_post_write_privilege_id' => $privilege->id,
        'min_comment_read_privilege_id' => $privilege->id,
        'min_comment_write_privilege_id' => $privilege->id,
        'create_user_id' => $boardUser->id
    ];
});
