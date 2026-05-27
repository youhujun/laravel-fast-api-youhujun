<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-05 16:00:47
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-06 01:47:20
 * @FilePath: \youhu-laravel-api-12\app\Jobs\Middleware\RateLimited.php
 */

namespace App\Jobs\Middleware;

use Illuminate\Support\Facades\Redis;

class RateLimited
{
    /**
     * 让队列任务慢慢执行
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        $key = 'rate_limit_' . get_class($job);

        $block_number = (int)config('common_redis.job.block_number');
        $allow_number = (int)config('common_redis.job.allow_number');
        $every_number = (int)config('common_redis.job.every_number');

        Redis::throttle($key)
            ->block($block_number)->allow($allow_number)->every($every_number)
            ->then(function () use ($job, $next) {
                // 获取锁 ...
                $next($job);
            }, function () use ($job) {
                // 无法获取锁 ...

                $job->release(2);
            });
    }
}
