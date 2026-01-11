<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-03 10:28:52
 * @FilePath: \database\seeders\LaravelFastApi\User\UserSeeder.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace Database\Seeders\LaravelFastApi\User;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$oneUserId = Str::uuid()->toString();
		$towUserId = Str::uuid()->toString();
		$threeUserId = Str::uuid()->toString();
		$foureUserId = Str::uuid()->toString();

        $userData = [
            ['userId'=>$oneUserId,'account_name'=>'develop','password'=>Hash::make('abc321'),'created_time'=>time(),'switch'=>1,'auth_token'=>Str::random(20),'source_user_id'=>0,'source_userId'=>'','real_auth_status'=>10,'invite_code'=>'0001'],
            ['userId'=>$towUserId,'account_name'=>'super','password'=>Hash::make('abc321'),'created_time'=>time(),'switch'=>1,'auth_token'=>Str::random(20),'source_user_id'=>1,'source_userId'=>$oneUserId,'real_auth_status'=>10,'invite_code'=>'0002'],
            ['userId'=>$threeUserId,'account_name'=>'admin','password'=>Hash::make('abc321'),'created_time'=>time(),'switch'=>1,'auth_token'=>Str::random(20),'source_user_id'=>2,'source_userId'=>$towUserId,'real_auth_status'=>10,'invite_code'=>'0003'],
            ['userId'=>$foureUserId,'account_name'=>'user','password'=>Hash::make('abc321'),'created_time'=>time(),'switch'=>1,'auth_token'=>Str::random(20),'source_user_id'=>3,'source_userId'=>$threeUserId,'real_auth_status'=>10,'invite_code'=>'0004'],
        ];

        DB::table('users')->insert($userData);
    }
}
