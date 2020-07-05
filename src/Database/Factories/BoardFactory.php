<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Inium\Laraboard\App\Board;
use Inium\Laraboard\App\User;
use Inium\Laraboard\App\Role;
use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Arr;

$fakerKo = FakerFactory::create('ko_KR');

$factory->define(Board::class, function (Faker $faker) use ($fakerKo) {
    // 게시판 사용자가 존재하지 않는 경우, 생성 후 사용
    $count = User::count();
    if ($count == 0) {
        factory(User::class, 10)->create();
    }

    // 게시판을 생성할 관리자 정보를 가져온다.
    $admin = User::whereHas('role', function($q) {
        $q->where('is_admin', true);
    })->inRandomOrder()->first();

    $r = Role::where('is_admin', false)->orderBy('id', 'DESC')->take(3)->get();
    $role = Arr::random($r->toArray());

    return [
        'name'                      => 'free',
        'name_ko'                   => '자유게시판',
        'description'               => $fakerKo->realText,
        'post_point'                => $faker->randomElement(array(10, 15, 20)),
        'comment_point'             => $faker->randomElement(array(1, 5)),
        'post_rows_per_page'        => $faker->randomElement(array(20, 25, 30)),
        'comment_rows_per_page'     => $faker->randomElement(array(20, 30, 50)),
        'min_post_read_role_id'     => $role['id'],
        'min_post_write_role_id'    => $role['id'],
        'min_comment_read_role_id'  => $role['id'],
        'min_comment_write_role_id' => $role['id'],
        'create_user_id'            => $admin->id,
        'updated_at'                => null // 추가 시 updated_at 무시
    ];
});
