<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-02 18:13:30
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-22 16:44:57
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Phone\System\Region\PhoneRegionFacadeService.php
 */

namespace App\Service\Facade\LaravelFastApi\V1\Phone\System\Region;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

use App\Service\Facade\Traits\V1\AlwaysService;
use App\Service\Facade\Traits\V1\QueryService;

use App\Exceptions\Phone\CommonException;

use App\Events\Phone\CommonEvent;

use App\Models\LaravelFastApi\V1\System\Region\Region;

use App\Http\Resources\LaravelFastApi\V1\Phone\System\RegionResource;

/**
 * @see \App\Facade\Phone\System\Region\PhoneRegionFacade
 */
class PhoneRegionFacadeService
{
   public function test()
   {
       echo "PhoneRegionFacadeService test";
   }

    use AlwaysService;

	 /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->init((new Region),'deep');
    }


   /**
	* 获取地区id
	*
	* @param  [type] $validated
	* @param  [type] $user
	*/
   public function getRegionById($validated,$user)
   {
		$result = code(config('phone_code.GetRegionByIdError'));

		if(!isset($validated['parent_id']))
		{
			throw new CommonException('GetRegionByIdNoParentIdError');
		}

		$parent_id = $validated['parent_id'];

		$regionCollection = Region::where('parent_id',$parent_id)->get();

		$result = ['code'=>0,'msg'=>'获取地区成功','data'=>RegionResource::collection($regionCollection)];

		return $result;

   }

   /**
	* 获取树形地区
	*
	* @param  [type] $validated
	* @param  [type] $user
	*/
   public function getTreeRegions($validated,$user)
   {
		$result = code(config('phone_code.GetTreeRegionError'));

        $redisTreeRegion = Redis::hget('system:config','treeRegions');

        if($redisTreeRegion)
        {
            $treeRegion = \json_decode($redisTreeRegion,true);
        }
        else
        {
            $treeRegion = $this->getTreeData();

            $redisResult = Redis::hset('system:config','treeRegions',convertToString($treeRegion));
        }

        RegionResource::showControl(1);

        $data['data'] = RegionResource::collection($treeRegion);

        $result = code(['code'=>0,'msg'=>'获取树形地区成功!'],$data);

        return  $result;
   }
}
