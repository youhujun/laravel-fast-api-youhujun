<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-06 16:41:20
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\Service\Group\ArticleCategory\CategorySeeder.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\Service\Group\ArticleCategory;

use App\Models\LaravelFastApi\V1\System\Module\Article\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::truncate();

        $this->command->info('开始填充系统文章分类');

        Category::create([
            'id' => 10,
            'parent_id' => 0,
            'deep' => 1,
            'switch' => 1,
            'category_name' => '公告通知',
            'category_code' => 'notice',
            'category_picture_uid' => '',
            'note' => '',
            'sort' => 100,
            'created_time' => time(),
        ]);

        Category::create([
            'id' => 20,
            'parent_id' => 10,
            'deep' => 2,
            'switch' => 1,
            'category_name' => '内部通知',
            'category_code' => 'inside_notice',
            'category_picture_uid' => '',
            'note' => '',
            'sort' => 100,
            'created_time' => time(),
        ]);

        Category::create([
            'id' => 30,
            'parent_id' => 10,
            'deep' => 2,
            'switch' => 1,
            'category_name' => '外部公告',
            'category_code' => 'open_notice',
            'category_picture_uid' => '',
            'note' => '',
            'sort' => 100,
            'created_time' => time(),
        ]);

        Category::create([
            'id' => 40,
            'parent_id' => 0,
            'deep' => 1,
            'switch' => 1,
            'category_name' => '系统文章',
            'category_code' => 'system_article',
            'category_picture_uid' => '',
            'note' => '',
            'sort' => 100,
            'created_time' => time(),
        ]);


        $this->command->info('✅填充系统文章分类完成');
    }
}
