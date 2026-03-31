<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-05 15:30:00
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-13 15:48:41
 * @FilePath: \app\Jobs\LaravelFastApi\V1\Admin\Login\AdminLogoutJob.php
 */

namespace App\Jobs\LaravelFastApi\V1\Admin\Login;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

use App\Jobs\Middleware\RateLimited;

use App\Models\LaravelFastApi\V1\Admin\Admin;

class AdminLogoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $adminObject;
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
         $this->admin = $adminObject->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        //执行逻辑
        $adminObject = $this->admin;
		
        try
        {

            //如果有说明没有执行退出
            if ($adminObject->remember_token)
            {
                $result = 0;

                $this->clearAdminCache($adminObject, $adminObject->remember_token);

                $adminObject->remember_token = null;

                $result = $adminObject->save();

                if($result)
                {
					plog(['info'=>'AdminLogoutJobSuccess','msg'=>'自动退出成功'],'AdminLogoutJob','AdminLogoutJobSuccess');
                }
                else
                {
					plog(['info'=>'AdminLogoutJobError','msg'=>'自动退出失败'],'AdminLogoutJob','AdminLogoutJobError');
                }
            }

        }
        catch (\Throwable $th)
        {
           // em($th, true);
		   plog(['info'=>'AdminLogoutJobExcetion','msg'=>'自动退出异常','error'=>$th],'AdminLogoutJob','AdminLogoutJobExcetion');
        }
    }

    public function middleware()
    {
         return [new RateLimited];
    }

    /**
     * 清除管理员缓存
     *
     * @param  Admin  $adminObject
     * @param  [String] $token
     */
    private function clearAdminCache($adminObject, $token)
    {
        Redis::del("admin_token:{$token}");
        Redis::hdel("admin:admin",$adminObject->id);
        Redis::hdel("admin_info:admin_info",$adminObject->id);
    }
}
