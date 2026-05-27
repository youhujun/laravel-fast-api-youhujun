<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-01 01:53:29
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-04 11:56:18
 * @FilePath: \youhu-laravel-api-12\app\Jobs\Common\Admin\CommonEvent\EsAdminEventLogJob.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Jobs\Common\Admin\CommonEvent;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Middleware\RateLimited;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Models\LaravelFastApi\V1\Admin\Log\AdminEventLog;

class EsAdminEventLogJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public AdminEventLog $adminEventLogObject;

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
    public function __construct( AdminEventLog $adminEventLogObject)
    {
        $this->adminEventLogObject = $adminEventLogObject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adminEventLogObject = $this->adminEventLogObject;

        $indexName = config('common_es.indices.logs.admin_event_logs');

        $configKey = get_shard_config_key();

        $insertDataArray = [
            '_docId' => $adminEventLogObject->biz_id,
            'shard_key' => $adminEventLogObject->shard_key,
            'shard_db' => ShardFacade::getDbName($adminEventLogObject->admin_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($adminEventLogObject->admin_uid, 'admin_event_logs', $configKey),
            'admin_event_log_uid' => $adminEventLogObject->biz_id,
            'admin_uid' => $adminEventLogObject->admin_uid,
            'data_type' => $adminEventLogObject->data_type,
            'event_route_action' => $adminEventLogObject->event_route_action,
            'event_name' => $adminEventLogObject->event_name,
            'event_type' => $adminEventLogObject->event_type,
            'event_code' => $adminEventLogObject->event_code,
            'note' => $adminEventLogObject->note,
            'created_at' => $adminEventLogObject->created_at,
            'updated_at' => $adminEventLogObject->updated_at,
            'deleted_at' => $adminEventLogObject->deleted_at,
            'created_time' => $adminEventLogObject->created_time,
            'updated_time' => $adminEventLogObject->updated_time,

        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $adminEventLogObject->biz_id);

        //plog(['info' => 'EsAdminEventLogJob','esResult' => $esResult], 'EsAdminEventLogJob', 'handle');

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['info' => 'EsAdminEventLogJob','esResult' => $esResult,'$adminEventLogObject' => $adminEventLogObject], 'EsAdminEventLogJob', 'handle');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
