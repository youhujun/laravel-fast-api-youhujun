<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-28 15:39:12
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-28 15:39:28
 * @FilePath: \youhu-laravel-api-12\app\Jobs\Common\Phone\CommonEvent\EsUserEventLogJob.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Jobs\Common\Phone\CommonEvent;

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
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\Log\UserEventLog;

class EsUserEventLogJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected UserEventLog $userEventLogObject;
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
    public function __construct(UserEventLog $userEventLogObject)
    {
        $this->userEventLogObject = $userEventLogObject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userEventLogObject = $this->userEventLogObject;

        $indexName = config('common_es.indices.logs.user_event_logs');

        $configKey = get_shard_config_key();

        $insertDataArray = [
            '_docId' => $userEventLogObject->biz_id,
            'shard_key' => $userEventLogObject->shard_key,
            'shard_db' => ShardFacade::getDbName($userEventLogObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($userEventLogObject->user_uid, 'user_event_logs', $configKey),
            'user_event_log_uid' => $userEventLogObject->biz_id,
            'user_uid' => $userEventLogObject->user_uid,
            'data_type' => $userEventLogObject->data_type,
            'event_route_action' => $userEventLogObject->event_route_action,
            'event_name' => $userEventLogObject->event_name,
            'event_type' => $userEventLogObject->event_type,
            'event_code' => $userEventLogObject->event_code,
            'note' => $userEventLogObject->note,
            'created_at' => $userEventLogObject->created_at,
            'updated_at' => $userEventLogObject->updated_at,
            'deleted_at' => $userEventLogObject->deleted_at,
            'created_time' => $userEventLogObject->created_time,
            'updated_time' => $userEventLogObject->updated_time,

        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $userEventLogObject->biz_id);

        //plog(['info' => 'EsAdminEventLogJob','esResult' => $esResult], 'EsAdminEventLogJob', 'handle');

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['info' => 'EsAdminEventLogJob','esResult' => $esResult,'$userEventLogObject' => $userEventLogObject], 'EsUserEventLogJob', 'handle');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
