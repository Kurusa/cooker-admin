<?php

return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'deepseek' => [
        'api_key' => env('DEEPSEEK_API_KEY'),
        'base_uri' => 'https://api.deepseek.com',
    ],

    'telegram' => [
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'token' => env('TELEGRAM_BOT_TOKEN'),
    ],
];
