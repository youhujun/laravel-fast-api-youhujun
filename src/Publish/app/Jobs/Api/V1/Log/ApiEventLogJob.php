<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-30 19:32:23
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-05 21:29:08
 * @FilePath: \youhu-laravel-api-12\app\Jobs\Api\V1\Log\ApiEventLogJob.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Jobs\Api\V1\Log;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Middleware\RateLimited;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

class ApiEventLogJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected string $user_uid;
    protected string $service_flag;
    protected string $paramsJsonString;
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
    public function __construct(string $user_uid, string $service_flag, string $paramsJsonString)
    {
        $this->user_uid = $user_uid;
        $this->service_flag = $service_flag;
        $this->paramsJsonString = $paramsJsonString;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user_uid = $this->user_uid;
        $service_flag = $this->service_flag;
        $paramsJsonString = $this->paramsJsonString;

        $indexName = config('common_es.indices.logs.api_event_logs');

        $api_event_log_uid = get_snow_flake_id();
        // 构造ES文档数据
        $dataArray = [
            '_docId' => $api_event_log_uid,
            'user_uid' => $user_uid,
            'data_type' => 1,
            'service_code' => $service_flag,
            'operator_type' => 10,
            'operator_uid' => $user_uid,
            'note' => $paramsJsonString,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // 指定ID创建文档
        $result = EsFacade::createDoc($indexName, $dataArray, $api_event_log_uid);

        plog(['info' => '记录测试日志结果','result' => $result], 'ApiEventLogJob', 'handle');

        if (isset($result['code']) || $result['code'] != 0) {
            plog(['error' => 'es记录日志失败','result' => $result], 'ApiEventLogJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
