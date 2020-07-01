<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Photo\Models\Album;

$factory->define(Album::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->realText(),
    ];
});
