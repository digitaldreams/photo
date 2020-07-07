<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Photo\Models\Photo;

$factory->define(Photo::class, function (Faker $faker) {
    return [
        'src'         => $faker->imageUrl(),
        'caption'     => $faker->realText(),
        'captured_at' => $faker->dateTime,
    ];
});
