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

    Route::get('admin/cooperation', 'Cooperation/index'); //查看合作申请
    Route::post('cooperation/add', 'Cooperation/create'); //申请合作
    Route::get('cooperation/read', 'Cooperation/read'); //查看合作详情
    Route::delete('cooperation/del', 'Cooperation/delete'); //终止合作
    Route::put('cooperation/edit', 'Cooperation/edit'); //合作审批
    Route::get('admin/contract', 'Contract/index'); //查看合同
    Route::post('contract/add', 'Contract/create'); //添加合同
    Route::post('contract/upload', 'Contract/upload'); //合同上传
    Route::delete('contract/del', 'Contract/delete'); //合同删除


})->ext();
Route::group('apis', function () {

    Route::get('admin/menu', 'Menu/index'); //菜单查询
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
    Route::get('admin/messages', 'Messages/index'); //查看消息
    Route::post('messages/add', 'Messages/create'); //添加消息
    Route::get('messages/read', 'Messages/read'); //阅读消息
    Route::delete('messages/del', 'Messages/delete'); //角色删除
    

})->middleware(['priority', 'verification']);
