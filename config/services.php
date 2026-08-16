<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'tag_mail' => [
        'host' => env('TAG_MAIL_HOST', env('MAIL_HOST')),
        'port' => env('TAG_MAIL_PORT', env('MAIL_PORT', 465)),
        'username' => env('TAG_MAIL_USERNAME', env('MAIL_USERNAME')),
        'password' => env('TAG_MAIL_PASSWORD', env('MAIL_PASSWORD')),
        'encryption' => env('TAG_MAIL_ENCRYPTION', 'ssl'),
        'from_address' => env('TAG_MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS')),
        'from_name' => env('TAG_MAIL_FROM_NAME', 'ForjaLab Emergencias'),
    ],


];
