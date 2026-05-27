<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-26 06:48:50
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-26 08:39:11
 * @FilePath: \youhu-laravel-api-12\app\Jobs\LaravelFastApi\V1\Admin\Logout\EsAdminLogoutJob.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Jobs\LaravelFastApi\V1\Admin\Logout;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Middleware\RateLimited;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Models\LaravelFastApi\V1\Admin\Admin;

class EsAdminLogoutJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
    public function __construct(Admin $adminObject)
    {
        $this->adminObject = $adminObject->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //执行逻辑
        $adminObject = $this->adminObject;

        try {
            //如果有说明没有执行退出

            $indexName = config('common_es.indices.user.admins');

            $updateDataArray = [
                'remember_token' => null,
                'updated_at' => date('Y-m-d H:i:s', time())
            ];

            $result = EsFacade::updateDoc($indexName, $adminObject->biz_id, $updateDataArray);

            if (!isset($result['code']) || $result['code'] != 0) {
                plog(['error' => '更新管理员remember_token失败','result' => $result], 'EsAdminLogoutJob', 'AsyncAdminRememberTokenError');
            }
        } catch (\Throwable $th) {
            // em($th, true);
            plog(['error' => '更新管理员remember_token失败','exception' => $th], 'EsAdminLoginJob', 'AsyncAdminRememberTokenException');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
