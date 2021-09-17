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
     */
//问题发布接口
Route::group('apis', function () {
    Route::get('admin/menu', 'Menu/index'); //菜单查询

})->ext();
Route::group('apis', function () {


        Route::post('menu/add', 'Menu/create'); //菜单添加
        Route::get('menu/read', 'Menu/read'); //菜单id查询
        Route::put('menu/edit', 'Menu/edit'); //菜单修改
        Route::delete('menu/del', 'Menu/delete'); //菜单删除
        Route::get('auth/authorizeid', 'Auth/authorizeid'); //授权角色查询
        Route::get('auth/authorize', 'Auth/authorize'); //授权查询
        Route::put('auth/saveAuthorize', 'Auth/saveAuthorize'); //授权
        Route::get('admin/auth', 'Auth/index'); //角色查询
        Route::post('auth/add', 'Auth/create'); //角色添加
        Route::get('auth/read', 'Auth/read'); //角色id查询
        Route::put('auth/edit', 'Auth/edit'); //角色修改
        Route::delete('auth/del', 'Auth/delete'); //角色删除
        Route::get('adminuser/adminauth', 'AdminUser/adminauth'); //管理员角色查询
        Route::get('admin/adminuser', 'AdminUser/index'); //管理员查询
        Route::post('adminuser/add', 'AdminUser/create'); //管理员添加
        Route::get('adminuser/read', 'AdminUser/read'); //管理员id查询
        Route::put('adminuser/edit', 'AdminUser/edit'); //员工转移
        Route::post('adminuser/del', 'AdminUser/delete'); //管理员删除

})->middleware(['priority', 'verification'])->allowCrossDomain([
    /** 设置跨域允许的header头信息，新增token字段 */
    'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With, authKey, Accept, Origin, token',
    /** 允许所有请求 */
    'Access-Control-Allow-Origin'      => '*',
    'Access-Control-Allow-Credentials' => 'true',
]);
