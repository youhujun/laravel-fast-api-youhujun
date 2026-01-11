<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-10-15 19:03:19
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-10-15 19:19:03
 * @FilePath: \app\Jobs\TestJob.php
 */

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

use App\Jobs\Middleware\RateLimited;

use App\Models\Admin\Admin;
use App\Models\User\User;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $admin;
    protected $user;

	protected $testArray;
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
    public function __construct($testArray)
    {
        $this->testArray = $testArray;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug(['测试队列']);

		$testArray = $this->testArray;

        Log::debug(['$testArray'=>$testArray]);
    }

    public function middleware()
    {
         return [new RateLimited];
    }
}
