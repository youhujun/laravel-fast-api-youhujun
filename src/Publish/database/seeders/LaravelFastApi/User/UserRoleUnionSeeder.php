<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-16 09:26:18
 * @FilePath: \database\seeders\LaravelFastApi\User\UserRoleUnionSeeder.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace Database\Seeders\LaravelFastApi\User;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class UserRoleUnionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //先获取所有的角色id
        $roleCollection = DB::table('role')->select(['id'])->orderBy('id','asc')->get();

        //角色id容器
        $roleIdArray = [];
        //开发者
        $developRoleUninData = [];
        //超级管理员
        $superRoleUninData = [];

        foreach ($roleCollection as $key => $value)
        {
           $roleIdArray[] = $value->id;

           $developRoleUninData[] = [
                'created_time' => time(),
                'created_at' => date('Y-m-d H:i:s',\time()),
                'user_id' => 1,
                'role_id'=>$value->id
           ];

           $superRoleUninData[] =  [
                'created_time' => time(),
                'created_at' => date('Y-m-d H:i:s',\time()),
                'user_id' => 2,
                'role_id'=>$value->id
           ];

        }

        $userRoleUnionData = [
            ['user_id'=>3,'role_id'=>30,'created_time'=>time()],
            ['user_id'=>3,'role_id'=>40,'created_time'=>time()],
            ['user_id'=>4,'role_id'=>40,'created_time'=>time()],

        ];

        DB::table('user_role_union')->insert($developRoleUninData);
        DB::table('user_role_union')->insert($superRoleUninData);
        DB::table('user_role_union')->insert($userRoleUnionData);
    }
}
