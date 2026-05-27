<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-02 18:17:51
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-22 16:35:51
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Log\AdminUserLogFacadeService.php
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
use App\DTOs\LaravelFastApi\V1\Admin\Log\UserLog\GetUserEventLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\UserLog\GetUserLoginLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\UserLog\DeleteUserEventLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\UserLog\DeleteUserLoginLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\UserLog\MultipleDeleteUserEventLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\UserLog\MultipleDeleteUserLoginLogDTO;
//Job
use App\Jobs\LaravelFastApi\V1\Admin\User\Login\EsDeleteUserLoginLogJob;
use App\Jobs\LaravelFastApi\V1\Admin\User\Event\EsDeleteUserEventLogJob;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\Log\UserEventLog;
use App\Models\LaravelFastApi\V1\User\Log\UserLoginLog;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\Log\EsUserEventLogCollection;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\Log\EsUserLoginLogCollection;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Log\UserLogController
 * @see \App\Facades\Admin\Log\AdminUserLogFacade
 */
class AdminUserLogFacadeService
{
    public function test()
    {
        echo "AdminUserLogFacadeService test";
    }

    public static $sortMapArray = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];

    /**
     *
     *获取登录日志
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function getUserLoginLog(GetUserLoginLogDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetUserLoginLogError'));


        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.logs.user_login_logs');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

        if ($requestDTO?->user_uid) {
            $esQuery->where('user_uid', $requestDTO->user_uid);
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
            throw new CommonException('GetUserLoginLogError');
        }

        $result = new EsUserLoginLogCollection($loginLogPaginator, ['code' => 0,'msg' => '获取用户登录日志成功!']);

        return $result;
    }

    /**
     * 删除日志
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function deleteUserLoginLog(DeleteUserLoginLogDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteUserLoginLogError'));

        $indexName = config('common_es.indices.logs.user_login_logs');

        $esUserLoginLogObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_login_log_uid', $requestDTO->user_login_log_uid)->get()->first();

        if (!$esUserLoginLogObject) {
            throw new CommonException('ServiceBusyError');
        }

        $userLoginLogObject = UserLoginLog::queryByShard($esUserLoginLogObject->user_uid)->where('user_login_log_uid', $requestDTO->user_login_log_uid)->first();

        if (!$userLoginLogObject) {
            throw new CommonException('ThatDataNotExistError');
        }

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')

        ];

        $deleteResult = $userLoginLogObject->updateWithShard($updateDataArray);

        if (!$deleteResult) {
            throw new CommonException('DeleteUserLoginLogError');
        }

        $indexName = config('common_es.indices.logs.user_login_logs');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $esResult = EsFacade::updateDoc($indexName, $userLoginLogObject->biz_id, $updateDataArray);

        if (!isset($esResult) || !isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es删除用户登录日志失败','$userLoginLogObject' => $userLoginLogObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'AdminUserLogFacadeService', 'handleError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'DeleteUserLoginLog');

        $result = code(['code' => 0,'msg' => '删除用户登录日志成功!']);

        return $result;
    }

    /**
     * 多选删除
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function multipleDeleteUserLoginLog(MultipleDeleteUserLoginLogDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MultipleDeleteUserLoginLogError'));

        $indexName = config('common_es.indices.logs.user_login_logs');

        $select_uid_array = $requestDTO->select_uid_array;

        $esUserLoginLogCollection = EsQueryFacade::index($indexName)->whereNull('deleted_at')->whereIn('user_login_log_uid', $select_uid_array)->get();

        if ($esUserLoginLogCollection->count() != count($select_uid_array)) {
            throw new CommonException('ServiceBusyError');
        }

        foreach ($esUserLoginLogCollection as $key => $esUserLoginLogObject) {
            $userLoginLogObject = AdminLoginLog::queryByShard($esUserLoginLogObject->user_uid)->where('user_login_log_uid', $esUserLoginLogObject->user_login_log_uid)->first();

            if (!$userLoginLogObject) {
                plog(['error' => '未找到登录日志对象','$esUserLoginLogObject' => $esUserLoginLogObject], 'AdminUserLogFacadeService', 'multipleDeleteUserLoginLogError');
                continue;
            }

            $updateDataArray = [
                'deleted_at' => date('Y-m-d H:i:s')
            ];

            $deleteResult = $userLoginLogObject->updateWithShard($updateDataArray);

            if (!$deleteResult) {
                plog(['error' => '删除登录日志失败','$userLoginLogObject' => $userLoginLogObject], 'AdminUserLogFacadeService', 'multipleDeleteUserLoginLogError');
                continue;
            }

            $indexName = config('common_es.indices.logs.user_login_logs');

            $updateDataArray = [
                'deleted_at' => date('Y-m-d H:i:s')
            ];

            $esResult = EsFacade::updateDoc($indexName, $userLoginLogObject->biz_id, $updateDataArray);

            if (!isset($esResult) || !isset($esResult['code']) || $esResult['code'] != 0) {
                plog(['error' => 'es删除用户登录日志失败','$userLoginLogObject' => $userLoginLogObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'AdminUserLogFacadeService', 'handleError');
            }
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'MultipleDeleteUserLoginLog');

        $result = code(['code' => 0,'msg' => '批量删除登录日志成功!']);

        return $result;
    }

    /**
     * 获取事件日志
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function getUserEventLog(GetUserEventLogDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetEventLogError'));

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.logs.user_event_logs');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

        if ($requestDTO?->user_uid) {
            $esQuery->where('user_uid', $requestDTO->user_uid);
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

        //p($eventLogPaginator);die;

        if (!optional($eventLogPaginator)) {
            throw new CommonException('GetEventLogError');
        }

        $result = new EsUserEventLogCollection($eventLogPaginator, ['code' => 0,'msg' => '获取用户事件日志成功!']);

        return $result;
    }

    /**
     * 删除事件日志
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function deleteUserEventLog(DeleteUserEventLogDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteEventLogError'));

        $indexName = config('common_es.indices.logs.user_event_logs');

        $esUserEventLogObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_event_log_uid', $requestDTO->user_event_log_uid)->get()->first();

        if (!$esUserEventLogObject) {
            throw new CommonException('ServiceBusyError');
        }

        $userEventLogObject = UserEventLog::queryByShard($esUserEventLogObject->user_uid)->where('user_event_log_uid', $requestDTO->user_event_log_uid)->first();

        if (!$userEventLogObject) {
            throw new CommonException('ThatDataNotExistError');
        }

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')

        ];

        $deleteResult = $userEventLogObject->updateWithShard($updateDataArray);

        if (!$deleteResult) {
            throw new CommonException('DeleteEventLogError');
        }

        $indexName = config('common_es.indices.logs.user_event_logs');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $esResult = EsFacade::updateDoc($indexName, $userEventLogObject->biz_id, $updateDataArray);

        if (!isset($esResult) || !isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es删除事件日志失败','$userEventLogObject' => $userEventLogObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'AdminUserLogFacadeService', 'handleError');
        }

        $result = code(['code' => 0,'msg' => '删除事件日志成功!']);

        return $result;
    }

    /**
     * 多选删除
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function multipleDeleteUserEventLog(MultipleDeleteUserEventLogDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MultipleDeleteEventLogError'));

        $indexName = config('common_es.indices.logs.user_event_logs');

        $select_uid_array = $requestDTO->select_uid_array;

        $esUserEventLogCollection = EsQueryFacade::index($indexName)->whereNull('deleted_at')->whereIn('user_event_log_uid', $select_uid_array)->get();

        if ($esUserEventLogCollection->count() != count($select_uid_array)) {
            throw new CommonException('ServiceBusyError');
        }

        foreach ($esUserEventLogCollection as $key => $esUserEventLogObject) {
            $userEventLogObject = UserEventLog::queryByShard($esUserEventLogObject->user_uid)->where('user_event_log_uid', $esUserEventLogObject->user_event_log_uid)->first();

            if (!$userEventLogObject) {
                plog(['error' => '未找到事件日志对象','$esUserEventLogObject' => $esUserEventLogObject], 'AdminUserLogFacadeService', 'multipleDeleteUserEventLogError');
                continue;
            }

            $updateDataArray = [
                'deleted_at' => date('Y-m-d H:i:s')
            ];

            $deleteResult = $userEventLogObject->updateWithShard($updateDataArray);

            if (!$deleteResult) {
                plog(['error' => '删除事件日志失败','$userEventLogObject' => $userEventLogObject], 'AdminUserLogFacadeService', 'multipleDeleteUserEventLogError');
                continue;
            }

            $indexName = config('common_es.indices.logs.user_event_logs');

            $updateDataArray = [
                'deleted_at' => date('Y-m-d H:i:s')
            ];

            $esResult = EsFacade::updateDoc($indexName, $userEventLogObject->biz_id, $updateDataArray);

            if (!isset($esResult) || !isset($esResult['code']) || $esResult['code'] != 0) {
                plog(['error' => 'es删除事件日志失败','$userEventLogObject' => $userEventLogObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'AdminUserLogFacadeService', 'handleError');
            }
        }

        $result = code(['code' => 0,'msg' => '批量删除事件日志成功!']);

        return $result;
    }
}
