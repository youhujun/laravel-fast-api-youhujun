<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-09 19:43:46
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-14 18:25:48
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserItemFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Services\Facade\Common\V1\Es\EsQueryFacadeService;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\SelectItem\GetDefaultUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\SelectItem\FindUserDTO;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\EsUserCollection;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserItemController
 * @see \App\Services\Facade\Admin\User\User\AdminUserItemFacadeService
 */
class AdminUserItemFacadeService
{
    public function test()
    {
        echo "AdminUserItemFacadeService test";
    }

    protected static $sortMapArray = [
        '1' => ['created_time','asc'],
        '2' => ['created_time','desc'],
    ];

    protected static $searchItem = [
        'phone',
        'account_name',
        'nick_name',
        'real_name',
        'id_number'
    ];

    /**
      * 获取默认的用户选项
      *
      * @return void
      */
    public function getDefaultUser(GetDefaultUserDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetDefaultUserError'));

        $validated = $requestDTO->toArray();

        $perPage = 10;
        $currentPage = 1;

        $indexName = config('common_es.indices.user.users');

        $esQuery = (new EsQueryFacadeService())->index($indexName);

        $esQuery->whereNull('deleted_at');

        //如果要求实名认证状态
        if (isset($validated['real_auth_status']) && $validated['real_auth_status']) {
            $esQuery->where('real_auth_status', $validated['real_auth_status']);
        }

        $esQuery->orderBy(self::$sortMapArray[2][0], self::$sortMapArray[2][1]);

        $userCollection =  $esQuery->page($currentPage, $perPage)->get();

        $result = new EsUserCollection($userCollection, ['code' => 0,'msg' => '获取默认用户选项成功!']);

        return $result;
    }

    /**
     * 查找用户
     *
     * @param [type] $find
     * @return void
     */
    public function findUser(FindUserDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.FindUserError'));

        $validated = $requestDTO->toArray();

        $perPage = 10;
        $currentPage = 1;

        $indexName = config('common_es.indices.user.users');

        $esQuery = (new EsQueryFacadeService())->index($indexName);

        $esQuery->whereNull('deleted_at');

        if (isset($validated['real_auth_status']) && $validated['real_auth_status']) {
            $esQuery->orWhere('real_auth_status', $validated['real_auth_status']);
        }

        if (isset($validated['find'])) {
            //判断是否是手机号
            $numberRegex = '/^[0-9]+$/';

            $numberResult = \preg_match($numberRegex, $validated['find']);

            //如果是手机号
            if ($numberResult) {
                $esQuery->whereLike('phone', $validated['find']);
            } else {
                $esQuery->allWhereLike(['nick_name', 'real_name'], $validated['find']);
                //$esQuery->orWhereLike('nick_name', $validated['find'])->orWhereLike('real_name', $validated['find']);
            }
        }

        $esQuery->orderBy(self::$sortMapArray[2][0], self::$sortMapArray[2][1]);

        $userCollection =  $esQuery->page($currentPage, $perPage)->get();

        $result = new EsUserCollection($userCollection, ['code' => 0,'msg' => '查找用户选项成功!']);

        return $result;
    }
}
