<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-14 15:27:06
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-28 01:31:07
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Platform\AdminPhoneBannerFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Services\Facade\LaravelFastApi\V1\Admin\Service\Platform;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Exceptions\Admin\CommonException;

use App\Events\Admin\CommonEvent;

use App\Services\Facade\Traits\V1\QueryService;

use App\Models\LaravelFastApi\V1\System\Phone\PhoneBanner;

use App\Http\Resources\LaravelFastApi\V1\Admin\Service\PhoneBanner\PhoneBannerCollection;

/**
 * @see \App\Facades\LaravelFastApi\V1\Admin\Service\Platform\AdminPhoneBannerFacade
 */
class AdminPhoneBannerFacadeService
{
   public function test()
   {
       echo "AdminPhoneBannerFacadeService test";
   }

    use QueryService;

    protected static $sort = [
      1 => ['created_time','asc'],
      2 => ['created_time','desc'],
    ];



    /**
     * 获取首页轮播图
     * @param {*} $validated
     * @param {*} $admin
     * @return {*}
     */
    public function getPhoneBanner($validated,$admin)
    {
        $result = code(config('admin_code.GetPhoneBannerError'));

        $this->setQueryOptions($validated);

        $query = PhoneBanner::query();

        if(isset($validated['sortType']))
        {
            $sortType = $validated['sortType'];

            $query->orderBy(self::$sort[$sortType][0],self::$sort[$sortType][1]);
        }

        $phoneBannerList = $query->paginate($this->perPage,$this->columns,$this->pageName,$this->page);

        if(\optional($phoneBannerList))
        {
            $result = new PhoneBannerCollection($phoneBannerList,['code'=>0,'msg'=>'后台获取手机轮播图列表成功']);
        }

        return  $result;
    }

    /**
    * @添加首页轮播图:
    * @param {*} $validated
    * @param {*} $admin
    * @return {*}
    */
   public function addPhoneBanner($validated,$admin)
   {
        $result = code(config('admin_code.AddPhoneBannerError'));

        $phoneBanner = new PhoneBanner;

        $phoneBanner->admin_id = $admin->id;

        foreach ( $validated as $key => $value)
        {
            if(\is_null($value))
            {
                $value = "";
            }

            $phoneBanner->$key = $value;
        }

        $phoneBanner->created_time = time();
        $phoneBanner->created_at = time();

        $phoneBannerResult = $phoneBanner->save();

        if(!$phoneBannerResult)
        {
            throw new CommonException('AddPhoneBannerError');
        }

        CommonEvent::dispatch($admin,$validated,'AddPhoneBanner');

        $result = code(['code'=>0,'msg'=>'添加轮播图成功!']);

        return $result;

   }


    /**
     * 更新轮播图
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function updatePhoneBanner($validated,$admin)
    {
        $result = code(config('admin_code.UpdatePhoneBannerError'));

        $phoneBanner = PhoneBanner::find($validated['id']);

        if(!optional($phoneBanner))
        {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateData = [];

        $where[] = ['revision','=',$phoneBanner ->revision];

        foreach ( $validated as $key => $value)
        {
            if($key === 'id')
            {
                $where[] = ['id','=',$value];
                continue;
            }

            if(\is_null($value))
            {
                $value = "";
            }

            $updateData[$key] = $value;
        }

        $phoneBanner->user_id = $admin->id;

        $updateData['revision'] = $phoneBanner ->revision + 1;

        $updateData['updated_at']  = date('Y-m-d H:i:s',time());

        $updateData['updated_time'] = time();

        $phoneBannerResult = PhoneBanner::where($where)->update($updateData);

        if(!$phoneBannerResult)
        {
           throw new CommonException('UpdatePhoneBannerError');
        }

        CommonEvent::dispatch($admin,$phoneBanner,'UpdatePhoneBanner');

        $result = code(['code'=>0,'msg'=>'修改轮播图成功!']);

        return $result;
    }

    /**
     * 删除首页轮播图
     * @param {*} $validated
     * @param {*} $admin
     * @return {*}
     */
    public function deletePhoneBanner( $validated,$admin)
    {
         //删除
         $result = code(config('admin_code.RestorePhoneBannerError'));

         $eventName = 'RestorePhoneBanner';

         if($validated['is_delete'])
         {
             $result = code(config('admin_code.DeletePhoneBannerError'));

             $eventName = 'DeletePhoneBanner';
         }

         if($validated['is_delete'])
         {
             $phoneBanner = PhoneBanner::find($validated['id']);

            if(!optional($phoneBanner))
            {
                throw new CommonException('ThisDataNotExistsError');
            }

             $phoneBanner->deleted_time = time();

             $phoneBanner->deleted_at = date('Y-m-d H:i:s',time());

             $phoneBannerResult =  $phoneBanner->save();
         }
         else
         {
             //恢复
             $phoneBanner = PhoneBanner::withTrashed()->find($validated['id']);

             $phoneBannerResult =  $phoneBanner->restore();

         }

         if(!$phoneBannerResult )
         {
             if($validated['is_delete'])
             {
                throw new CommonException('DeletePhoneBannerError');
             }

             throw new CommonException('RestorePhoneBannerError');
         }

         CommonEvent::dispatch($admin,$validated,$eventName);

         $result = code(['code'=>0,'msg'=>'恢复轮播图成功!']);

         if($validated['is_delete'])
         {
              $result = code(['code'=>0,'msg'=>'删除轮播图成功!']);
         }

         return $result;
    }

	/**
	 * 批量删除
	 *
	 * @param  [type] $validated
	 * @param  [type] $admin
	 */
	public function multipleDeletePhoneBanner( $validated,$admin)
	{
		$result = code(config('admin_code.MultipleDeletePhoneBannerError'));

        if(isset($validated['selectId']) && count($validated['selectId']))
        {
            $deleteResult = PhoneBanner::whereIn('id',$validated['selectId'])->delete();

            if(!$deleteResult)
            {
                throw new CommonException('MultipleDeletePhoneBannerError');
            }

            CommonEvent::dispatch($admin,$validated,'MultipleDeletePhoneBanner');

            $result = code(['code'=>0,'msg'=>'批量删除轮播图成功!']);
        }

        return $result;
	}
}
