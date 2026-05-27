<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-01 17:29:55
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-01 23:27:10
 * @FilePath: \youhu-laravel-api-12\app\Jobs\Template\V1\Es\Replace\EsDisableReplaceJob.php
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
use App\Models\LaravelFastApi\V1\Admin\Admin;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

class EsDisableReplaceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected User $userObject;
    protected Admin $adminObject;
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

        $updateDataArray = [
            'account_status' => 0,
        ];

        $esResult = EsFacade::updateDoc($indexName, $userObject->biz_id, $updateDataArray);

        plog(['info' => 'EsDisableUserJob','$esResult' => $esResult], 'EsDisableUserJob', 'handle');

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['info' => 'EsDisableUserJobError','$esResult' => $esResult], 'EsDisableUserJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
