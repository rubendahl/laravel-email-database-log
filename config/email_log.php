<?php

return [
    'folder' => env('EMAIL_LOG_ATTACHMENT_FOLDER','email_log_attachments'),
    'access_middleware' => env('EMAIL_LOG_ACCESS_MIDDLEWARE',null),
    'routes_prefix' => env('EMAIL_LOG_ROUTES_PREFIX',''), //when changing prefix please be sure to update the webhook's URLs also
    'mailgun' => [
        'secret' => env('MAILGUN_SECRET', null),
    ],
    'save_attachments' => env('EMAIL_LOG_SAVE_ATTACHMENT','folder'),
    'store_user_id' => env('EMAIL_LOG_STORE_USER_ID',false),
];
