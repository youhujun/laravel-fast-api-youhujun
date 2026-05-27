<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-26 02:27:11
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 02:32:26
 * @FilePath: \youhu-laravel-api-12\app\Jobs\LaravelFastApi\V1\Admin\Login\EsDeleteAdminLoginLogJob.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Jobs\LaravelFastApi\V1\Admin\Login;

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
use App\Models\LaravelFastApi\V1\Admin\Log\AdminLoginLog;

class EsDeleteAdminLoginLogJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected AdminLoginLog $adminLoginLogObject;
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
    public function __construct(AdminLoginLog $adminLoginLogObject, Admin $adminObject)
    {
        $this->adminLoginLogObject = $adminLoginLogObject;
        $this->adminObject = $adminObject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adminLoginLogObject = $this->adminLoginLogObject;
        $adminObject = $this->adminObject;

        $indexName = config('common_es.indices.logs.admin_login_logs');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $esResult = EsFacade::updateDoc($indexName, $adminLoginLogObject->biz_id, $updateDataArray);

        if (!isset($esResult) || !isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es删除管理员登录日志失败','$adminLoginLogObject' => $adminLoginLogObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'EsDeleteAdminLoginLogJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
