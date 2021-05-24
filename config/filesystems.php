<?php

return [
    'default' => 'local',
    'disks' => [
        'backupFiles' => [
            'driver' => 'local',
            'root' => env('BACKUP_FILES_DIRECTORY'),
        ],
        's3' => [
            'driver' => 's3',
            'key'    => env('AWS_PUBLIC_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'cache'  => [
                'store'  => 'file',
                'prefix' => 's3-cache',
                'expire' => 900,
            ],
        ]
    ],
];
