<?php
/**
 * Factory functions for tests
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
 */
namespace Tests;

use Faker\Factory as Faker;

function app_factory( $params = [] ) {
    $faker = Faker::create();

    return array_merge( [
        'name' => $faker->name,
        'type' => $faker->randomElement( [ 'Webview', 'Link' ] ),
        'creation_type' => $faker->randomElement( [ 'Custom', 'Code' ] ),
        'icon' => $faker->imageUrl(),
        'url' => $faker->url,
        'sort' => $faker->numberBetween( 0, 50 ),
        'slug' => $faker->slug,
        'is_hidden' => $faker->boolean,
    ], $params );
}

function training_factory( $params = [] ) {
    $faker = Faker::create();

    return array_merge( [
        'name' => $faker->name,
        'embed_video' => $faker->url,
        'anchor' => $faker->slug,
        'sort' => $faker->numberBetween( 0, 50 ),
    ], $params );
}

function wp_user_factory( $params = [] ) {
    $faker = Faker::create();

    return array_merge( [
        'user_login' => $faker->userName,
        'user_pass' => $faker->password,
        'user_nicename' => $faker->name,
        'user_email' => $faker->email,
        'user_url' => $faker->url,
        'user_registered' => $faker->dateTimeThisYear->format( 'Y-m-d H:i:s' ),
        'user_status' => 0,
        'display_name' => $faker->name,
        'user_activation_key' => ''
    ], $params );
}

function wp_credentials_factory( $params = [] )
{
    $faker = Faker::create();

    return array_merge([
        'username' => $faker->userName,
        'password' => $faker->password,
        'email' => $faker->email,
    ], $params);
}

function registration_factory( $params = [] ) {
    $faker = Faker::create();

    $password = $faker->password;
    return array_merge( [
        'username' => $faker->userName,
        'email' => $faker->email,
        'password' => $password,
        'confirm_password' => $password,
    ], $params );
}
