<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-12 23:05:24
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent\UpdateArticleCategoryUnionListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\Article\Union\ArticleCategoryUnion;

class UpdateArticleCategoryUnionListener
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

        $articleCategoryDeleteResult = ArticleCategoryUnion::where('article_id',$article->id)->delete();

        if(!$articleCategoryDeleteResult)
        {
            if($isTransation)
            {
                 DB::rollBack();
            }

            throw new CommonException('ResetArticleCategoryError');
        }

        $categoryData = [];

        foreach($validated['categoryArray'] as $key => $value)
        {
                $categoryData[] = ['created_at'=>date('Y-m-d H:i:s',time()),'created_time'=>time(),'article_id'=>$article->id,'category_id'=>$value];
        }

        $articleCategoryResult = ArticleCategoryUnion::insert($categoryData);

        if(!$articleCategoryResult)
        {
            if($isTransation)
            {
                 DB::rollBack();
            }

            throw new CommonException('UpdateArticleCategoryError');
        }
    }
}
