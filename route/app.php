<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;
use think\middleware;
use think\middleware\Throttle;
/*
     * verification 加密验证
     * priority 登陆验证 token验证
     * auths 路由权限验证
     */

Route::group('apis', function () {
        Route::get('menu/menu', 'Menu/menu'); //根据角色返回菜单
        Route::get('admin/flow', 'Flow/index'); //流量统计查询
        Route::get('admin/income', 'Income/index'); //数据统计查询
        Route::get('faq/msgread', 'Faq/msg_read'); //设置已读
        Route::get('faq/im', 'Faq/ImId'); //查询
        Route::post('admin/login', 'Login/index'); //登陆
        Route::get('admin/userlogin', 'Login/UserLogin'); //登陆
        Route::post('admin/code', 'Login/code'); //获取验证码
})->middleware('verification');
Route::group('apis', function () {
        Route::get('admin/sale', 'Sale/index'); //销售统计查询
        Route::get('flow/export', 'Flow/export');
        Route::get('sale/export', 'Sale/export');
        Route::post('admin/loginout', 'Login/LoginOut');
})->middleware(['verification', 'priority']);

//问题发布接口
Route::group('apis', function () {

        Route::get('admin/menu', 'Menu/index'); //菜单查询
        Route::post('menu/add', 'Menu/create'); //菜单添加
        Route::get('admin/reply', 'Imreply/index'); //快捷回复查询
        Route::post('reply/add', 'Imreply/create'); //快捷回复增加
        Route::post('reply/del', 'Imreply/delete'); //快捷回复删除
        Route::get('menu/read', 'Menu/read'); //菜单id查询
        Route::post('menu/edit', 'Menu/edit'); //菜单修改
        Route::post('menu/del', 'Menu/delete'); //菜单删除
        Route::get('auth/authorizeid', 'Auth/authorizeid'); //授权角色查询
        Route::get('auth/authorize', 'Auth/authorize'); //授权查询
        Route::post('auth/saveAuthorize', 'Auth/saveAuthorize'); //授权
        Route::get('admin/auth', 'Auth/index'); //角色查询
        Route::post('auth/add', 'Auth/create'); //角色添加
        Route::get('auth/read', 'Auth/read'); //角色id查询
        Route::post('auth/edit', 'Auth/edit'); //角色修改
        Route::post('auth/del', 'Auth/delete'); //角色删除
        Route::get('adminuser/adminauth', 'AdminUser/adminauth'); //管理员角色查询
        Route::get('admin/adminuser', 'AdminUser/index'); //管理员查询
        Route::post('adminuser/add', 'AdminUser/create'); //管理员添加
        Route::get('adminuser/read', 'AdminUser/read'); //管理员id查询
        Route::post('adminuser/edit', 'AdminUser/edit'); //管理员修改
        Route::post('adminuser/del', 'AdminUser/delete'); //管理员删除

})->middleware(['priority', 'verification', 'auths', 'log']);
