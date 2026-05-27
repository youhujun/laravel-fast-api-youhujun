<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-10 13:51:41
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-24 12:47:46
 * @FilePath: \youhu-laravel-api-12\app\Jobs\LaravelFastApi\V1\Admin\Article\PublishArticleJob.php
 */

namespace App\Jobs\LaravelFastApi\V1\Admin\Article;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Middleware\RateLimited;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\Article\Article;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

class PublishArticleJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected Article $articleObject;
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
    public function __construct(Admin $adminObject, Article $articleObject)
    {
        $this->adminObject = $adminObject->withoutRelations();
        $this->articleObject = $articleObject->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $articleObject = $this->articleObject;
        $adminObject = $this->adminObject;

        $indexName = config('common_es.indices.article.articles');
        try {
            //先改数据库
            $updateDataArray = [
                'status' => 10
            ];

            $updateResult = $articleObject->updateWithShard($updateDataArray);

            if ($updateResult) {
                plog(['info' => '文章自动发布成功','status' => 'success','$articleObject' => $articleObject], 'PublishArticleJob', 'handle');
                //es同步
                $indexName = config('common_es.indices.article.articles');

                $esResult = EsFacade::updateDoc($indexName, $articleObject->biz_id, $updateDataArray);

                if (!isset($esResult['code']) || $esResult['code'] != 0) {
                    plog(['error' => 'es更新文章失败','$articleObject' => $articleObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'PublishArticleJob', 'handleError');
                }
            } else {
                plog(['info' => '文章自动发布失败','status' => 'fail','$articleObject' => $articleObject], 'PublishArticleJob', 'handle');
            }
        } catch (\Throwable $th) {
            plog(['error' => $th,'status' => 'fail','$articleObject' => $articleObject], 'PublishArticleJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
