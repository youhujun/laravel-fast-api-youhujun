<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-31 15:27:03
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-23 11:14:28
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

use App\Services\Facade\Traits\V1\QueryService;

use App\Exceptions\Admin\CommonException;

use App\Events\Admin\CommonEvent;

use App\Models\LaravelFastApi\V1\System\Platform\SystemWechatConfig;

use App\Http\Resources\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigCollection;


use App\Facades\Pub\Excel\ExcelFacade;

/**
 * @see \App\Facades\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigFacade
 */
class SystemWechatConfigFacadeService
{
   public function test()
   {
       echo "SystemWechatConfigFacadeService test";
   }

   use QueryService;

   protected static $sort = [
      1 => ['created_time','asc'],
      2 => ['created_time','desc'],
    ];

   protected static $searchItem = [
        'name',
		'appid'
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
                    'mysql_SystemWechatConfig_name'=> $value[0],
                    'mysql_SystemWechatConfig_code'=> empty($value[1])?null:$value[1],
                    'is_default' => empty($value[2])?0:$value[2],
                    'sort' => empty($value[3])?100:$value[3]
                ];
            }

            $result = SystemWechatConfig::insert($insertData);

       }

       return $result;
   }

    /**
     * 导出表格数据
     *
     * @param [type] $systemWechatConfigObjectList
     * @return void
     */
    protected function exportData($systemWechatConfigObjectList)
    {
        $cloumn = [['列名一','列名二','列名三','列名四']];

        $data = [];

        foreach ($systemWechatConfigObjectList as $key => $value)
        {
           $list = [];

           $list[] = $value->mysql_SystemWechatConfig_name;
           $list[] = $value->mysql_SystemWechatConfig_code;
           $list[] = $value->is_dfault == 1?'是':'否';
           $list[] = $value->created_at;

           $data[] =  $list;
        }

        $title = "标题名称";

        ExcelFacade::exportExcelData($cloumn, $data,$title,1);

        return $title;

    }


    /**
     * 查询
     *
     * @param [type] $validated
     * @return void
     */
    public function getSystemWechatConfig($validated,$admin)
    {

       $result = code(config('admin_code.GetSystemWechatConfigError'));

       $this->setQueryOptions($validated);

       $query = SystemWechatConfig::query();

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
                $systemWechatConfigObjectList = null;

                $title = '';
                //本页数据
                if($validated['exportType'] == 1)
                {
                    $systemWechatConfigObjectList = $query->offset(($this->page - 1) * $this->perPage)->limit($this->perPage)->get();

                    $title = $this->exportData($systemWechatConfigObjectList);
                }

                if($validated['exportType'] == 2)
                {
                    $systemWechatConfigObjectList = $query->get();

                    $title = $this->exportData($systemWechatConfigObjectList);
                }

                $exists = Storage::disk('public')->exists("excel/{$title}.xlsx");

                if($exists)
                {
                    //return response()->download(public_path("storage/excel/{$title}.xlsx"));

                   $download = asset("storage/excel/{$title}.xlsx");
                }
            }
        }

        $systemWechatConfigObjectList = $query->paginate($this->perPage,$this->columns,$this->pageName,$this->page);

        if(\optional($systemWechatConfigObjectList))
        {
            $result = new SystemWechatConfigCollection($systemWechatConfigObjectList,['code'=>0,'msg'=>'获取系统微信配置列表成功!'],$download);
            //如果要增加其他返回参数,需要在SystemWechatConfigCollection处理
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
    public function addSystemWechatConfig($validated,$admin)
    {
        $result = code(config('admin_code.AddSystemWechatConfigError'));

        $systemWechatConfigObject = new SystemWechatConfig;

        if(!count($validated))
        {
            throw new CommonException('ParamsIsNullError');
        }

        foreach ( $validated as $key => $value)
        {

            if(isset($value))
            {
                $systemWechatConfigObject->$key = $value;
            }


        }

        $systemWechatConfigObject->created_time = time();
        $systemWechatConfigObject->created_at = time();

        $systemWechatConfigObjectResult = $systemWechatConfigObject->save();

        if(!$systemWechatConfigObjectResult)
        {
            throw new CommonException('AddSystemWechatConfigError');
        }

        CommonEvent::dispatch($admin,$validated,'AddSystemWechatConfig');

        $result = code(['code'=>0,'msg'=>'添加系统微信配置成功']);

        return $result;
    }


    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function updateSystemWechatConfig($validated,$admin)
    {
        $result = code(config('admin_code.UpdateSystemWechatConfigError'));

        if(!count($validated))
        {
            throw new CommonException('ParamsIsNullError');
        }

        $systemWechatConfigObject = SystemWechatConfig::find($validated['id']);

        if(!$systemWechatConfigObject)
        {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateData = [];

        $where[] = ['revision','=',$systemWechatConfigObject ->revision];

        foreach ( $validated as $key => $value)
        {
            if($key === 'id')
            {
                $where[] = ['id','=',$value];
                continue;
            }

            if(isset($value))
            {
                $updateData[$key] = $value;
            }
        }

        $updateData['revision'] = $systemWechatConfigObject ->revision + 1;

        $updateData['updated_time'] = time();

        $updateData['updated_at']  = date('Y-m-d H:i:s',time());

        $systemWechatConfigObjectResult = SystemWechatConfig::where($where)->update($updateData);

        if(!$systemWechatConfigObjectResult)
        {
           throw new CommonException('UpdateSystemWechatConfigError');
        }

        CommonEvent::dispatch($admin,$systemWechatConfigObject,'UpdateSystemWechatConfig');

        $result = code(['code'=>0,'msg'=>'更新系统微信配置成功']);

        return $result;
    }


    /**
     * 删除
     *
     * @param [type] $id
     * @param [type] $admin
     * @return void
     */
    public function deleteSystemWechatConfig($validated,$admin)
    {
        //删除
        $result = code(config('admin_code.RestoreSystemWechatConfigError'));

        $eventName = 'RestoreSystemWechatConfig';

        if($validated['is_delete'])
        {
            $result = code(config('admin_code.DeleteSystemWechatConfigError'));

            $eventName = 'DeleteSystemWechatConfig';
        }

        if($validated['is_delete'])
        {
            $systemWechatConfigObject = SystemWechatConfig::find($validated['id']);

            if(!$systemWechatConfigObject)
            {
                throw new CommonException('ThisDataNotExistsError');
            }

            $systemWechatConfigObjectResult =  $systemWechatConfigObject->delete();
        }
        else
        {
            //恢复
            $systemWechatConfigObject = SystemWechatConfig::withTrashed()->find($validated['id']);

            if(!$systemWechatConfigObject)
            {
                throw new CommonException('ThisDataNotExistsError');
            }

            $systemWechatConfigObjectResult =  $systemWechatConfigObject->restore();

        }

        if(!$systemWechatConfigObjectResult )
        {
            if($validated['is_delete'])
            {
                 throw new CommonException('DeleteSystemWechatConfigError');
            }

            throw new CommonException('RestoreSystemWechatConfigError');

        }

        CommonEvent::dispatch($admin,$validated['id'],$eventName);

        $result = code(['code'=>0,'msg'=>'恢复系统微信配置成功']);

        if($validated['is_delete'])
        {
             $result = code(['code'=>0,'msg'=>'删除系统微信配置成功']);
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
    public function multipleDeleteSystemWechatConfig($validated,$admin)
    {

        if(isset($validated['selectId']) && count($validated['selectId']))
        {
            $result = code(config('admin_code.MultipleRestoreSystemWechatConfigError'));

            $eventName = 'MultipleRestoreSystemWechatConfig';

            if($validated['is_delete'])
            {
                $result = code(config('admin_code.MultipleDeleteSystemWechatConfigError'));

                $eventName = 'MultipleDeleteSystemWechatConfig';
            }

            //批量删除
            if($validated['is_delete'])
            {
                 $deleteResult = SystemWechatConfig::whereIn('id',$validated['selectId'])->delete();
            }
            else
            {
                $deleteResult = SystemWechatConfig::withTrashed()->whereIn('id',$validated['selectId'])->restore();
            }

            if(!$deleteResult)
            {
                if($validated['is_delete'])
                {
                    throw new CommonException('MultipleDeleteSystemWechatConfigError');
                }

                throw new CommonException('MultipleRestoreSystemWechatConfigError');
            }

            CommonEvent::dispatch($admin,$validated,$eventName);

            $result = code(['code'=>0,'msg'=>'批量恢复系统微信配置成功']);

            if($validated['is_delete'])
            {
                $result = code(['code'=>0,'msg'=>'批量删除系统微信配置成功']);
            }

        }

        return $result;
    }
}
