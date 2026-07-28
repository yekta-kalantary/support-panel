<?php

return [
    'admin' => [
        'first_name' => env('ADMIN_FIRST_NAME', 'مدیر'),
        'last_name' => env('ADMIN_LAST_NAME', 'سیستم'),
        'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        'mobile' => env('ADMIN_MOBILE', '09120000000'),
        'password' => env('ADMIN_PASSWORD', 'ChangeMe123!'),
    ],
    'attachments' => [
        'disk' => env('TICKET_ATTACHMENT_DISK', 'local'),
        'max_files' => 5,
        'max_size_kb' => 10 * 1024,
        'extensions' => ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'txt', 'zip'],
    ],
];
