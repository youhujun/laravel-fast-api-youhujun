<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-09 19:49:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-17 05:25:57
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserTeamFacadeService.php
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
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Team\GetUserSourceDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Team\GetUserLowerTeamDTO;
//用户
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Union\UserSourceUnion;
//响应资源
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\EsUserResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\EsUserCollection;

/**
 * @see \App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserTeamFacade
 */
class AdminUserTeamFacadeService
{
    public function test()
    {
        echo "AdminUserTeamFacadeService test";
    }

    protected static $sort = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];


    /**
     * 获取用户的上级用户(推荐用户)
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function getUserSource(GetUserSourceDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetUserRecommendError'));

        $validated = $requestDTO->toArray();

        $source_user_uid =  $requestDTO->source_user_uid;

        if($source_user_uid){
             $indexName = config('common_es.indices.user.users');

            $esSourceUserObject = EsQueryFacade::index($indexName)->where('user_uid', $source_user_uid)->get()->first();

            //直接熔断降级
            if (!isset($esSourceUserObject->user_uid)) {
                throw new CommonException('ServiceBusyError');
            }
        }

        $data['data'] = null;

        if(isset($esSourceUserObject->user_uid)){
            $data['data'] =  new EsUserResource($esSourceUserObject);
        }

        $result = code(['code' => 0,'msg' => '获取推荐用户成功!'], $data);

        return $result;
    }

    /**
    * 获取用户下级团队
    */
    public function getUserLowerTeam(GetUserLowerTeamDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetUserLowerTeamError'));

        $validated = $requestDTO->toArray();

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $lower_type = $requestDTO->lower_type;
        $user_uid = $requestDTO->user_uid;

        $userSourceUnionIndexName = config('common_es.indices.union.user_source_unions');

        // 1. 初始化ES查询构造器
        $esUserSourceUnionQuery = EsQueryFacade::index($userSourceUnionIndexName);

        // 默认全局查询未删除用户
        $esUserSourceUnionQuery->whereNull('deleted_at');

        // 根据下级类型构建查询条件
        //查看全部
        if ($lower_type == 0) {
            $esUserSourceUnionQuery->orWhere('first_uid', $user_uid)->orWhere('second_uid', $user_uid);
        }

        //查看一级团队
        if ($lower_type == 10) {
            $esUserSourceUnionQuery->where('first_uid', $user_uid);
        }

        //查看二级团队
        if ($lower_type == 20) {
            $esUserSourceUnionQuery->where('second_uid', $user_uid);
        }

        $esUserSourceUnionColelction = $esUserSourceUnionQuery->get();

       

        if (!$esUserSourceUnionColelction->count()) {
            $result = code(['code' => 0,'msg' => '获取推用户团队成功!','data'=>[]]);
        } else {
            $user_uid_array = [];

            $user_uid_array = $esUserSourceUnionColelction->map(function ($esUserSourceUnionObject) {
                return $esUserSourceUnionObject->user_uid;
            })->toArray();

            $userIndexName = config('common_es.indices.user.users');

            $esUserQuery = EsQueryFacade::index($userIndexName);

            $esUserQuery->whereNull('deleted_at');

            $esUserQuery->whereIn('user_uid', $user_uid_array);

            $userPaginator = $esUserQuery->paginate($perPage, $currentPage);

            $result = new EsUserCollection($userPaginator, ['code' => 0,'msg' => '获取推用户团队成功!'], null, null);
        }

        return  $result;
    }
}
