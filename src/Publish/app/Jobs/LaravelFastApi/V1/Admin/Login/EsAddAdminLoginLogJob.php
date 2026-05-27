<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-26 01:46:46
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 02:00:27
 * @FilePath: \youhu-laravel-api-12\app\Jobs\LaravelFastApi\V1\Admin\Login\EsAddAdminLoginLogJob.php
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

class EsAddAdminLoginLogJob implements ShouldQueue
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

        $indexNmae = config('common_es.indices.logs.admin_login_logs');

        $configKey = get_shard_config_key();

        $insertDataArray = [
            '_docId' => $adminLoginLogObject->biz_id,
            'shard_key' => $adminLoginLogObject->shard_key,
            'shard_db' => ShardFacade::getDbName($adminLoginLogObject->admin_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($adminLoginLogObject->admin_uid, 'admin_login_logs', $configKey),
            'admin_login_log_uid' => $adminLoginLogObject->admin_login_log_uid,
            'admin_uid' => $adminLoginLogObject->admin_uid,
            'status' => $adminLoginLogObject->status,
            'instruction' => $adminLoginLogObject->instruction,
            'ip' => $adminLoginLogObject->ip,
            'data_type' => $adminLoginLogObject->data_type,
            'login_type' => $adminLoginLogObject->login_type,
            'created_at' => $adminLoginLogObject->created_at,
            'updated_at' => $adminLoginLogObject->updated_at,
            'deleted_at' => $adminLoginLogObject->deleted_at,
            'created_time' => $adminLoginLogObject->created_time,
            'updated_time' => $adminLoginLogObject->updated_time,
        ];

        $esResult = EsFacade::createDoc($indexNmae, $insertDataArray, $adminLoginLogObject->biz_id);

        if (!isset($esResult) || !isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加管理员登录日志失败','$adminLoginLogObject' => $adminLoginLogObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'EsAddAdminLoginLogJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
