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
     * company 企业认证验权
     */
//测试接口专用
Route::group('apis', function () {

    })->ext();
//文件上传导出
Route::group('apis', function () {

    Route::post('uploads/mechanism_evidence_upload', 'Uploads/mechanism_evidence_upload'); //机构证据上传
    Route::post('uploads/mechanism_contract_upload', 'Uploads/mechanism_contract_upload'); //机构合同上传
    Route::post('uploads/authentication', 'Uploads/authentication'); //机构合同上传

    Route::get('pdf/evidence', 'Pdf/evidence'); //导出pdf
})->ext();
//企业任务管理
Route::group('apis', function () {
    Route::post('companytask/add', 'CompanyTask/create'); //发布任务
    Route::get('companytask/release_index', 'CompanyTask/release_index'); //查看发出任务
    Route::get('companytask/accept_index', 'CompanyTask/accept_index'); //查看接受任务
    Route::get('companytask/read', 'CompanyTask/read'); //查看任务详情
    Route::get('companytask/company', 'CompanyTask/company'); //查看合作得机构
    Route::put('companytask/task_update', 'CompanyTask/task_update'); //修改任务状态
    Route::get('companytask/task_people', 'CompanyTask/task_people'); //查看正在工作得员工
    Route::get('companytask/task_people_evidence', 'CompanyTask/task_people_evidence'); //查看员工得证据
    Route::put('companytask/task_people_edit', 'CompanyTask/task_people_edit'); //对员工工作进行审批
    Route::delete('companytask/del', 'CompanyTask/delete'); //机构拒绝任务
    Route::post('companytask/reward', 'CompanyTask/reward'); //对员工进行奖励
    Route::get('companytask/company_reward', 'CompanyTask/company_reward'); //查看所有凭证
    Route::get('companytask/read_company_reward', 'CompanyTask/read_company_reward'); //查看对个人得打款凭证
    Route::get('companytask/read_attor_company_reward', 'CompanyTask/read_attor_company_reward'); //查看对机构得打款凭证

})->middleware(['priority', 'verification','company']);
Route::group('apis', function () {
    //菜单
    Route::get('admin/menu', 'Menu/index'); //菜单查询
    Route::post('menu/add', 'Menu/create'); //菜单添加
    Route::get('menu/read', 'Menu/read'); //菜单id查询
    Route::put('menu/edit', 'Menu/edit'); //菜单修改
    Route::delete('menu/del', 'Menu/delete'); //菜单删除
    //角色验权
    Route::get('auth/authorizeid', 'Auth/authorizeid'); //授权角色查询
    Route::get('auth/authorize', 'Auth/authorize'); //授权查询
    Route::put('auth/saveAuthorize', 'Auth/saveAuthorize'); //授权
    Route::get('admin/auth', 'Auth/index'); //角色查询
    Route::post('auth/add', 'Auth/create'); //角色添加
    Route::get('auth/read', 'Auth/read'); //角色id查询
    Route::put('auth/edit', 'Auth/edit'); //角色修改
    Route::delete('auth/del', 'Auth/delete'); //角色删除
    //管理员管理
    Route::get('adminuser/adminauth', 'AdminUser/adminauth'); //管理员角色查询
    Route::get('admin/adminuser', 'AdminUser/index'); //管理员查询
    Route::post('adminuser/add', 'AdminUser/create'); //管理员添加
    Route::get('adminuser/read', 'AdminUser/read'); //管理员id查询
    Route::put('adminuser/edit', 'AdminUser/edit'); //员工转移
    Route::post('adminuser/del', 'AdminUser/delete'); //管理员删除
    //消息管理
    Route::get('admin/messages', 'Messages/index'); //查看消息
    Route::post('messages/add', 'Messages/create'); //添加消息
    Route::get('messages/read', 'Messages/read'); //阅读消息
    Route::delete('messages/del', 'Messages/delete'); //角色删除
    //企业备案
    Route::get('authentication/read', 'Authentication/read'); //查看是否已经备案
    Route::post('authentication/add', 'Authentication/create'); //添加备案信息
    //合作申请
    Route::get('admin/cooperation', 'Cooperation/index'); //查看合作申请
    Route::post('cooperation/add', 'Cooperation/create'); //申请合作
    Route::get('cooperation/read', 'Cooperation/read'); //查看合作详情
    Route::get('cooperation/company', 'Cooperation/company'); //查看合作
    Route::delete('cooperation/del', 'Cooperation/delete'); //终止合作
    Route::put('cooperation/edit', 'Cooperation/edit'); //合作审批
    //合同
    Route::get('admin/contract', 'Contract/index'); //查看合同
    Route::post('contract/add', 'Contract/create'); //添加合同
    Route::post('contract/upload', 'Contract/upload'); //合同上传
    Route::delete('contract/del', 'Contract/delete'); //合同删除

})->middleware(['priority', 'verification']);
