<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-12 14:58:02
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-01 15:16:24
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Support\Facades\Facade;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\GetUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\AddUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\DisableUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\MultipleDisableUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\DeleteUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\MultipleDeleteUserDTO;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserFacadeService
 */
class AdminUserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminUserFacade";
    }

    public static function getUser(Admin $adminObject, GetUserDTO $getUserDTO)
    {
        return static::getFacadeRoot()->getUser($adminObject, $getUserDTO);
    }

    public static function getShardUser(Admin $adminObject, GetUserDTO $getUserDTO)
    {
        return static::getFacadeRoot()->getShardUser($adminObject, $getUserDTO);
    }

    public static function getEsUser(Admin $adminObject, GetUserDTO $getUserDTO)
    {
        return static::getFacadeRoot()->getEsUser($adminObject, $getUserDTO);
    }

    public static function addUser(Admin $adminObject, AddUserDTO $addUserDTO)
    {
        return static::getFacadeRoot()->addUser($adminObject, $addUserDTO);
    }

    public static function disableUser(Admin $adminObject, DisableUserDTO $disableUserDTO)
    {
        return static::getFacadeRoot()->disableUser($adminObject, $disableUserDTO);
    }

    public static function multipleDisableUser(Admin $adminObject, MultipleDisableUserDTO $multipleDisableUserDTO)
    {
        return static::getFacadeRoot()->multipleDisableUser($adminObject, $multipleDisableUserDTO);
    }

    public static function deleteUser(Admin $adminObject, DeleteUserDTO $deleteUserDTO)
    {
        return static::getFacadeRoot()->deleteUser($adminObject, $deleteUserDTO);
    }

    public static function multipleDeleteUser(Admin $adminObject, MultipleDeleteUserDTO $multipleDeleteUserDTO)
    {
        return static::getFacadeRoot()->multipleDeleteUser($adminObject, $multipleDeleteUserDTO);
    }
}
