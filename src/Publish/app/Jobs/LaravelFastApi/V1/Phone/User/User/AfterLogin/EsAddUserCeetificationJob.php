<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-29 16:54:19
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-29 16:59:17
 * @FilePath: \youhu-laravel-api-12\app\Jobs\LaravelFastApi\V1\Phone\User\User\AfterLogin\EsAddUserCeetificationJob.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Jobs\LaravelFastApi\V1\Phone\User\User\AfterLogin;

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
use App\Models\LaravelFastApi\V1\User\UserCertification;

class EsAddUserCeetificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected User $userObject;
    protected UserCertification $userCertificationObject;
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
    public function __construct(User $userObject, UserCertification $userCertificationObject)
    {
        $this->userObject = $userObject;
        $this->userCertificationObject = $userCertificationObject;
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userObject =  $this->userObject;
        $userCertificationObject =  $this->userCertificationObject;

        $indexName = config('common_es.indices.user.user_certifications');

        $configKey = get_shard_config_key();

        $insertDataArray = [
            'user_certification_uid' => $userCertificationObject->biz_id,
            'user_uid' => $userCertificationObject->user_uid,
            'shard_key' => $userCertificationObject->shard_key,
            'shard_db' => ShardFacade::getDbName($userCertificationObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($userCertificationObject->user_uid, 'user_certifications', $configKey),
            'cert_type' => $userCertificationObject->cert_type,
            'cert_status' => $userCertificationObject->cert_status,
            'certified_time' => $userCertificationObject->certified_time,
            'certified_at' => $userCertificationObject->certified_at,
            'cert_remark' => $userCertificationObject->cert_remark,
            'created_at' => $userCertificationObject->created_at,
            'updated_at' => $userCertificationObject->updated_at,
            'created_time' => $userCertificationObject->created_time,
            'updated_time' => $userCertificationObject->updated_time,
            'deleted_at' => $userCertificationObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $userCertificationObject->biz_id);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加用户登录日志失败','$userCertificationObject' => $userCertificationObject,'$esResult' => $esResult,'$userObject' => $userObject], 'EsAddUserCeetificationJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
