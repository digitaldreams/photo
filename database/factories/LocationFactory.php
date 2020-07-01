<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Photo\Models\Location;

$factory->define(Location::class, function (Faker $faker) {
    return [
        'name' => $faker->city,
        'place_id' => $faker->randomNumber(),
        'address' => $faker->streetAddress,
        'locality' => $faker->word,
        'city' => $faker->city,
        'state' => $faker->country,
        'country' => $faker->country,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
    ];
});
