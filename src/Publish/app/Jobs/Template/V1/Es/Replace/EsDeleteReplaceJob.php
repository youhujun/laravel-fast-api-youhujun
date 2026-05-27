<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-01 17:30:07
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-01 23:27:37
 * @FilePath: \youhu-laravel-api-12\app\Jobs\Template\V1\Es\Replace\EsDeleteReplaceJob.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Jobs\Template\V1\Es\Replace;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Middleware\RateLimited;
use App\Models\LaravelFastApi\V1\User\User;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

class EsDeleteReplaceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected User $userObject;
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
    public function __construct(User $userObject)
    {
        $this->userObject = $userObject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userObject = $this->userObject;

        $indexName = config('common_es.indices.user.users');

        $updateDataArray = ['deleted_at' => date('Y-m-d H:i:s')];

        $esResult = EsFacade::updateDoc($indexName, $userObject->biz_id, $updateDataArray);

        plog(['info' => 'EsDeleteUserJob','$esResult' => $esResult], 'EsDeleteUserJob', 'handle');

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['info' => 'EsDeleteUserJobError','$esResult' => $esResult], 'EsDeleteUserJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
