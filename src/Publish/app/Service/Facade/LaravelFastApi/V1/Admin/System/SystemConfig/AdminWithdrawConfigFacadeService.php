<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-11-28 14:42:33
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-28 15:51:49
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminWithdrawConfigFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Service\Facade\LaravelFastApi\V1\Admin\System\SystemConfig;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

use App\Service\Facade\Traits\V1\QueryService;

use App\Exceptions\Admin\CommonException;

use App\Events\Admin\CommonEvent;

use App\Models\LaravelFastApi\V1\System\SystemWithdrawConfig;

use App\Http\Resources\LaravelFastApi\V1\Admin\System\SystemConfig\Withdraw\SystemWithdrawConfigResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\System\SystemConfig\Withdraw\SystemWithdrawConfigCollection;

use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;



/**
 * @see \App\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminWithdrawConfigFacade
 */
class AdminWithdrawConfigFacadeService
{
   public function test()
   {
       echo "AdminWithdrawConfigFacadeService test";
   }

   use QueryService;

   protected static $sort = [
      1 => ['created_time','asc'],
      2 => ['created_time','desc'],
    ];

   protected static $searchItem = [
        'comment'
    ];

   protected static $storage_public_path = DIRECTORY_SEPARATOR.'app'. DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;


   /**
	* 获取提现配置
	*
	* @param  [type] $validated
	* @param  [type] $admin
	*/
   public function getWithdrawConfig($validated,$admin)
   {
		$result = code(config('admin_code.GetWithdrawConfigError'));

		$withdrawConfigCollection = SystemWithdrawConfig::all();

		//p($withdrawConfigCollection);die;

		$result = new SystemWithdrawConfigCollection($withdrawConfigCollection,['code'=>0,'msg'=>'获取系统提现配置成功!']);

		return $result;
   }

   /**
	* 修改提现配置
    */
   public function updateWithdrawConfig($validated,$admin)
   {
		$result = code(config('admin_code.UpdateWithdrawConfigError'));

		$withdrawConfigObject = SystemWithdrawConfig::find($validated['id']);

		if(!$withdrawConfigObject)
		{
			throw new CommonException('ThisDataNotExistsError');
		}

		$where = [];

        $updateData = [];

        $where[] = ['revision','=',$withdrawConfigObject ->revision];
        $where[] = ['id','=',$withdrawConfigObject ->id];

		$updateData['revision'] = $withdrawConfigObject ->revision + 1;

        $updateData['updated_time'] = time();

        $updateData['updated_at']  = date('Y-m-d H:i:s',time());

        $updateData['item_value']  = $validated['item_value'];

		if($withdrawConfigObject->value_type == 20){

			$updateData['item_value'] = bcdiv($validated['item_value'],100,2);
		}


		$updateResult = SystemWithdrawConfig::where($where)->update($updateData);

		if(!$updateResult){
			throw new CommonException('UpdateWithdrawConfigError');
		}

		
		CommonEvent::dispatch($admin,$validated,'UpdateWithdrawConfig');

        $result = code(['code'=>0,'msg'=>'修改系统提现配置成功']);

        return $result;
	
   }
}
