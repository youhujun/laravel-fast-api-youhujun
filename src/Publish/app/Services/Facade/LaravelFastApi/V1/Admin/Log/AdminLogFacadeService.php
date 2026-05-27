<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-02 16:16:01
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-17 13:59:54
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Log\AdminLogFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Log;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\GetAdminEventLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\GetAdminLoginLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\DeleteAdminEventLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\DeleteAdminLoginLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\MultipleDeleteAdminLoginLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\MultipleDeleteAdminEventLogDTO;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\Admin\Log\AdminEventLog;
use App\Models\LaravelFastApi\V1\Admin\Log\AdminLoginLog;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Log\EsAdminEventLogCollection;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Log\EsAdminLoginLogCollection;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Log\AdminLogController
 * @see \App\Facades\LaravelFastApi\V1\Admin\Log\AdminLogFacade
 */
class AdminLogFacadeService
{
    public function test()
    {
        echo "AdminLogFacadeService test";
    }

    public static $sortMapArray = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];

    /**
      *获取登录日志
      * @param [type] $validated
      * @param [type] $adminObject
      * @return void
      */
    public function getAdminLoginLog(GetAdminLoginLogDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetAdminLoginError'));

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.logs.admin_login_logs');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

        if ($requestDTO?->admin_uid) {
            $esQuery->where('admin_uid', $requestDTO->admin_uid);
        }

        if ($requestDTO?->status) {
            $esQuery->where('status', $requestDTO->status);
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

        $loginLogPaginator = $esQuery->page($currentPage, $perPage)->paginate();

        if (!optional($loginLogPaginator)) {
            throw new CommonException('GetAdminLoginError');
        }

        $result = new EsAdminLoginLogCollection($loginLogPaginator, ['code' => 0,'msg' => '获取登录日志成功!']);

        return $result;
    }

    /**
     * 删除日志
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function deleteAdminLoginLog(DeleteAdminLoginLogDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteAdminLoginLogError'));

        $indexName = config('common_es.indices.logs.admin_login_logs');

        $esAdminLoginLogObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('admin_login_log_uid', $requestDTO->admin_login_log_uid)->get()->first();

        if (!$esAdminLoginLogObject) {
            throw new CommonException('ServiceBusyError');
        }

        $adminLoginLogObject = AdminLoginLog::queryByShard($esAdminLoginLogObject->admin_uid)->where('admin_login_log_uid', $requestDTO->admin_login_log_uid)->first();

        if (!$adminLoginLogObject) {
            throw new CommonException('ThatDataNotExistError');
        }

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')

        ];

        $deleteResult = $adminLoginLogObject->updateWithShard($updateDataArray);

        if (!$deleteResult) {
            throw new CommonException('DeleteAdminLoginLogError');
        }

                $indexName = config('common_es.indices.logs.admin_login_logs');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $esResult = EsFacade::updateDoc($indexName, $adminLoginLogObject->biz_id, $updateDataArray);

        if (!isset($esResult) || !isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es删除管理员登录日志失败','$adminLoginLogObject' => $adminLoginLogObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'EsDeleteAdminLoginLogJob', 'handleError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'DeleteAdminLoginLog');

        $result = code(['code' => 0,'msg' => '删除登录日志成功!']);

        return $result;
    }

    /**
     * 多选删除
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function multipleDeleteAdminLoginLog(MultipleDeleteAdminLoginLogDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MultipleDeleteAdminLoginLogError'));

        $indexName = config('common_es.indices.logs.admin_login_logs');

        $select_uid_array = $requestDTO->select_uid_array;

        $esAdminLoginLogCollection = EsQueryFacade::index($indexName)->whereNull('deleted_at')->whereIn('admin_login_log_uid', $select_uid_array)->get();

        if ($esAdminLoginLogCollection->count() != count($select_uid_array)) {
            throw new CommonException('ServiceBusyError');
        }

        foreach ($esAdminLoginLogCollection as $key => $esAdminLoginLogObject) {
            $adminLoginLogObject = AdminLoginLog::queryByShard($esAdminLoginLogObject->admin_uid)->where('admin_login_log_uid', $esAdminLoginLogObject->admin_login_log_uid)->first();

            if (!$adminLoginLogObject) {
                plog(['error' => '未找到登录日志对象','$esAdminLoginLogObject' => $esAdminLoginLogObject], 'AdminLogFacadeService', 'multipleDeleteAdminLoginLogError');
                continue;
            }

            $updateDataArray = [
                'deleted_at' => date('Y-m-d H:i:s')
            ];

            $deleteResult = $adminLoginLogObject->updateWithShard($updateDataArray);

            if (!$deleteResult) {
                plog(['error' => '删除登录日志失败','$adminLoginLogObject' => $adminLoginLogObject], 'AdminLogFacadeService', 'multipleDeleteAdminLoginLogError');
                continue;
            }

            $indexName = config('common_es.indices.logs.admin_login_logs');

            $updateDataArray = [
                'deleted_at' => date('Y-m-d H:i:s')
            ];

            $esResult = EsFacade::updateDoc($indexName, $adminLoginLogObject->biz_id, $updateDataArray);

            if (!isset($esResult) || !isset($esResult['code']) || $esResult['code'] != 0) {
                plog(['error' => 'es删除管理员登录日志失败','$adminLoginLogObject' => $adminLoginLogObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'EsDeleteAdminLoginLogJob', 'handleError');
            }
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'MultipleDeleteAdminLoginLog');

        $result = code(['code' => 0,'msg' => '批量删除登录日志成功!']);

        return $result;
    }

    /**
     * 获取事件日志
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function getAdminEventLog(GetAdminEventLogDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetEventLogError'));

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.logs.admin_event_logs');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

        if ($requestDTO?->admin_uid) {
            $esQuery->where('admin_uid', $requestDTO->admin_uid);
        }

        if ($requestDTO?->eventType) {
            $esQuery->where('eventType', $requestDTO->eventType);
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

        $eventLogPaginator = $esQuery->page($currentPage, $perPage)->paginate();

        // p($eventLogPaginator);
        // die;

        if (!optional($eventLogPaginator)) {
            throw new CommonException('GetEventLogError');
        }

        $result = new EsAdminEventLogCollection($eventLogPaginator, ['code' => 0,'msg' => '获取事件日志成功!']);

        return $result;
    }

    /**
     * 删除事件日志
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function deleteAdminEventLog(DeleteAdminEventLogDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteEventLogError'));

        $indexName = config('common_es.indices.logs.admin_event_logs');

        $esAdminEventLogObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('admin_event_log_uid', $requestDTO->admin_event_log_uid)->get()->first();

        if (!$esAdminEventLogObject) {
            throw new CommonException('ServiceBusyError');
        }

        $adminEventLogObject = AdminEventLog::queryByShard($esAdminEventLogObject->admin_uid)->where('admin_event_log_uid', $requestDTO->admin_event_log_uid)->first();

        if (!$adminEventLogObject) {
            throw new CommonException('ThatDataNotExistError');
        }

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')

        ];

        $deleteResult = $adminEventLogObject->updateWithShard($updateDataArray);

        if (!$deleteResult) {
            throw new CommonException('DeleteEventLogError');
        }

        $indexName = config('common_es.indices.logs.admin_event_logs');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $esResult = EsFacade::updateDoc($indexName, $adminEventLogObject->biz_id, $updateDataArray);

        if (!isset($esResult) || !isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es删除事件日志失败','$adminEventLogObject' => $adminEventLogObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'EsDeleteAdminEventLogJob', 'handleError');
        }

        $result = code(['code' => 0,'msg' => '删除事件日志成功!']);

        return $result;
    }

    /**
     * 多选删除
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function multipleDeleteAdminEventLog(MultipleDeleteAdminEventLogDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MultipleDeleteEventLogError'));

        $indexName = config('common_es.indices.logs.admin_event_logs');

        $select_uid_array = $requestDTO->select_uid_array;

        $esAdminEventLogCollection = EsQueryFacade::index($indexName)->whereNull('deleted_at')->whereIn('admin_event_log_uid', $select_uid_array)->get();

        if ($esAdminEventLogCollection->count() != count($select_uid_array)) {
            throw new CommonException('ServiceBusyError');
        }

        foreach ($esAdminEventLogCollection as $key => $esAdminEventLogObject) {
            $adminEventLogObject = AdminEventLog::queryByShard($esAdminEventLogObject->admin_uid)->where('admin_event_log_uid', $esAdminEventLogObject->admin_event_log_uid)->first();

            if (!$adminEventLogObject) {
                plog(['error' => '未找到事件日志对象','$esAdminEventLogObject' => $esAdminEventLogObject], 'AdminLogFacadeService', 'multipleDeleteAdminEventLogError');
                continue;
            }

            $updateDataArray = [
                'deleted_at' => date('Y-m-d H:i:s')
            ];

            $deleteResult = $adminEventLogObject->updateWithShard($updateDataArray);

            if (!$deleteResult) {
                plog(['error' => '删除事件日志失败','$adminEventLogObject' => $adminEventLogObject], 'AdminLogFacadeService', 'multipleDeleteAdminEventLogError');
                continue;
            }

            $indexName = config('common_es.indices.logs.admin_event_logs');

            $updateDataArray = [
                'deleted_at' => date('Y-m-d H:i:s')
            ];

            $esResult = EsFacade::updateDoc($indexName, $adminEventLogObject->biz_id, $updateDataArray);

            if (!isset($esResult) || !isset($esResult['code']) || $esResult['code'] != 0) {
                plog(['error' => 'es删除事件日志失败','$adminEventLogObject' => $adminEventLogObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'EsDeleteAdminEventLogJob', 'handleError');
            }
        }

        $result = code(['code' => 0,'msg' => '批量删除事件日志成功!']);

        return $result;
    }
}
