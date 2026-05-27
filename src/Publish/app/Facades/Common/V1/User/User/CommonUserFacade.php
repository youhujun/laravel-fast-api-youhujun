<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 15:44:57
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 17:34:01
 * @FilePath: \youhu-laravel-api-12\app\Facades\Common\V1\User\User\CommonUserFacade.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Facades\Common\V1\User\User;

use Illuminate\Support\Facades\Facade;
use App\Models\LaravelFastApi\V1\User\User;
use App\DTOs\Common\V1\User\User\BusinessRegisterUserDTO;
/**
 * @see \App\Services\Facade\Common\V1\User\User\CommonUserFacadeService
 */
class CommonUserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "CommonUserFacade";
    }

    /**
     * 注册用户
     *
     * @param  array     $paramArray
     * @param  User|null $userObject
     */
    public static function registerUser(BusinessRegisterUserDTO $businessDTO, ?User $userObject = null): User
    {
        return static::getFacadeRoot()->registerUser($businessDTO, $userObject);
    }

    /**
     * 获取用户头像
     *
     * @param  User   $userObject
     * @return string
     */
    public static function getUserAvatar(User $userObject): string
    {
        return static::getFacadeRoot()->getUserAvatar($userObject);
    }

    /**
     * 获取用户OpenID
     *
     * @param  User    $userObject
     * @param  integer $openid_type
     * @return string
     */
    public static function getUserOpenid(User $userObject, int $openid_type = 10): mixed
    {
        return static::getFacadeRoot()->getUserOpenid($userObject, $openid_type);
    }

    /**
     * 获取用户角色数组
     *
     * @param  User  $userObject
     * @return array
     */
    public static function getUserRoleIdArray(User $userObject): array
    {
        return static::getFacadeRoot()->getUserRoleIdArray($userObject);
    }
}
