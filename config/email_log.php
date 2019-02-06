<?php

return [
    'disk' => env('EMAIL_LOG_ATTACHMENT_DISK','email_log_attachments'),
    'access_middleware' => env('EMAIL_LOG_ACCESS_MIDDLEWARE',null),
    'access_middleware_api' => env('EMAIL_LOG_ACCESS_MIDDLEWARE_API',null),
    'routes_prefix' => env('EMAIL_LOG_ROUTES_PREFIX',''), //when changing prefix please be sure to update the webhook's URLs also
    'routes_prefix_api' => env('EMAIL_LOG_ROUTES_PREFIX_API',''),
    'routes_webhook_prefix' => env('EMAIL_LOG_ROUTES_WEBHOOK_PREFIX', env('EMAIL_LOG_ROUTES_PREFIX','')), //when changing prefix please be sure to update the webhook's URLs also
    'save_attachments' => env('EMAIL_LOG_SAVE_ATTACHMENT','folder'),
    'store_user_id' => env('EMAIL_LOG_STORE_USER_ID',false),
    'mailgun' => [
        'secret' => env('MAILGUN_SECRET', null),
        'filter_unknown_emails' => env('EMAIL_LOG_MAILGUN_FILTER_UNKNOWN_EMAILS', true),
    ],
];
