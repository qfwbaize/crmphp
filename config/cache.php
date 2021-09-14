<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------
use think\facade\Env;
return [
    // 默认缓存驱动
    'default' => env('CACHE.DRIVER', 'file'),

    // 缓存连接方式配置
    'stores'  => [
        'file' => [
            // 驱动方式
            'type'       => 'File',
            // 缓存保存目录
            'path'       => '',
            // 缓存前缀
            'prefix'     => '',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
        ],
        // 配置Reids
        'redis'    =>    [
            'type'     => Env::get('CACHE.TYPE' ),
            'host'     => Env::get('CACHE.HOST'),
            'port'     => Env::get('CACHE.PORT'),
            'password' => Env::get('CACHE.PASSWORD'),
            'select'   => Env::get('CACHE.SELECT'),
            // 全局缓存有效期（0为永久有效）
            'expire'   => Env::get('CACHE.EXPIRE'),
            // 缓存前缀
            'prefix'   => Env::get('CACHE.PREFIX'),
            'timeout'  => Env::get('CACHE.TIMEOUT'),
        ],
    ],
];
