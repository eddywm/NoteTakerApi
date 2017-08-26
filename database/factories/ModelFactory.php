<?php


/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\Note::class, function (Faker\Generator $faker) {


    $sentence = $faker->sentence(4);
    $reminderDate = $faker->dateTime();
    return [
        'title' => $sentence,
        'body' => $faker->paragraph,
        'is_seen' => $faker->boolean,
        'reminder_date' => $reminderDate,
        'user_id' => $faker->numberBetween(1,10),
        'slug' => str_slug($sentence." ".$reminderDate->format("Y-m-d"))
    ];
});


