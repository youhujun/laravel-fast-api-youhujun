<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-04-29 18:45:46
 * @FilePath: \database\seeders\LaravelFastApi\Service\Group\Category\CategorySeeder.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace Database\Seeders\LaravelFastApi\Service\Group\Category;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['created_at'=>\date('Y-m-d H:i:s',time()),'created_time'=>time(),'sort'=>100,'parent_id'=>0,'deep'=>1,'category_name'=>'公告通知','category_code'=>'notice'],
            ['created_at'=>\date('Y-m-d H:i:s',time()),'created_time'=>time(),'sort'=>100,'parent_id'=>1,'deep'=>2,'category_name'=>'内部通知','category_code'=>'inside_notice'],
            ['created_at'=>\date('Y-m-d H:i:s',time()),'created_time'=>time(),'sort'=>100,'parent_id'=>1,'deep'=>2,'category_name'=>'外部公告','category_code'=>'open_notice'],
            ['created_at'=>\date('Y-m-d H:i:s',time()),'created_time'=>time(),'sort'=>100,'parent_id'=>0,'deep'=>1,'category_name'=>'系统文章','category_code'=>'system_article']
        ];

        DB::table('category')->insert($data);
    }
}
