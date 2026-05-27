<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-21 21:47:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-21 21:58:58
 * @FilePath: \youhu-laravel-api-12\app\Jobs\Common\V1\Es\Develop\EsAddDeveloperRoleJob.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Jobs\Common\V1\Es\Develop;

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
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;

class EsAddDeveloperRoleJob implements ShouldQueue
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

        $indexName = config('common_es.indices.union.user_role_unions');

        $configKey = get_shard_config_key();

        $userRoleUnionCollection = UserRoleUnion::queryByShard($userObject->user_uid)->where('user_uid', $userObject->biz_id)->where('type', 10)->get();

        $insertDataArray = [];

        foreach ($userRoleUnionCollection as $userRoleUnionObject) {
            $insertDataArray[] = [
                '_docId' => $userRoleUnionObject->user_role_union_uid,
                'shard_key' => $userRoleUnionObject->shard_key,
                'shard_db' => ShardFacade::getDbName($userRoleUnionObject->user_uid, $configKey),
                'shard_table' => ShardFacade::getTableName($userRoleUnionObject->user_uid, 'user_role_unions', $configKey),
                'user_role_union_uid' => $userRoleUnionObject->user_role_union_uid,
                'user_uid' => $userRoleUnionObject->user_uid,
                'role_id' => $userRoleUnionObject->role_id,
                'type' => $userRoleUnionObject->type,
                'created_time' => $userRoleUnionObject->created_time,
                'updated_time' => $userRoleUnionObject->updated_time,
                'created_at' => $userRoleUnionObject->created_at,
                'updated_at' => $userRoleUnionObject->updated_at,
                'deleted_at' => $userRoleUnionObject->deleted_at,
            ];
        }

        $result = EsFacade::batchActDoc($indexName, $insertDataArray);

        plog(['info' => 'es批量添加管理员角色完成','$result' => $result], 'EsAddDeveloperRoleJob', 'handle');

        if (!isset($result['code']) || $result['code'] != 0) {
            plog(['error' => 'es批量添加管理员角色失败','$result' => $result,'$insertDataArray' => $insertDataArray], 'EsAddDeveloperRoleJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
