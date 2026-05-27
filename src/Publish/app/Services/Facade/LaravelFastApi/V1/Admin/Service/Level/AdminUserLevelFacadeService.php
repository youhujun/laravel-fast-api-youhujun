<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-10 03:18:58
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Service\Level\AdminUserLevelFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Service\Level;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Services\Facade\Traits\V1\Es\EsAlwaysService;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\DefaultUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\FindUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\GetUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\AddUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\UpdateUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\DeleteUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\MultipleDeleteUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\AddUserLevelItemUnionDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\UpdateUserLevelItemUnionDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\DeleteUserLevelItemUnionDTO;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Level\UserLevel;
use App\Models\LaravelFastApi\V1\System\Level\Union\UserLevelItemUnion;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Service\Level\UserLevel\EsUserLevelCollection;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Service\Level\UserLevel\EsSelectUserLevelCollection;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Level\UserLevelController
 * @see \App\Facades\LaravelFastApi\V1\Admin\Service\Level\AdminUserLevelFacade
 */
class AdminUserLevelFacadeService
{
    public function test()
    {
        echo "AdminUserLevelFacadeService test";
    }

    protected static $sortMapArray = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];

    protected static $searchItemMapArray = [
        'level_name',
        'level_code'
    ];

    protected static $storage_public_path = DIRECTORY_SEPARATOR.'app'. DIRECTORY_SEPARATOR.'public';

    /**
     * 获取常用
     *
     * @param [type] $userObject
     * @return void
     */
    public function defaultUserLevel(DefaultUserLevelDTO $requestDTO)
    {
        $result = code(config('admin_code.DefaultUserLevelError'));

        $validated = $requestDTO->toArray();

        $indexName = config('common_es.indices.business.user_levels');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);
        $esQuery->whereNull('deleted_at');

        $esQuery->orderBy('id', 'asc');

        $max_size = config('common_es.max_result_window');

        $userLevelCollection = $esQuery->limit($max_size)->get();

        if (!optional($userLevelCollection)) {
            throw new CommonException('DefaultUserLevelError');
        }

        $result = new EsSelectUserLevelCollection($userLevelCollection, ['code' => 0,'msg' => '获取默认用户级别成功!']);

        return  $result;
    }

    /**
     * 搜索查找选项
     *
     * @param [type] $find
     * @return void
     */
    public function findUserLevel(FindUserLevelDTO $requestDTO)
    {
        $result = code(config('admin_code.FindUserLevelError'));

        $validated = $requestDTO->toArray();

        $indexName = config('common_es.indices.business.user_levels');

        $esQuery = EsQueryFacade::index($indexName);
        $esQuery->whereNull('deleted_at');
        if (!empty($validated['find'])) {
            $esQuery->whereLike('level_name', $validated['find']);
        }
        $esQuery->orderBy('id', 'asc');

        $userLevelCollection = $esQuery->limit(10)->get();

        if (!optional($userLevelCollection)) {
            throw new CommonException('FindUserLevelError');
        }

        $result = new EsSelectUserLevelCollection($userLevelCollection, ['code' => 0,'msg' => '查找用户级别成功!']);

        return  $result;
    }

    /**
     * 查询
     *
     * @param [type] $validated
     * @return void
     */
    public function getUserLevel(GetUserLevelDTO $requestDTO)
    {
        $result = code(config('admin_code.GetUserLevelError'));

        $validated = $requestDTO->toArray();

        $perPage = $validated['pageSize'];
        $currentPage = $validated['currentPage'];

        $indexName = config('common_es.indices.business.user_levels');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

       // 模糊搜索（手机号/姓名等）
        if (isset($requestDTO->findSelectIndex) && isset($requestDTO->find) && !empty($requestDTO->find)) {
            $findIndex = $requestDTO->findSelectIndex;
            $findValue = $requestDTO->find;
            $searchField = self::$searchItemMapArray[$findIndex] ?? '';
            if ($searchField) {
                $esQuery->whereLike($searchField, $findValue);
            }
        }

        // 时间范围
        if (isset($requestDTO->timeRange) && \count($requestDTO->timeRange)) {
            $startTime = strtotime($requestDTO->timeRange[0]);
            $endTime = strtotime($requestDTO->timeRange[1]);
            $esQuery->whereBetween('created_time', [$startTime, $endTime]);
        }

        $esQuery->orderBy('id','desc');

        //排序
        if (isset($requestDTO->sortType)) {
            $sortType = $requestDTO->sortType;

            $esQuery->orderBy(self::$sortMapArray[$sortType][0], self::$sortMapArray[$sortType][1]);
        }

        // 导出逻辑（完全按你的真实业务）

        $download = null;


        $userLevelPaginator = $esQuery->page($currentPage, $perPage)->paginate();

        if (\optional($userLevelPaginator)) {
            $result = new EsUserLevelCollection($userLevelPaginator, ['code' => 0,'msg' => '获取用户级别成功!']);
        }

        return  $result;
    }

    /**
     * 添加
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function addUserLevel(AddUserLevelDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddUserLevelError'));

        $validated = $requestDTO->toArray();

        $userLevelObject = new UserLevel();

        foreach ($validated as $key => $value) {
            if (\is_null($value)) {
                $value = "";
            }

            $userLevelObject->$key = $value;
        }

        $userLevelObject->created_time = time();
        $userLevelObject->created_at = time();

        $userLevelResult = $userLevelObject->save();

        if (!$userLevelResult) {
            throw new CommonException('AddUserLevelError');
        }

        $indexName = config('common_es.indices.business.user_levels');

        $insertDataArray = [
            '_docId' => $userLevelObject->id,
            'id' => $userLevelObject->id,
            'level_name' => $userLevelObject->level_name,
            'level_code' => $userLevelObject->level_code,
            'amount' => $userLevelObject->amount,
            'background_picture_uid' => $userLevelObject->background_picture_uid,
            'note' => $userLevelObject->note,
            'sort' => $userLevelObject->sort,
            'created_time' => $userLevelObject->created_time,
            'updated_time' => $userLevelObject->updated_time,
            'created_at' => $userLevelObject->created_at,
            'updated_at' => $userLevelObject->updated_at,
            'deleted_at' => $userLevelObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $userLevelObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加用户级别失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$userLevelObject' => $userLevelObject,'$adminObject' => $adminObject], 'EsAddUserLevelJob', 'handleError');
            throw new CommonException('EsAddUserLevelError');
        }

        CommonEvent::dispatch($adminObject, $userLevelObject, 'AddUserLevel');

        $result = code(['code' => 0,'msg' => '添加用户级别成功!']);

        return $result;
    }


    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updateUserLevel(UpdateUserLevelDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateUserLevelError'));

        $validated = $requestDTO->toArray();

        $userLevelObject = UserLevel::find($validated['id']);

        if (!$userLevelObject) {
            throw new CommonException('ThisDataHasChildrenError');
        }

        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$userLevelObject ->revision];

        foreach ($validated as $key => $value) {
            if ($key === 'id') {
                $where[] = ['id','=',$value];
                continue;
            }

            if (\is_null($value)) {
                $value = "";
            }

            $updateDataArray[$key] = $value;
        }

        $updateDataArray['revision'] = $userLevelObject ->revision + 1;

        $updateDataArray['updated_time'] = time();

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        $userLevelResult = UserLevel::where($where)->update($updateDataArray);

        if (!$userLevelResult) {
            throw new CommonException('UpdateUserLevelError');
        }

        $userLevelObject = $userLevelObject->fresh();

        $indexName = config('common_es.indices.business.user_levels');

        $updateDataArray = [
            '_docId' => $userLevelObject->id,
            'id' => $userLevelObject->id,
            'level_name' => $userLevelObject->level_name,
            'level_code' => $userLevelObject->level_code,
            'amount' => $userLevelObject->amount,
            'background_picture_uid' => $userLevelObject->background_picture_uid,
            'note' => $userLevelObject->note,
            'sort' => $userLevelObject->sort,
            'created_time' => $userLevelObject->created_time,
            'updated_time' => $userLevelObject->updated_time,
            'created_at' => $userLevelObject->created_at,
            'updated_at' => $userLevelObject->updated_at,
            'deleted_at' => $userLevelObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($indexName, $userLevelObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新用户级别失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$userLevelObject' => $userLevelObject,'$adminObject' => $adminObject], 'EsUpdateUserLevelJob', 'handleError');
            throw new CommonException('EsUpdateUserLevelError');
        }

        CommonEvent::dispatch($adminObject, $userLevelObject, 'UpdateUserLevel');


        $result = code(['code' => 0,'msg' => '修改用户级别成功!']);

        return $result;
    }


    /**
     * 删除
     *
     * @param [type] $id
     * @param [type] $adminObject
     * @return void
     */
    public function deleteUserLevel(DeleteUserLevelDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteUserLevelError'));

        $validated = $requestDTO->toArray();


        $userLevelObject = UserLevel::find($validated['id']);

        if (!$userLevelObject) {
            throw new CommonException('ThisDataHasChildrenError');
        }


        $userLevelObject->deleted_at = date('Y-m-d H:i:s');

        $userLevelResult =  $userLevelObject->save();

        if (!$userLevelResult) {
            throw new CommonException('DeleteUserLevelError');
        }

        $indexName = config('common_es.indices.business.user_levels');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $esResult = EsFacade::updateDoc($indexName, $userLevelObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es删除用户级别失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$userLevelObject' => $userLevelObject,'$adminObject' => $adminObject], 'EsDeleteUserLevelJob', 'handleError');
            throw new CommonException('EsDeleteUserLevelError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'DeleteUserLevel');

        $result = code(['code' => 0,'msg' => '删除用户级别成功!']);

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function MultipleDeleteUserLevel(MultipleDeleteUserLevelDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MultipleDeleteUserLevelError'));

        $select_id_array = $requestDTO->select_id_array;

        $deleteResult = UserLevel::whereIn('id',$select_id_array)->delete();

        if (!$deleteResult) {
            throw new CommonException('MultipleDeleteUserLevelError');
        }

        $indexName = config('common_es.indices.business.user_levels');

        $userLevelCollection = UserLevel::withTrashed()->whereIn('id', $select_id_array)->get();

        $updateDataArray = [];

        foreach ($userLevelCollection as $userLevelObject) {
            $updateDataArray[] = [
                '_docId' => $userLevelObject->id,
                'id' => $userLevelObject->id,
                'type' => $userLevelObject->type,
                'item_name' => $userLevelObject->item_name,
                'item_code' => $userLevelObject->item_code,
                'description' => $userLevelObject->description,
                'sort' => $userLevelObject->sort,
                'created_time' => $userLevelObject->created_time,
                'updated_time' => $userLevelObject->updated_time,
                'created_at' => $userLevelObject->created_at,
                'updated_at' => $userLevelObject->updated_at,
                'deleted_at' => $userLevelObject->deleted_at,
            ];
        }

        $esResult = EsFacade::batchActDoc($indexName, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es批量更新用户级别失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$userLevelObject' => $userLevelObject,'$adminObject' => $adminObject], 'EsMultipleDeleteUserLevelJob', 'handleError');
            throw new CommonException('EsMultipleDeleteUserLevelError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'MultipleDeleteUserLevel');

        $result = code(['code' => 0,'msg' => '批量删除用户级别成功!']);


        return $result;
    }

    /**
     * \添加用户级别配置项
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function addUserLevelItemUnion(AddUserLevelItemUnionDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddUserLevelItemUnionError'));

        $validated = $requestDTO->toArray();

        $userLevelItemUnionObject = new UserLevelItemUnion();

        foreach ($validated as $key => $value) {
            if (\is_null($value)) {
                $value = "";
            }

            $userLevelItemUnionObject->$key = $value;
        }

        $userLevelItemUnionObject->created_time = time();
        $userLevelItemUnionObject->created_at = time();

        $userLevelItemUnionResult = $userLevelItemUnionObject->save();

        if (!$userLevelItemUnionResult) {
            throw new CommonException('AddUserLevelItemUnionError');
        }

        $indexName = config('common_es.indices.union.user_level_item_unions');

        $insertDataArray = [
            '_docId' => $userLevelItemUnionObject->id,
            'id' => $userLevelItemUnionObject->id,
            'user_level_id' => $userLevelItemUnionObject->user_level_id,
            'level_item_id' => $userLevelItemUnionObject->level_item_id,
            'value_type' => $userLevelItemUnionObject->value_type,
            'value' => $userLevelItemUnionObject->value,
            'sort' => $userLevelItemUnionObject->sort,
            'created_time' => $userLevelItemUnionObject->created_time,
            'updated_time' => $userLevelItemUnionObject->updated_time,
            'created_at' => $userLevelItemUnionObject->created_at,
            'updated_at' => $userLevelItemUnionObject->updated_at,
            'deleted_at' => $userLevelItemUnionObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $userLevelItemUnionObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加用户级别和级别配置项关联失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$userLevelItemUnionObject' => $userLevelItemUnionObject,'$adminObject' => $adminObject], 'EsAddUserLevelItemUnionJob', 'handleError');
            throw new CommonException('EsUAddUserLevelItemUnionError');
        }

        CommonEvent::dispatch($adminObject, $userLevelItemUnionObject, 'AddUserLevelItemUnion');

        $result = code(['code' => 0,'msg' => '添加用户级别配置项成功!']);

        return $result;
    }

    /**
     * 更新 用户级别配置项
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updateUserLevelItemUnion(UpdateUserLevelItemUnionDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateUserLevelItemUnionError'));

        $validated = $requestDTO->toArray();

        $userLevelItemUnionObject = UserLevelItemUnion::find($validated['id']);

        if (!$userLevelItemUnionObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$userLevelItemUnionObject ->revision];

        foreach ($validated as $key => $value) {
            if ($key === 'id') {
                $where[] = ['id','=',$value];
                continue;
            }

            if (\is_null($value)) {
                $value = "";
            }

            $updateDataArray[$key] = $value;
        }

        $updateDataArray['revision'] = $userLevelItemUnionObject ->revision + 1;

        $updateDataArray['updated_time'] = time();

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        $userLevelItemUnionResult = UserLevelItemUnion::where($where)->update($updateDataArray);

        if (!$userLevelItemUnionResult) {
            throw new CommonException('UpdateUserLevelError');
        }

        $userLevelItemUnionObject = $userLevelItemUnionObject->fresh();

        $indexName = config('common_es.indices.union.user_level_item_unions');

        $updateDataArray = [
            '_docId' => $userLevelItemUnionObject->id,
            'id' => $userLevelItemUnionObject->id,
            'user_level_id' => $userLevelItemUnionObject->user_level_id,
            'level_item_id' => $userLevelItemUnionObject->level_item_id,
            'value_type' => $userLevelItemUnionObject->value_type,
            'value' => $userLevelItemUnionObject->value,
            'sort' => $userLevelItemUnionObject->sort,
            'created_time' => $userLevelItemUnionObject->created_time,
            'updated_time' => $userLevelItemUnionObject->updated_time,
            'created_at' => $userLevelItemUnionObject->created_at,
            'updated_at' => $userLevelItemUnionObject->updated_at,
            'deleted_at' => $userLevelItemUnionObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($indexName, $userLevelItemUnionObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新用户级别和级别配置项关联失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$userLevelItemUnionObject' => $userLevelItemUnionObject,'$adminObject' => $adminObject], 'EsUpdateUserLevelItemUnionJob', 'handleError');
            throw new CommonException('EsUpdateUserLevelItemUnionError');
        }

        CommonEvent::dispatch($adminObject, $userLevelItemUnionObject, 'UpdateUserLevel');

        $result = code(['code' => 0,'msg' => '修改用户级别配置项成功!']);

        return $result;
    }


    /**
     * 删除 用户级别配置项
     *
     * @param [type] $id
     * @param [type] $adminObject
     * @return void
     */
    public function deleteUserLevelItemUnion(DeleteUserLevelItemUnionDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteUserLevelItemUnionError'));

        $validated = $requestDTO->toArray();

        $userLevelItemUnionObject = UserLevelItemUnion::find($validated['id']);

        if (!$userLevelItemUnionObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $userLevelItemUnionObject->deleted_at = date('Y-m-d H:i:s');

        $userLevelItemUnionResult =  $userLevelItemUnionObject->save();

        if (!$userLevelItemUnionResult) {
            throw new CommonException('DeleteUserLevelItemUnionError');
        }

        $indexName = config('common_es.indices.union.user_level_item_unions');

        $updateDataArray = [
            'deleted_at' =>  date('Y-m-d H:i:s'),
        ];

        $esResult = EsFacade::updateDoc($indexName, $userLevelItemUnionObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es删除用户级别和级别配置项关联失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$userLevelItemUnionObject' => $userLevelItemUnionObject,'$adminObject' => $adminObject], 'EsDeleteUserLevelItemUnionJob', 'handleError');
            throw new CommonException('EsDeleteUserLevelItemUnionError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'DeleteUserLevelItemUnion');

        $result = code(['code' => 0,'msg' => '删除用户级别配置项成功!']);

        return $result;
    }
}
