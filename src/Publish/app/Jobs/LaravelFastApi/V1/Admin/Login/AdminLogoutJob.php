<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-05 15:30:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-09 03:04:32
 * @FilePath: \youhu-laravel-api-12\app\Jobs\LaravelFastApi\V1\Admin\Login\AdminLogoutJob.php
 */

namespace App\Jobs\LaravelFastApi\V1\Admin\Login;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use App\Jobs\Middleware\RateLimited;
use App\Models\LaravelFastApi\V1\Admin\Admin;

class AdminLogoutJob implements ShouldQueue
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
            if ($adminObject->remember_token) {
                $result = 0;

                $this->clearAdminCache($adminObject, $adminObject->remember_token);

                $adminObject->remember_token = null;

                $result = $adminObject->save();

                if ($result) {
                    plog(['info' => 'AdminLogoutJobSuccess','msg' => '自动退出成功'], 'AdminLogoutJob', 'handle');
                    $indexName = config('common_es.indices.user.admins');

                    $updateDataArray = [
                        'remember_token' => null,
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ];

                    $esResult = EsFacade::updateDoc($indexName, $adminObject->biz_id, $updateDataArray);

                    if (!isset($esResult['code']) || $esResult['code'] != 0) {
                        plog(['error' => '更新管理员remember_token失败','$esResult' => $esResult], 'AdminLogoutJob', 'handleError');
                    }
                } else {
                    plog(['error' => 'AdminLogoutJobError','msg' => '自动退出失败'], 'AdminLogoutJob', 'handleError');
                }
            }
        } catch (\Throwable $th) {
            // em($th, true);
            plog(['info' => 'AdminLogoutJobExcetion','msg' => '自动退出异常','error' => $th], 'AdminLogoutJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }

    /**
     * 清除管理员缓存
     *
     * @param  Admin  $adminObject
     * @param  [String] $token
     */
    private function clearAdminCache(Admin $adminObject, string $token): void
    {
        $redisAdminTokenKey = config('common_redis.admin_token.key');
        $redisAdminKey = config('common_redis.admin.key');
        $redisAdminInfoKey = config('common_redis.admin_info.key');

        $redisAdminField = config('common_redis.admin.field');
        $redisAdminInfoField = config('common_redis.admin_info.field');


        Redis::del($redisAdminTokenKey.$token);
        Redis::hdel($redisAdminKey, $redisAdminField.$adminObject->biz_id);
        Redis::hdel($redisAdminInfoKey, $redisAdminInfoField.$adminObject->biz_id);
    }
}
