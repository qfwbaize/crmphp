<?php
return [
    //短信
    'config'  => [
        'account' => env('CHANGLAN.CL_ACCOUNT',''),
        'password' => env('CHANGLAN.CL_PASSWORD',''),
        'send_url' => env('CHANGLAN.CL_SEND_URL',''),
        'balnce_query_url'=> env('CHANGLAN.CL_BALANCE_QUERY_URL',''),
        'variable_url'=>env(''),
    ],
    // 不需要验证登录的控制器
    'no_login_controller' => [
        'login',
    ],

    // 不需要验证登录的节点
    'no_login_node'       => [
        'login/index',
        'login/out',
    ],

    // 不需要验证权限的控制器
    'no_auth_controller'  => [
        'ajax',
        'login',
        'index',
    ],

    // 不需要验证权限的节点
    'no_auth_node'        => [
        'login/index',
        'login/out',
    ],
];