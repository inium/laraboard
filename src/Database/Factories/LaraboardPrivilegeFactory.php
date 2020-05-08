<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Inium\Laraboard\Models\Privilege;
use Faker\Generator as Faker;

$factory->define(Privilege::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word,
        'description' => $faker->text,
        'is_admin' => $faker->boolean(3)
    ];
});
