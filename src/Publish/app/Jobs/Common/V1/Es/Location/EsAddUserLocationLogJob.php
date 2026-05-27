<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-05-01 05:00:53
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-01 05:07:36
 * @FilePath: \youhu-laravel-api-12\app\Jobs\Common\V1\Es\Location\EsAddUserLocationLogJob.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Jobs\Common\V1\Es\Location;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Middleware\RateLimited;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Log\UserLocationLog;

class EsAddUserLocationLogJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected User $userObject;
    protected UserLocationLog $userLocationLogObject;
    /**
     * 任务尝试次数
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 3;

    /**
     * 任务失败前允许的最大异常数
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * 如果任务的模型不再存在，则删除该任务
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;
    /**
     * Create a new job instance.
     */
    public function __construct(User $userObject, UserLocationLog $userLocationLogObject)
    {
        $this->userObject = $userObject;
        $this->userLocationLogObject = $userLocationLogObject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userObject = $this->userObject;
        $userLocationLogObject = $this->userLocationLogObject;

        $indexName = config('common_es.indices.logs.user_location_logs');

        $configKey = get_shard_config_key();

        $insertDataArray = [
            '_docId' => $userLocationLogObject->user_location_log_uid,
            'shard_key' => $userLocationLogObject->shard_key,
            'shard_db' => ShardFacade::getDbName($userLocationLogObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($userLocationLogObject->user_uid, 'user_location_logs', $configKey),
            'user_location_log_uid' => $userLocationLogObject->user_location_log_uid,
            'user_uid' => $userLocationLogObject->user_uid,
            'latitude' => $userLocationLogObject->latitude,
            'longitude' => $userLocationLogObject->longitude,
            'type' => $userLocationLogObject->type,
            'data_type' => $userLocationLogObject->data_type,
            'address' => $userLocationLogObject->address,
            'created_time' => $userLocationLogObject->created_time,
            'updated_time' => $userLocationLogObject->updated_time,
            'created_at' => $userLocationLogObject->created_at,
            'updated_at' => $userLocationLogObject->updated_at,
            'deleted_at' => $userLocationLogObject->deleted_at,
        ];

        $result = EsFacade::createDoc($indexName, $insertDataArray, $userLocationLogObject->biz_id);

        if (!isset($result['code']) || $result['code'] != 0) {
            plog(['error' => 'es添加用户位置日志失败','$result' => $result,'$insertDataArray' => $insertDataArray], 'EsAddUserLocationLogJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
