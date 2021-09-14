<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 应用地址
    'app_host'         => env('APP.HOST', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 默认应用
    'default_app'      => 'index',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'          => [],
    // 后台别名
    //'admin_alias_name' => env('easyadmin.admin', 'api'),
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => [],

    // 异常页面的模板文件
    'exception_tmpl'   => app()->isDebug()==true ? app()->getThinkPath() . 'tpl/think_exception.tpl':base_path().'sorry.html',
    'http_exception_template'=> [404 =>base_path().'404.html'],


    // 错误显示信息,非调试模式有效
    'error_message'    => false,
    // 显示错误信息
    'show_error_msg'   => false,
];
