<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Laraboard\Privilege;
use Faker\Generator as Faker;

$factory->define(Privilege::class, function (Faker $faker) {
    // 한글 설명을 위한 faker 생성
    // $fakerKr = \Faker\Factory::create('ko_KR');

    return [
        'name' => $faker->unique()->word,
        // 'description' => $fakerKr->realText(),
        'description' => $faker->text,
        'is_admin' => $faker->boolean(3)
    ];
});
