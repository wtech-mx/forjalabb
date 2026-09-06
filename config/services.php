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

    'skydropx' => [
        'base_url' => env('SKYDROPX_BASE_URL', 'https://pro.skydropx.com'),
        'client_id' => env('SKYDROPX_CLIENT_ID'),
        'client_secret' => env('SKYDROPX_CLIENT_SECRET'),
        'origin_postal_code' => env('SKYDROPX_ORIGIN_POSTAL_CODE'),
        'origin_state' => env('SKYDROPX_ORIGIN_STATE'),
        'origin_city' => env('SKYDROPX_ORIGIN_CITY'),
        'origin_neighborhood' => env('SKYDROPX_ORIGIN_NEIGHBORHOOD'),
    ],

    'google_merchant' => [
        'account_id' => env('GOOGLE_MERCHANT_ACCOUNT_ID'),
        'data_source' => env('GOOGLE_MERCHANT_DATA_SOURCE'),
        'credentials' => env('GOOGLE_MERCHANT_CREDENTIALS', 'storage/app/private/google-merchant-service-account.json'),
        'store_url' => env('GOOGLE_MERCHANT_STORE_URL', env('APP_URL')),
    ],

    'drive_gallery' => [
        'api_key' => env('GOOGLE_DRIVE_API_KEY'),
        'folder_id' => env('GOOGLE_DRIVE_GALLERY_FOLDER_ID') ?: '1QXjXh40eUZHRX2Pkq2ZWRxzP-ZYf1B6i',
        'client_id' => env('GOOGLE_DRIVE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_DRIVE_REDIRECT_URI', rtrim((string) env('APP_URL'), '/').'/admin/drive-gallery/google/callback'),
    ],

    'whatsapp_web' => [
        'url' => env('WHATSAPP_WEB_URL', 'http://127.0.0.1:3210'),
        'token' => env('WHATSAPP_API_TOKEN', 'forjalab-local-whatsapp'),
    ],


];
