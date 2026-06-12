<?php

return [

  /*
  |--------------------------------------------------------------------------
  | VAPID (Web Push API)
  |--------------------------------------------------------------------------
  |
  | Klucze wygeneruj: php artisan webpush:vapid
  |
  */

    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'mailto:'.env('MAIL_FROM_ADDRESS', 'hello@example.com')),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
        'pem_file' => env('VAPID_PEM_FILE'),
    ],

    'model' => \NotificationChannels\WebPush\PushSubscription::class,

    'database_connection' => env('WEBPUSH_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),

    'table_name' => env('WEBPUSH_TABLE_NAME', 'push_subscriptions'),

    'client_options' => [],

];
