<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 09:38:04
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-17 09:42:23
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Login\AdminLoginFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Login;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Exceptions\Admin\CommonException;
use App\DTOs\LaravelFastApi\V1\Admin\Login\AdminLoginDTO;
use App\Events\LaravelFastApi\V1\Admin\Login\AdminLoginEvent;
use App\Events\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent;
use App\Jobs\LaravelFastApi\V1\Admin\Login\AdminLogoutJob;
use App\Jobs\LaravelFastApi\V1\Admin\Login\EsAdminLoginJob;
use App\Jobs\LaravelFastApi\V1\Admin\Logout\EsAdminLogoutJob;
use App\Facades\LaravelFastApi\V1\Admin\Login\Common\AdminBackgroundLoginFacade;
use App\Models\LaravelFastApi\V1\Admin\Admin;

/**
 * @see \App\Facades\LaravelFastApi\V1\Admin\Login\AdminLoginFacade
 */
class AdminLoginFacadeService
{
    public function test()
    {
        echo "AdminLoginFacadeService test";
    }

    /**
     * 后台管理员认证登录 账户|邮箱|手机 登录
     *
     * @param array $validated 验证通过传递过来的参数
     * @return array 返回值
     */
    public function authLogin(AdminLoginDTO $loginDTO): array
    {
        $result = code(config('admin_code.AdminLoginError'));

        //p($validated);die;

        //设置默认守卫是admin
        Auth::setDefaultDriver('admin');

        $remember = true; //生成 remmber_token
        /**
         * 注意数据库一旦生成了remember_token,再次登录是不变的,除主动清除或者更新,否则一直存在且不变
         *  //用户登录日志记录
         */
        $ip = Request::getClientIp();

        $verifyAccountNameResult = 0;
        $verifyEmailResult = 0;
        $verifyPhoneResult = 0;
        //验证账号
        $dataAccountNameArray['account_name'] = $loginDTO->username;
        $dataAccountNameArray['password'] = $loginDTO->password;
        $dataAccountNameArray['account_status'] = 1;

        $verifyAccountNameResult = Auth::attempt($dataAccountNameArray, $remember);

        //p($verifyAccountNameResult);die;

        //账号验证失败
        if (!$verifyAccountNameResult) {
            //验证邮箱
            $dataEmailArray['email'] = $loginDTO->username;

            $dataEmailArray['password'] = $loginDTO->password;

            $dataEmailArray['account_status'] = 1;

            $verifyEmailResult = Auth::attempt($dataEmailArray, $remember);

            //邮箱验证失败
            if (!$verifyEmailResult) {
                //验证手机号
                $dataPhoneArray['phone'] = $loginDTO->username;

                $dataPhoneArray['password'] = $loginDTO->password;

                $dataPhoneArray['account_status'] = 1;
                $verifyPhoneResult = Auth::attempt($dataPhoneArray, $remember);
            }
        }

        if (!$verifyAccountNameResult && !$verifyEmailResult && !$verifyPhoneResult) {
            throw new CommonException('AdminLoginError');
        }

        $adminObject = Auth::user();
        //p($adminObject);die;

        //无论如何 在redis缓存中执行一边重新再的登录
        AdminBackgroundLoginFacade::checkResetLogin($adminObject);

        $dataArray['data'] = [];

        if (empty($adminObject) || !isset($adminObject->remember_token)) {
            throw new CommonException('GetLoginAdminError');
        }

        $dataArray['data']['token'] = $adminObject->remember_token;

        //adminLogin::dispatch($adminObject, $ip);
        AdminLoginEvent::dispatch($adminObject, $loginDTO);

        //登录成功以后 12个小时以后数据库自动退出
        //如果是开发模式就不执行自动退出
        $developMode = config('youhujun.develop_mode');

        if (!$developMode) {
            AdminLogoutJob::dispatchIf($adminObject->remember_token, $adminObject)->delay(now()->addSeconds(3600 * 12));
        }


        EsAdminLoginJob::dispatchIf($adminObject->remember_token, $adminObject)->delay(now()->addSeconds(3));

        $result = code(['code' => 0,'msg' => '登录成功!'], $dataArray);

        return $result;
    }

    /**
     * 用户退出登录
     *
     * @return void
    */
    public function logout(Admin $adminObject): array
    {
        $result = [];

        $token = $adminObject->remember_token;

        $adminObject->remember_token = null;

        //UserUpdateToken
        $logoutResult = $adminObject->save();

        if (!$logoutResult) {
            throw new CommonException('AdminLogoutError');
        }

        AdminLogoutEvent::dispatch($adminObject, $token);

        EsAdminLogoutJob::dispatch($adminObject)->delay(now()->addSeconds(3));


        $result = code(['code' => 0,'msg' => '管理员退出成功!']);

        return $result;
    }

    /**
     * 获取管理员信息
     * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Login\LoginAfterController
     * @param   $adminObject
     */
    public function getAdminInfo($adminObject): array
    {
        $adminInfoDataArray = [
            'user_uid' => $adminObject->user_uid,
            'admin_uid' => $adminObject->admin_uid,
            'username' => $adminObject->account_name,
            'nickname' => '',
            'introduction' => '',
            'roles' => get_admin_roles($adminObject),
            'avatar' => '',
            'phone' => $adminObject->phone ? $adminObject->phone : '',
            'nickname' => '',
            'introduction' => '',
            'avatar' => '',
            'album_uid' => '',
            'perms' => []
        ];

        $user_uid = $adminObject->user_uid;

        $userIndexName = config('common_es.indices.user.users');

        $esUserQuery = EsQueryFacade::index($userIndexName);

        $esUserQuery->whereNull('deleted_at');

        $userObject = $esUserQuery->where('user_uid', $user_uid)->get()->first();

        if ($userObject) {
            $adminInfoDataArray['nickname'] = $userObject->nick_name;
            $adminInfoDataArray['introduction'] = $userObject->introduction;
            $adminInfoDataArray['avatar'] = $userObject->avatar;
        }

        //处理相册uid
        $adminInfoDataArray['album_uid'] = '';

        $admin_album_uid = get_admin_album_uid($adminObject->admin_uid);

        if ($admin_album_uid) {
            $adminInfoDataArray['album_uid'] = $admin_album_uid;
        }

        $result = code(['code' => 0,'msg' => '获取管理员信息成功'], ['data' => $adminInfoDataArray]);

        return $result;
    }
}
