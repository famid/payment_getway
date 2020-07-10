<?php

/** @var Factory $factory */

use App\Blog;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Blog::class, function (Faker $faker) {
    return array(
        'title' => $faker->title,
        'description' => $faker->text,
        'tags' => 'romantic'

    );
});
