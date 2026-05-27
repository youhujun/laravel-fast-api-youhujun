<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 20:26:46
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-05 02:45:51
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminSystemConfigFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\SystemConfig\AddSystemConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\SystemConfig\GetSystemConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\SystemConfig\UpdateSystemConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\SystemConfig\DeleteSystemConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\SystemConfig\MultipleDeleteSystemConfigDTO;
//Event
use App\Events\LaravelFastApi\V1\Admin\System\SystemConfig\AddSystemConfigEvent;
use App\Events\LaravelFastApi\V1\Admin\System\SystemConfig\UpdateSystemConfigEvent;
use App\Events\LaravelFastApi\V1\Admin\System\SystemConfig\DeleteSystemConfigEvent;
use App\Events\LaravelFastApi\V1\Admin\System\SystemConfig\MultipleDeleteSystemConfigEvent;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\SystemConfig;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\System\EsSystemCOnfigCollection;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\System\EsSystemCOnfigResource;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\SystemConfigController
 * @see \App\Facades\LaravelFastApi\V1\Admin\System\SystemConfig\AdminSystemConfigFacade
 */
class AdminSystemConfigFacadeService
{
    public function test()
    {
        echo "AdminSystemConfigFacadeService test";
    }

    protected static $sortMapArray = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];

    protected static $searchItemMapArray = [
        'item_label',
        'item_introduction'
    ];

    protected static $storage_public_path = DIRECTORY_SEPARATOR.'app'. DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;


    /**
     * 查询
     *
     * @param [type] $validated
     * @return void
     */
    public function getSystemConfig(GetSystemConfigDTO $requestDTO)
    {
        $result = code(config('admin_cdoe.GetSystemConfigError'));

        $validated = $requestDTO->toArray();

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.system.system_configs');

        $esQuery = EsQueryFacade::index($indexName);

        $esQuery->whereNull('deleted_at');

        if (isset($requestDTO->findSelectIndex) && isset($requestDTO->find) && !empty($requestDTO->find))  {
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
        //排序
        if (isset($requestDTO->sortType)) {
            $sortType = $requestDTO->sortType;

            $esQuery->orderBy(self::$sortMapArray[$sortType][0], self::$sortMapArray[$sortType][1]);
        }

        $download = null;

        $systemConfigPaginator = $esQuery->page($currentPage, $perPage)->paginate();

        if (\optional($systemConfigPaginator)) {
            $result = new EsSystemCOnfigCollection($systemConfigPaginator, ['code' => 0,'msg' => '获取系统配置成功!'], $download);
            //如果要增加其他返回参数,需要在SystemConfigCollection处理
            //$result = code(config('admin_cdoe.replaceSuccess'),['data'=>$systemConfigList,'download' => $download]);
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
    public function addSystemConfig(AddSystemConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_cdoe.AddSystemConfigError'));

        $validated = $requestDTO->toArray();

        $systemConfigObject = new SystemConfig();

        foreach ($validated as $key => $value) {
            if (isset($value)) {
                $systemConfigObject->$key = $value;
            }
        }

        $systemConfigObject->created_time = time();
        $systemConfigObject->created_at = time();

        $systemConfigResult = $systemConfigObject->save();

        if (!$systemConfigResult) {
            throw new CommonException('AddSystemConfigError');
        }

        AddSystemConfigEvent::dispatch($systemConfigObject, $adminObject);

        CommonEvent::dispatch($adminObject, $systemConfigObject, 'AddSystemConfig');

        $redisKey = config('common_redis.system_config.key');
        $redisField = config('common_redis.system_config.field');
        Redis::hdel($redisKey, $redisField);
        Cache::flush();

        $result = code(['code' => 0,'msg' => '添加系统配置成功!']);

        return $result;
    }


    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updateSystemConfig(UpdateSystemConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_cdoe.UpdateSystemConfigError'));

        $validated = $requestDTO->toArray();

        $systemConfigObject = SystemConfig::find($validated['id']);

        if (!optional($systemConfigObject)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$systemConfigObject ->revision];

        foreach ($validated as $key => $value) {
            if ($key === 'id') {
                $where[] = ['id','=',$value];
                continue;
            }

            if (isset($value)) {
                $updateDataArray[$key] = $value;
            }
        }

        $updateDataArray['revision'] = $systemConfigObject ->revision + 1;

        $updateDataArray['updated_time'] = time();

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        $systemConfigResult = SystemConfig::where($where)->update($updateDataArray);

        if (!$systemConfigResult) {
            throw new CommonException('UpdateSystemConfigError');
        }

        $systemConfigObject = $systemConfigObject->fresh();
        
        UpdateSystemConfigEvent::dispatch($systemConfigObject, $adminObject);

        CommonEvent::dispatch($adminObject, $systemConfigObject, 'UpdateSystemConfig');

        $redisKey = config('common_redis.system_config.key');
        $redisField = config('common_redis.system_config.field');
        Redis::hdel($redisKey, $redisField);
        Cache::flush();

        $result = code(['code' => 0,'msg' => '更新系统配置成功']);

        return $result;
    }


    /**
     * 删除
     *
     * @param [type] $id
     * @param [type] $adminObject
     * @return void
     */
    public function deleteSystemConfig(DeleteSystemConfigDTO $requestDTO, Admin $adminObject)
    {
        //删除
        $result = code(config('admin_cdoe.DeleteSystemConfigError'));

        $validated = $requestDTO->toArray();

        $eventName = 'DeleteSystemConfig';

        $systemConfigObject = SystemConfig::find($validated['id']);

        if (!optional($systemConfigObject)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $systemConfigResult =  $systemConfigObject->delete();

        if (!$systemConfigResult) {
            throw new CommonException('DeleteSystemConfigError');
        }

        DeleteSystemConfigEvent::dispatch($systemConfigObject, $adminObject);

        CommonEvent::dispatch($adminObject, $validated['id'], $eventName);

        $redisKey = config('common_redis.system_config.key');
        $redisField = config('common_redis.system_config.field');
        Redis::hdel($redisKey, $redisField);
        Cache::flush();

        $result = code(['code' => 0,'msg' => '删除成功']);

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function multipleDeleteSystemConfig(MultipleDeleteSystemConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_cdoe.MultipleDeleteSystemConfigError'));

        $validated = $requestDTO->toArray();

        $eventName = 'MultipleDeleteSystemConfig';

        $deleteResult = SystemConfig::whereIn('id', $validated['select_id_array'])->delete();

        if (!$deleteResult) {
            throw new CommonException('MultipleRestoreSystemConfigError');
        }

        MultipleDeleteSystemConfigEvent::dispatch($requestDTO, $adminObject);

        CommonEvent::dispatch($adminObject, $validated, $eventName);

        $redisKey = config('common_redis.system_config.key');
        $redisField = config('common_redis.system_config.field');
        Redis::hdel($redisKey, $redisField);
        Cache::flush();

        $result = code(['code' => 0,'msg' => '批量删除成功']);

        return $result;
    }
}
