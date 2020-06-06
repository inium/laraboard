<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Inium\Laraboard\App\Role;
use Faker\Generator as Faker;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'name'        => $faker->unique()->word,
        'description' => $faker->text,
        'is_admin'    => $faker->boolean(3)
    ];
});
