<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Inium\Laraboard\App\Role;
use Faker\Generator as Faker;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'name'        => $faker->unique()->word,
        'description' => $faker->text,
        'is_admin'    => $faker->boolean(3),
        'updated_at'  => null // 추가 시 updated_at 무시
    ];
});
