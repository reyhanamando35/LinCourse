<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        // UPLOADS_DRIVER=s3 (dipakai di Vercel, filesystem-nya tidak permanen): disk 'local' & 'public'
        // pindah ke dua bucket Supabase Storage. Tanpa env itu, keduanya tetap folder storage/ biasa.
        'local' => env('UPLOADS_DRIVER') === 's3' ? [
            'driver' => 's3',
            'key' => env('SUPABASE_S3_KEY'),
            'secret' => env('SUPABASE_S3_SECRET'),
            'region' => env('SUPABASE_S3_REGION'),
            'endpoint' => env('SUPABASE_S3_ENDPOINT'),
            'bucket' => env('SUPABASE_PRIVATE_BUCKET', 'lincourse-private'),
            'use_path_style_endpoint' => true,
            'throw' => false,
            'report' => false,
        ] : [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => env('UPLOADS_DRIVER') === 's3' ? [
            'driver' => 's3',
            'key' => env('SUPABASE_S3_KEY'),
            'secret' => env('SUPABASE_S3_SECRET'),
            'region' => env('SUPABASE_S3_REGION'),
            'endpoint' => env('SUPABASE_S3_ENDPOINT'),
            'bucket' => env('SUPABASE_PUBLIC_BUCKET', 'lincourse-public'),
            // Bucket di-set Public di dashboard Supabase; S3 Supabase tidak mendukung ACL, jadi tanpa 'visibility'
            'url' => env('SUPABASE_PUBLIC_URL'),
            'use_path_style_endpoint' => true,
            'throw' => false,
            'report' => false,
        ] : [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            // Relatif, supaya URL gambar ikut host yang sedang dipakai (localhost, 127.0.0.1, dll.)
            'url' => '/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
