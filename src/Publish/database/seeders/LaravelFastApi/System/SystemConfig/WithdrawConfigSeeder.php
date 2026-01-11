<?php

namespace Database\Seeders\LaravelFastApi\System\SystemConfig;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WithdrawConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		//手续费
        $handingFeeData = [
			['item_name'=>'handing_fee_reason_amount','item_value'=>'500','value_type'=>10,'created_time'=>time(),'created_at'=>date('Y-m-d H:i:s',time()),'sort'=>100],
			['item_name'=>'handing_fee_use_amount','item_value'=>'5','value_type'=>10,'created_time'=>time(),'created_at'=>date('Y-m-d H:i:s',time()),'sort'=>100],
			['item_name'=>'handing_fee_use_percentage','item_value'=>'0.01','value_type'=>20,'created_time'=>time(),'created_at'=>date('Y-m-d H:i:s',time()),'sort'=>100],
		];

		DB::connection('mysql')->table('system_withdraw_config')->insert($handingFeeData);

		//个税
        $incomeTaxData = [
			['item_name'=>'income_tax_reason_amount','item_value'=>'5000','value_type'=>10,'created_time'=>time(),'created_at'=>date('Y-m-d H:i:s',time()),'sort'=>100],
			['item_name'=>'income_tax_use_percentage','item_value'=>'0.2','value_type'=>20,'created_time'=>time(),'created_at'=>date('Y-m-d H:i:s',time()),'sort'=>100],
		];

		DB::connection('mysql')->table('system_withdraw_config')->insert($incomeTaxData);
    }
}
