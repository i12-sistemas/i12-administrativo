<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'domains' => [
          'driver' => 'local',
          'root' => storage_path('app/public'),
          'url' => env('APP_URL').'/storage',
          'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],
        's3icom' => [
          'driver' => 's3',
          'key' => env('AWS_ICOM_ACCESS_KEY_ID'),
          'secret' => env('AWS_ICOM_SECRET_ACCESS_KEY'),
          'region' => env('AWS_ICOM_DEFAULT_REGION'),
          'bucket' => env('AWS_ICOM_BUCKET'),
        ],
        's3icom_public' => [
          'driver' => 's3',
          'key' => env('AWS_ICOM_ACCESS_KEY_ID'),
          'secret' => env('AWS_ICOM_SECRET_ACCESS_KEY'),
          'region' => env('AWS_ICOM_DEFAULT_REGION'),
          'bucket' => env('AWS_ICOM_BUCKET'),
          'visibility' => 'public',
        ],
        's3backup' => [
          'driver' => 's3',
          'key' => env('AWS_BACKUP_ACCESS_KEY_ID'),
          'secret' => env('AWS_BACKUP_SECRET_ACCESS_KEY'),
          'region' => env('AWS_BACKUP_DEFAULT_REGION'),
          'bucket' => env('AWS_BACKUP_BUCKET'),
        ],
        's3backup_1' => [
          'driver' => 's3',
          'key' => env('AWS_BACKUP_ACCESS_KEY_ID_1'),
          'secret' => env('AWS_BACKUP_SECRET_ACCESS_KEY_1'),
          'region' => env('AWS_BACKUP_DEFAULT_REGION_1'),
          'bucket' => env('AWS_BACKUP_BUCKET_1'),
        ],
        's3tabelaibpt' => [
          'driver' => 's3',
          'key' => env('AWS_IBPT_REPO_ACCESS_KEY_ID'),
          'secret' => env('AWS_IBPT_REPO_SECRET_ACCESS_KEY'),
          'region' => env('AWS_IBPT_REPO_DEFAULT_REGION'),
          'bucket' => env('AWS_IBPT_REPO_BUCKET')
        ]

    ],

];
