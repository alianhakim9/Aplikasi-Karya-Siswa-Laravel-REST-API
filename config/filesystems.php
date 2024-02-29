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
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        // uncomment untuk hosting 000Webhost tanpa symlink

        // 'local' => [
        //     'driver' => 'local',
        //     'root' => storage_path('app'),
        // ],

        // 'foto_profil_guru' => [
        //     'driver' => 'local',
        //     'root' => public_path() . '/../../public_html/' . env('PUBLIC_HTML_FOLDER') . '/storage/foto_profil/guru',
        //     'url' => env('APP_URL') . '/' . env('PUBLIC_HTML_FOLDER') . '/storage/foto_profil/guru',
        //     'visibility' => 'public',
        // ],

        // 'foto_profil_siswa' => [
        //     'driver' => 'local',
        //     'root' => public_path() . '/../../public_html/' . env('PUBLIC_HTML_FOLDER') . '/storage/foto_profil/siswa',
        //     'url' => env('APP_URL') .  '/' . env('PUBLIC_HTML_FOLDER') . '/storage/public/foto_profil/siswa',
        //     'visibility' => 'public',
        // ],

        // 'karya_citra' => [
        //     'driver' => 'local',
        //     'root' => public_path() . '/../../public_html/' . env('PUBLIC_HTML_FOLDER') . '/storage/karya_citra/gambar',
        //     'url' => env('APP_URL') .  '/' . env('PUBLIC_HTML_FOLDER') . '/storage/karya_citra/gambar/',
        //     'visibility' => 'public',
        // ],

        // 'karya_citra_video' => [
        //     'driver' => 'local',
        //     'root' => public_path() . '/../../public_html/' . env('PUBLIC_HTML_FOLDER') . '/storage/karya_citra/video',
        //     'url' => env('APP_URL') .  '/' . env('PUBLIC_HTML_FOLDER') . '/storage/karya_citra/video/',
        //     'visibility' => 'public',
        // ],

        // 'promosi' => [
        //     'driver' => 'local',
        //     'root' => public_path() . '/../../public_html/' . env('PUBLIC_HTML_FOLDER') . '/storage/promosi',
        //     'url' => env('APP_URL') .  '/' . env('PUBLIC_HTML_FOLDER') . '/storage/promosi',
        //     'visibility' => 'public',
        // ],

        // comment untuk hosting 000Webhost tanpa symlink

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'foto_profil_guru' => [
            'driver' => 'local',
            'root' => storage_path('app/public/foto_profil/guru'),
            'url' => env('APP_URL') . '/storage/foto_profil/guru',
            'visibility' => 'public',
        ],

        'foto_profil_siswa' => [
            'driver' => 'local',
            'root' => storage_path('app/public/foto_profil/siswa'),
            'url' => env('APP_URL') . '/storage/foto_profil/siswa',
            'visibility' => 'public',
        ],

        'karya_citra' => [
            'driver' => 'local',
            'root' => storage_path('app/public/karya_citra/gambar/'),
            'url' => env('APP_URL') . '/storage/karya_citra/gambar/',
            'visibility' => 'public',
        ],

        'karya_citra_video' => [
            'driver' => 'local',
            'root' => storage_path('app/public/karya_citra/video/'),
            'url' => env('APP_URL') . '/storage/karya_citra/video/',
            'visibility' => 'public',
        ],

        'promosi' => [
            'driver' => 'local',
            'root' => storage_path('app/public/promosi'),
            'url' => env('APP_URL') . '/storage/promosi',
            'visibility' => 'public',
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
