<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-03 16:48:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-22 17:05:50
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Phone\System\GoodsClass\PhoneGoodsClassFacadeService.php
 */

namespace App\Service\Facade\LaravelFastApi\V1\Phone\System\GoodsClass;

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

use App\Models\LaravelFastApi\V1\System\Module\Goods\GoodsClass;

use App\Http\Resources\LaravelFastApi\V1\Phone\System\Goods\GoodsClassResource;

/**
 * @see \App\Facade\Phone\System\GoodsClass\PhoneGoodsClassFacade
 */
class PhoneGoodsClassFacadeService
{
   public function test()
   {
       echo "PhoneGoodsClassFacadeService test";
   }

   use AlwaysService;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->init((new GoodsClass),'deep');
    }

    /**
	* 获取地区id
	*
	* @param  [type] $validated
	* @param  [type] $user
	*/
   public function getGoodsClassById($validated,$user)
   {
		$result = code(config('phone_code.GetGoodsClassByIdError'));

		if(!isset($validated['parent_id']))
		{
			throw new CommonException('GetGoodsClassByIdNoParentIdError');
		}

		$parent_id = $validated['parent_id'];

		$regionCollection = GoodsClass::where('parent_id',$parent_id)->get();

		$result = ['code'=>0,'msg'=>'获取分类成功','data'=>GoodsClassResource::collection($regionCollection)];

		return $result;

   }

   /**
	* 获取树形地区
	*
	* @param  [type] $validated
	* @param  [type] $user
	*/
   public function getTreeGoodsClass($validated,$user)
   {
		$result = code(config('phone_code.GetGoodsClassError'));

        $redisTreeGoodsClass = Redis::hget('system:config','treeGoodsClass');

        if($redisTreeGoodsClass)
        {
            $treeGoodsClass = json_decode($redisTreeGoodsClass,true);
        }
        else
        {
            $treeGoodsClass = $this->getTreeData(['picture']);

            $redisResult = Redis::hset('system:config','treeGoodsClass',json_encode($treeGoodsClass));
        }

        $data['data'] = [];

        if(count($treeGoodsClass))
        {
            $data['data'] = GoodsClassResource::collection($treeGoodsClass);
        }

        $result = code(['code'=>0,'msg'=>'获取树形产品分类成功'],$data);

        return  $result;
   }
}
