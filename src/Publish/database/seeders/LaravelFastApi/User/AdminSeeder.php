<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-07-24 14:49:06
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-08 11:13:05
 * @FilePath: \database\seeders\LaravelFastApi\User\AdminSeeder.php
 */


namespace Database\Seeders\LaravelFastApi\User;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		// 关键：通过固定的 account_name 查询对应的 userId（因为你的用户有明确的账号名）
		$developId = DB::table('users')->where('account_name', 'develop')->value('userId');
		$superId = DB::table('users')->where('account_name', 'super')->value('userId');
		$adminId = DB::table('users')->where('account_name', 'admin')->value('userId');

        $adminData = [
            ['account_name'=>'develop','user_id'=>1,'userId'=>$developId,'created_at'=>date('Y-m-d H:i:s',time()),'password'=>Hash::make('abc321'),'created_time'=>time(),'switch'=>1],
            ['account_name'=>'super','user_id'=>2,'userId'=>$superId,'created_at'=>date('Y-m-d H:i:s',time()),'password'=>Hash::make('abc321'),'created_time'=>time(),'switch'=>1],
            ['account_name'=>'admin','user_id'=>3,'userId'=>$adminId,'created_at'=>date('Y-m-d H:i:s',time()),'password'=>Hash::make('abc321'),'created_time'=>time(),'switch'=>1],
        ];
		
        DB::table('admin')->insert($adminData);
    }
}
