<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-03-14 15:05:44
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-21 15:15:57
 */

namespace App\Service\Facade;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

use App\Service\Facade\Traits\V1\QueryService;

use App\Exceptions\Phone\CommonException;

use App\Events\Phone\CommonEvent;

use App\Models\Replace;

use App\Http\Resources\YouHuShop\V1\Phone\Goods\GoodsResource;
use App\Http\Resources\YouHuShop\V1\Phone\Goods\GoodsCollection;

use App\Facade\Pub\Excel\ExcelFacade;

class ReplacePhoneListService
{

   public function test()
   {
       echo "ReplaceService test";
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
    * 批量导入数据
    *
    * @param UploadFileLog $uploadFileLog
    * @return void
    */
   public function importData($path)
   {
       $result = 0;

       $exists = Storage::disk('public')->exists($path);

       if($exists)
       {
            ExcelFacade::initReadExcel(storage_path(self::$storage_public_path.$path));

            ExcelFacade::setWorkSheet(0);

            $excelData = ExcelFacade::getDataByRow();

            array_shift($excelData);

            $insertData = [];

            foreach ($excelData as $key => $value)
            {
                $insertData[] =
                [
                    'mysql_replace_name'=> $value[0],
                    'mysql_replace_code'=> empty($value[1])?null:$value[1],
                    'is_default' => empty($value[2])?0:$value[2],
                    'sort' => empty($value[3])?100:$value[3]
                ];
            }

            $result = Replace::insert($insertData);

       }

       return $result;
   }

    /**
     * 导出表格数据
     *
     * @param [type] $replaceList
     * @return void
     */
    protected function exportData($replaceList)
    {
        $cloumn = [['列名一','列名二','列名三','列名四']];

        $data = [];

        foreach ($replaceList as $key => $value)
        {
           $list = [];

           $list[] = $value->mysql_replace_name;
           $list[] = $value->mysql_replace_code;
           $list[] = $value->is_dfault == 1?'是':'否';
           $list[] = $value->created_at;

           $data[] =  $list;
        }

        $title = "标题名称";

        ExcelFacade::exportExcelData($cloumn, $data,$title,1);

        return $title;

    }

    /**
     * 获取常用
     *
     * @param [type] $user
     * @return void
     */
    public function defaultReplace($validated,$admin)
    {
        $result = code(config('phone_code.DefaultReplaceError'));

        $this->where[] = ['is_default_option','=',1];

        $query = Replace::where($this->where)->orderBy('sort','desc')->orderBy('created_at','asc')->get();

		$replaceCollection = [];

		if(isset($validate['limit']))
		{
			$replaceCollection = $query->limit($validate['limit'])->get();
		}
		else
		{
			$replaceCollection = $query->limit(10)->get();
		}

        if(!optional($replaceCollection))
        {
            throw new CommonException('DefaultReplaceError');
        }

        $data['data'] = ReplaceResource::collection($replaceCollection);

        $result = code(['code'=>0,'msg'=>'获取默认选项成功'],$data);

        return  $result;

    }

    /**
     * 搜索查找选项
     *
     * @param [type] $find
     * @return void
     */
    public function findReplace($validated,$admin)
    {
       $replaceCollection = [];

		if(isset($validated['find']))
		{
			$query = Replace::where('replace_name','like',"%{$validated['find']}%")->orWhere('replace_code','like',"%{$validated['find']}%");

			if(isset($validated['replace_type']))
			{
				$query->where('replace_type',$validated['replace_type']);
			}

			$query->orderBy('sort','desc')->orderBy('created_at','asc');
		}
		else
		{
			$query = Replace::query();

			if(isset($validated['replace_type']))
			{
				$query->where('replace_type',$validated['replace_type']);
			}
			
			$query->orderBy('sort','desc')->orderBy('created_at','asc')->limit(10);
		}

       	$replaceCollection = $query->get();

        if(!optional($replaceCollection))
        {
            throw new CommonException('FindReplaceError');
        }

        $data['data'] = ReplaceResource::collection($replaceCollection);

        $result = code(['code'=>0,'msg'=>'查找属性名默认选项成功'],$data);

        return  $result;
    }

    /**
     * 查询
     *
     * @param [type] $validated
     * @return void
     */
    public function getReplace($validated,$admin)
    {

       $result = code(config('phone_code.GetReplaceError'));

       $this->setQueryOptions($validated);

       $query =Replace::query();

       //是否删除
       if(isset($validated['is_delete']) && $validated['is_delete'])
       {
            $query->onlyTrashed();
       }

       if(isset($validated['findSelectIndex']))
       {
            if(isset($validated['find']) && !empty($validated['find']))
            {
               $this->where[] = [self::$searchItem[$validated['findSelectIndex']],'like',"%{$validated['find']}%"];

               $query =$query->where($this->where);
            }
       }

        if(isset($validated['timeRange']) && \count($validated['timeRange']) > 0)
        {
            if(isset($validated['timeRange'][0]) && !empty($validated['timeRange'][0]))
            {
                $this->whereBetween[] = [\strtotime($validated['timeRange'][0])];
            }

            if(isset($validated['timeRange'][1]) && !empty($validated['timeRange'][1]))
            {
                $this->whereBetween[] = [\strtotime($validated['timeRange'][1])];
            }

            if(isset($this->whereBetween) && count($this->whereBetween) > 0)
            {
                $query->whereBetween('created_time',$this->whereBetween);
            }

        }

        if(isset($validated['sortType']))
        {
            $sortType = $validated['sortType'];

            $query->orderBy(self::$sort[$sortType][0],self::$sort[$sortType][1]);
        }

        $download = null;

        //判断是否需要导出数据
        if(isset($validated['isExport']) && $validated['isExport'] == 1)
        {
            if(isset($validated['exportType']))
            {
                $replaceList = null;

                $title = '';
                //本页数据
                if($validated['exportType'] == 1)
                {
                    $replaceList = $query->offset(($this->page - 1) * $this->perPage)->limit($this->perPage)->get();

                    $title = $this->exportData($replaceList);
                }

                if($validated['exportType'] == 2)
                {
                    $replaceList = $query->get();

                    $title = $this->exportData($replaceList);
                }

                $exists = Storage::disk('public')->exists("excel/{$title}.xlsx");

                if($exists)
                {
                    //return response()->download(public_path("storage/excel/{$title}.xlsx"));

                   $download = asset("storage/excel/{$title}.xlsx");
                }
            }
        }

        $replaceList = $query->paginate($this->perPage,$this->columns,$this->pageName,$this->page);

        if(\optional($replaceList))
        {
            $result = new ReplaceCollection($replaceList,['code'=>0,'msg'=>'获取成功!'],$download);
            //如果要增加其他返回参数,需要在ReplaceCollection处理
        }

        return  $result;
    }

    /**
     * 添加
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function addReplace($validated,$admin)
    {
        $result = code(config('phone_code.AddReplaceError'));

        $replace = new Replace;

        if(!count($validated))
        {
            throw new CommonException('ParamsIsNullError');
        }

        foreach ( $validated as $key => $value)
        {
             if(is_array($value))
            {
                if(count($value))
                {
                    $value = $value[0];
                }
                else
                {
                    $value = 0;
                }
            }

            if(isset($value))
            {
                $replace->$key = $value;
            }


        }

        $replace->created_time = time();
        $replace->created_at = time();

        $replaceResult = $replace->save();

        if(!$replaceResult)
        {
             throw new CommonException('AddReplaceError');
        }

        CommonEvent::dispatch($admin,$replace,'AddReplace');

        $result = code(['code'=>0,'msg'=>'成功']);

        return $result;
    }


    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function updateReplace($validated,$admin)
    {
        $result = code(config('phone_code.UpdateReplaceError'));

        if(!count($validated))
        {
            throw new CommonException('ParamsIsNullError');
        }

        $replace = Replace::find($validated['id']);

        if(!$replace)
        {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateData = [];

        $where[] = ['revision','=',$replace ->revision];

        foreach ( $validated as $key => $value)
        {
            if($key === 'id')
            {
                $where[] = ['id','=',$value];
                continue;
            }

             if(is_array($value))
            {
                if(count($value))
                {
                    $value = $value[0];
                }
                else
                {
                    $value = 0;
                }
            }

            if(isset($value))
            {
                $updateData[$key] = $value;
            }
        }

        $updateData['revision'] = $replace ->revision + 1;

        $updateData['updated_time'] = time();

        $updateData['updated_at']  = date('Y-m-d H:i:s',time());

        $replaceResult = Replace::where($where)->update($updateData);

        if(!$replaceResult)
        {
           throw new CommonException('UpdateReplaceError');
        }

        CommonEvent::dispatch($admin,$replace,'UpdateReplace');

        $result = code(['code'=>0,'msg'=>'成功']);

        return $result;
    }


    /**
     * 删除
     *
     * @param [type] $id
     * @param [type] $admin
     * @return void
     */
    public function deleteReplace($validated,$admin)
    {
        //删除
        $result = code(config('phone_code.RestoreReplaceError'));

        $eventName = 'RestoreReplace';

        if($validated['is_delete'])
        {
            $result = code(config('phone_code.DeleteReplaceError'));

            $eventName = 'DeleteReplace';
        }

        if($validated['is_delete'])
        {
            $replace = Replace::find($validated['id']);

            if(!$replace)
            {
                throw new CommonException('ThisDataNotExistsError');
            }

            $replaceResult =  $replace->delete();
        }
        else
        {
            //恢复
            $replace = Replace::withTrashed()->find($validated['id']);

            if(!$replace)
            {
                throw new CommonException('ThisDataNotExistsError');
            }

            $replaceResult =  $replace->restore();

        }

        if(!$replaceResult )
        {
            if($validated['is_delete'])
            {
                 throw new CommonException('DeleteReplaceError');
            }

            throw new CommonException('RestoreReplaceError');

        }

        CommonEvent::dispatch($admin,$validated['id'],$eventName);

        $result = code(['code'=>0,'msg'=>'成功']);

        if($validated['is_delete'])
        {
             $result = code(['code'=>0,'msg'=>'成功']);
        }

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function multipleDeleteReplace($validated,$admin)
    {

        if(isset($validated['selectId']) && count($validated['selectId']))
        {
            $result = code(config('phone_code.MultipleRestoreReplaceError'));

            $eventName = 'MultipleRestoreReplace';

            if($validated['is_delete'])
            {
                $result = code(config('phone_code.MultipleDeleteReplaceError'));

                $eventName = 'MultipleDeleteReplace';
            }

            //批量删除
            if($validated['is_delete'])
            {
                 $deleteResult = Replace::whereIn('id',$validated['selectId'])->delete();
            }
            else
            {
                $deleteResult = Replace::withTrashed()->whereIn('id',$validated['selectId'])->restore();
            }

            if(!$deleteResult)
            {
                if($validated['is_delete'])
                {
                    throw new CommonException('MultipleDeleteReplaceError');
                }

                throw new CommonException('MultipleRestoreReplaceError');
            }

            CommonEvent::dispatch($admin,$validated,$eventName);

            $result = code(['code'=>0,'msg'=>'成功']);

            if($validated['is_delete'])
            {
                $result = code(['code'=>0,'msg'=>'成功']);
            }

        }

        return $result;
    }

	/**
     * 更新单个属性
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function updateReplaceProperty($validated,$admin)
    {
        $result = code(config('phone_code.UpdateReplacePropertyError'));

        if(!count($validated))
        {
            throw new CommonException('ParamsIsNullError');
        }

        $replace = Replace::find($validated['id']);

        if(!$replace)
        {
            throw new CommonException('ThisDataNotExistsError');
        }

		if(!isset($replace->{$validated['property_name']}))
		{
			throw new CommonException('ThisDataPropertyNotExistsError');
		}

		$propertyArray = [
			'owner_type','check_status','auth_status','is_top','is_recommend','is_discount','goods_name','sale_price','market_price','cost_price','coin','deduct_coin','goods_star'
		];

		if(!in_array($validated['property_name'],$propertyArray))
		{
			throw new CommonException('ThisDataPropertyNoAllowError');
		}

        $where = [];

        $updateData = [];

        $where[] = ['revision','=',$replace ->revision];

        $updateData['revision'] = $replace ->revision + 1;

		$updateData[$validated['property_name']] = $validated['property_value'];

        $updateData['updated_time'] = time();

        $updateData['updated_at']  = date('Y-m-d H:i:s',time());

        $replaceResult = Replace::where($where)->update($updateData);

        if(!$replaceResult)
        {
           throw new CommonException('UpdateReplacePropertyError');
        }

        CommonEvent::dispatch($admin,$replace,'UpdateReplaceProperty');

        $result = code(['code'=>0,'msg'=>'成功']);

        return $result;
    }

    /**
     * 禁用
     *
     * @param [type] $id
     * @param [type] $admin
     * @return void
     */
    public function disableReplace($validated,$admin)
    {

        $result = code(config('phone_code.AbleReplaceError'));

        $eventName = 'AbleReplace';

        if($validated['switch'])
        {
            $result = code(config('phone_code.DisableReplaceError'));

            $eventName = 'DisableReplace';
        }

        $replace = Replace::find($validated['id']);

        $revision = $replace->revision;

        $updateData = [
            'updated_time'=>time(),
            'updated_at'=>\date('Y-m-d H:i:s',time()),
            'revision'=>$revision + 1,
            'switch'=>$validated['switch']
        ];

        $where = [];
        $where[] = ['id','=',$validated['id']];
        $where[] = ['revision','=',$revision];

        $replaceResult = Replace::where($where)->update($updateData);

        if(!$replaceResult)
        {
            if($validated['switch'])
            {
                throw new CommonException('DisableReplaceError');
            }

            throw new CommonException('AbleReplaceError');
        }

        CommonEvent::dispatch($admin,$validated,$eventName);

        $result = code(['code'=>0,'msg'=>'成功']);

        if($validated['switch'])
        {
             $result = code(['code'=>0,'msg'=>'成功']);
        }

        return $result;
    }

    /**
     * 批量禁用
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function multipleDisableReplace($validated,$admin)
    {

        if(isset($validated['selectId']) && count($validated['selectId']))
        {
            $result = code(config('phone_code.MultipleAbleReplaceError'));

            $eventName = 'MultipleAbleReplace';

            if($validated['switch'])
            {
                $result = code(config('phone_code.MultipleDisableReplaceError'));

                $eventName = 'MultipleDisableReplace';
            }

            $updateData = [
                'switch'=>$validated['switch'],
                'updated_time'=>time(),
                'updated_at'=>\date('Y-m-d H:i:s',time())
            ];

            $deleteResult = Replace::whereIn('id',$validated['selectId'])->update($updateData);

            if(!$deleteResult)
            {

                if($validated['switch'])
                {
                    throw new CommonException('MultipleDisableReplaceError');
                }

                throw new CommonException('MultipleAbleReplaceError');
            }

            CommonEvent::dispatch($admin,$validated,$eventName);

            $result = code(['code'=>0,'msg'=>'成功']);

            if($validated['switch'])
            {
                $result = code(['code'=>0,'msg'=>'成功']);
            }
        }

        return $result;
    }

    /**
     * 获取绑定关联地区
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function getReplaceUnionRegion($validated,$admin)
    {
          $result = code(config('phone_code.ReplaceUnionRegionError'));

          $eplaceRegionArray = ReplaceRegionUnion::where('replace_config_id',$validated['replace_config_id'])->pluck('region_id')->toArray();

          if(count($eplaceRegionArray) || count($eplaceRegionArray) == 0)
          {
            $data['data'] =$eplaceRegionArray;

            $result = code(['code'=>0,'msg'=>'成功'],$data);
          }

          return $result;
    }

    /**
     *更新绑定关联地区
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function updateReplaceUnionRegion($validated,$admin)
    {
         $result = code(config('phone_code.UpdateReplaceUnionRegionError'));

         if(count($validated['region_id_array']))
         {
             //先删除之前绑定的地区
            $eplaceRegionUnionNumber = ReplaceRegionUnion::where('replace_config_id',$validated['replace_config_id'])->get()->count();

            if( $eplaceRegionUnionNumber )
            {
                $eplaceRegionUnionBeforeResult = ReplaceRegionUnion::where('replace_config_id',$validated['replace_config_id'])->forceDelete();

                if(!$eplaceRegionUnionBeforeResult )
                {
                    throw new CommonException('UpdateReplaceUnionRegionError');
                }
            }

             $eplaceRegionUnionData = [];

             foreach ($validated['region_id_array'] as $key => $region_id)
             {
                $eplaceRegionUnionData[] = ['created_time'=>time(),'created_at'=>date('Y-m-d H:i:s',time()),'replace_config_id'=>$validated['replace_config_id'],'region_id'=>$region_id];
             }

             $eplaceRegionUnionResult =  ReplaceRegionUnion::insert($eplaceRegionUnionData);

             if(!$eplaceRegionUnionResult)
             {
                throw new CommonException('UpdateReplaceUnionRegionError');
             }

            CommonEvent::dispatch($admin,$validated,'UpdateReplaceUnionRegion');

            $result = code(['code'=>0,'msg'=>'成功']);

         }

        return  $result;
    }
}
