<?php
use think\facade\Env;
return [
    // 默认磁盘
    'default' => env('FILESYSTEM.DRIVER', 'local'),

    //阿里云

    // 磁盘列表
    'disks'   => [
        'aliyun' => [
            'type'         => Env::get('FILESYSTEM.TYPE'),
            'accessId'     => Env::get('FILESYSTEM.ACCESSID'),
            'accessSecret' => Env::get('FILESYSTEM.ACCESSSECRET'),
            'bucket'       => Env::get('FILESYSTEM.BUCKET'),
            'endpoint'     => Env::get('FILESYSTEM.ENDPOINT'),
            'url'          => Env::get('FILESYSTEM.URL'),//不要斜杠结尾，此处为URL地址域名。
        ],

        'local'  => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'public' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRootPath() . 'public/storage',
            // 磁盘路径对应的外部URL路径
            'url'        => '/storage',
            // 可见性
            'visibility' => 'public',
        ],
        'cdphoto' =>[
            'type'=>'local',
            'root'=>app()->getRootPath() . 'public/uploads',
            'url'        => '/uploads',
            'visibility' => 'public',
        ],

        // 更多的磁盘配置信息
    ],
];
