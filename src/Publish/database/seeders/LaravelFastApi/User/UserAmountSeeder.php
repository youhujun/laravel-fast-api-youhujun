<?php

namespace Database\Seeders\LaravelFastApi\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserAmountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		// 关键：通过固定的 account_name 查询对应的 userId（因为你的用户有明确的账号名）
        $developId = DB::table('users')->where('account_name', 'develop')->value('userId');
        $superId = DB::table('users')->where('account_name', 'super')->value('userId');
        $adminId = DB::table('users')->where('account_name', 'admin')->value('userId');
        $userId = DB::table('users')->where('account_name', 'user')->value('userId');

        $userAmountData = [
			['user_id'=>1,'userId'=>$developId,'created_time'=>time(),'created_at'=>date('Y-m-d H:i:s',time()),'sort'=>100],
			['user_id'=>2,'userId'=>$superId,'created_time'=>time(),'created_at'=>date('Y-m-d H:i:s',time()),'sort'=>100],
			['user_id'=>3,'userId'=>$adminId,'created_time'=>time(),'created_at'=>date('Y-m-d H:i:s',time()),'sort'=>100],
			['user_id'=>4,'userId'=>$userId,'created_time'=>time(),'created_at'=>date('Y-m-d H:i:s',time()),'sort'=>100],
		];

		DB::connection('mysql')->table('user_amount')->insert($userAmountData);
    }
}
