<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-27 22:35:23
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-27 23:54:09
 * @FilePath: \youhu-laravel-api-12\app\Jobs\LaravelFastApi\V1\Phone\User\User\Log\EsAddUserLoginLogJob.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Jobs\LaravelFastApi\V1\Phone\User\User\Log;

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
use App\Models\LaravelFastApi\V1\User\Log\UserLoginLog;

class EsAddUserLoginLogJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected UserLoginLog $userLoginLogObject;
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
    public function __construct(UserLoginLog $userLoginLogObject, User $userObject)
    {
        $this->userLoginLogObject = $userLoginLogObject;
        $this->userObject = $userObject;
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userLoginLogObject = $this->userLoginLogObject;
        $userObject = $this->userObject;

        $indexName = config('common_es.indices.logs.user_login_logs');

        $configKey = get_shard_config_key();

        $insertDataArray = [
            'user_login_log_uid' => $userLoginLogObject->biz_id,
            'user_uid' => $userLoginLogObject->user_uid,
            'shard_key' => $userLoginLogObject->shard_key,
            'shard_db' => ShardFacade::getDbName($userLoginLogObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($userLoginLogObject->user_uid, 'user_login_logs', $configKey),
            'data_type' => $userLoginLogObject->data_type,
            'login_type' => $userLoginLogObject->login_type,
            'status' => $userLoginLogObject->status,
            'ip' => $userLoginLogObject->ip,
            'instruction' => $userLoginLogObject->instruction,
            'created_at' => $userLoginLogObject->created_at,
            'updated_at' => $userLoginLogObject->updated_at,
            'created_time' => $userLoginLogObject->created_time,
            'updated_time' => $userLoginLogObject->updated_time,
            'deleted_at' => $userLoginLogObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $userLoginLogObject->biz_id);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加用户登录日志失败','$userLoginLogObject' => $userLoginLogObject,'$esResult' => $esResult,'$userObject' => $userObject], 'EsAddUserLoginLogJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
