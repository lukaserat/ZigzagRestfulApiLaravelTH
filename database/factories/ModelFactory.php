<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'username' => $faker->userName,
        'password' => $faker->password,
    ];
});

$factory->define(App\Phone::class, function (Faker\Generator $faker) {
    return [
        'value' => '+63 9'
            .$faker->randomNumber(2, true).' '
            .$faker->randomNumber(3, true).' '
            .$faker->randomNumber(4, true), // we will be using philippine country format
    ];
});
