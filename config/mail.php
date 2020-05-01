<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */

return [
    'aliYun' => [
        'accountName' => env('ALI_MAIL_ACCOUNT_NAME', ''),
        'accessKeyId' => env('ALI_MAIL_ACCESS_KEY_ID', ''),
        'accessKeySecret' => env('ALI_MAIL_ACCESS_KEY_SECRET', ''),
        'fromAlias' => env('ALI_MAIL_FORM_ALIAS', 'sl-im'),
        'subject' => env('ALI_MAIL_SUBJECT', ''),
        'body' => env('ALI_MAIL_BODY', ''),
        'version' => env('ALI_MAIL_VERSION', ''),
        'regionId' => env('ALI_MAIL_REGION_ID', ''),
        'host' => env('ALI_MAIL_HOST', '')
    ]
];
