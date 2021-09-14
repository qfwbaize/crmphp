<?php
use think\facade\Env;
return [
    //短信
    'config'  => [
        'type'         => Env::get('FILESYSTEM.TYPE'),
        'accessId'     => Env::get('FILESYSTEM.ACCESSID'),
        'accessSecret' => Env::get('FILESYSTEM.ACCESSSECRET'),
        'bucket'       => Env::get('FILESYSTEM.BUCKET'),
        'endpoint'     => Env::get('FILESYSTEM.ENDPOINT'),
        'url'          => Env::get('FILESYSTEM.URL'),//不要斜杠结尾，此处为URL地址域名。

    ],
];