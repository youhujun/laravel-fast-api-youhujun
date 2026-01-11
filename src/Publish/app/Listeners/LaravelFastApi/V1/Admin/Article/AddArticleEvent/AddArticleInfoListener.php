<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-10 13:11:17
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-12 19:15:06
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent\AddArticleInfoListener.php
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\Article\ArticleInfo;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\Article\AddArticleEvent
 */
class AddArticleInfoListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $admin = $event->admin;
        $validated = $event->validated;
        $article = $event->article;
        $isTransation = $event->isTransation;

         //添加文章详情
        $articleInfo = new ArticleInfo();

        $articleInfo->created_at = \time();

        $articleInfo->created_time = \time();

        $articleInfo->article_id = $article->id;

        $articleInfo->article_info = $validated['content'];

        $articleInfoResult =  $articleInfo->save();

        if(!$articleInfoResult)
        {
            if($isTransation)
            {
                DB::rollBack();
            }

            throw new CommonException('AddArticleInfoError');
        }

    }
}
