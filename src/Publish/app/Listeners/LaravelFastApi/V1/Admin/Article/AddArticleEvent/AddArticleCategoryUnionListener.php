<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 14:11:59
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent\AddArticleCategoryUnionListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\Article\Union\ArticleCategoryUnion;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\Article\AddArticleEvent
 */
class AddArticleCategoryUnionListener
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

          //添加文章分类关联
        $articleCategoryUnion = new ArticleCategoryUnion();

        $categoryData = [];

        foreach($validated['categoryArray'] as $key => $value)
        {
            $categoryData[] = ['created_at'=>date('Y-m-d H:i:s',time()),'created_time'=>time(),'article_id'=>$article->id,'category_id'=>$value];
        }

        $articleCategoryUnionResult = $articleCategoryUnion->insert($categoryData);

        if(!$articleCategoryUnionResult)
        {
            if($isTransation)
            {
                DB::rollBack();
            }

            throw new CommonException('AddArticleCategoryError');
        }
    }
}
