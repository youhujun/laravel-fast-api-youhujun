<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-31 15:42:03
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 18:03:16
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemDouyinConfigFacadeService.php
 */

namespace App\Service\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

use App\Service\Facade\Traits\V1\QueryService;

use App\Exceptions\Admin\CommonException;

use App\Events\Admin\CommonEvent;

use App\Models\LaravelFastApi\V1\System\Platform\SystemDouyinConfig;

use App\Http\Resources\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemDouyinConfigResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemDouyinConfigCollection;


use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;

/**
 * @see \App\Facade\Admin\System\SystemConfig\OtherPlatform\SystemDouyinConfigFacade
 */
class SystemDouyinConfigFacadeService
{
   public function test()
   {
       echo "SystemDouyinConfigFacadeService test";
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
                    'mysql_SystemDouyinConfig_name'=> $value[0],
                    'mysql_SystemDouyinConfig_code'=> empty($value[1])?null:$value[1],
                    'is_default' => empty($value[2])?0:$value[2],
                    'sort' => empty($value[3])?100:$value[3]
                ];
            }

            $result = SystemDouyinConfig::insert($insertData);

       }

       return $result;
   }

    /**
     * 导出表格数据
     *
     * @param [type] $systemDouyinConfigObjectList
     * @return void
     */
    protected function exportData($systemDouyinConfigObjectList)
    {
        $cloumn = [['列名一','列名二','列名三','列名四']];

        $data = [];

        foreach ($systemDouyinConfigObjectList as $key => $value)
        {
           $list = [];

           $list[] = $value->mysql_SystemDouyinConfig_name;
           $list[] = $value->mysql_SystemDouyinConfig_code;
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
    public function getSystemDouyinConfig($validated,$admin)
    {

       $result = code(config('admin_code.GetSystemDouyinConfigError'));

       $this->setQueryOptions($validated);

       $query = SystemDouyinConfig::query();

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
                $systemDouyinConfigObjectList = null;

                $title = '';
                //本页数据
                if($validated['exportType'] == 1)
                {
                    $systemDouyinConfigObjectList = $query->offset(($this->page - 1) * $this->perPage)->limit($this->perPage)->get();

                    $title = $this->exportData($systemDouyinConfigObjectList);
                }

                if($validated['exportType'] == 2)
                {
                    $systemDouyinConfigObjectList = $query->get();

                    $title = $this->exportData($systemDouyinConfigObjectList);
                }

                $exists = Storage::disk('public')->exists("excel/{$title}.xlsx");

                if($exists)
                {
                    //return response()->download(public_path("storage/excel/{$title}.xlsx"));

                   $download = asset("storage/excel/{$title}.xlsx");
                }
            }
        }

        $systemDouyinConfigObjectList = $query->paginate($this->perPage,$this->columns,$this->pageName,$this->page);

        if(\optional($systemDouyinConfigObjectList))
        {
            $result = new SystemDouyinConfigCollection($systemDouyinConfigObjectList,['code'=>0,'msg'=>'获取系统抖音配置列表成功!'],$download);
            //如果要增加其他返回参数,需要在SystemDouyinConfigCollection处理
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
    public function addSystemDouyinConfig($validated,$admin)
    {
        $result = code(config('admin_code.AddSystemDouyinConfigError'));

        $systemDouyinConfigObject = new SystemDouyinConfig;

        if(!count($validated))
        {
            throw new CommonException('ParamsIsNullError');
        }

        foreach ( $validated as $key => $value)
        {

            if(isset($value))
            {
                $systemDouyinConfigObject->$key = $value;
            }


        }

        $systemDouyinConfigObject->created_time = time();
        $systemDouyinConfigObject->created_at = time();

        $systemDouyinConfigObjectResult = $systemDouyinConfigObject->save();

        if(!$systemDouyinConfigObjectResult)
        {
            throw new CommonException('AddSystemDouyinConfigError');
        }

        CommonEvent::dispatch($admin,$validated,'AddSystemDouyinConfig');

        $result = code(['code'=>0,'msg'=>'添加系统抖音配置成功']);

        return $result;
    }


    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function updateSystemDouyinConfig($validated,$admin)
    {
        $result = code(config('admin_code.UpdateSystemDouyinConfigError'));

        if(!count($validated))
        {
            throw new CommonException('ParamsIsNullError');
        }

        $systemDouyinConfigObject = SystemDouyinConfig::find($validated['id']);

        if(!$systemDouyinConfigObject)
        {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateData = [];

        $where[] = ['revision','=',$systemDouyinConfigObject ->revision];

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

        $updateData['revision'] = $systemDouyinConfigObject ->revision + 1;

        $updateData['updated_time'] = time();

        $updateData['updated_at']  = date('Y-m-d H:i:s',time());

        $systemDouyinConfigObjectResult = SystemDouyinConfig::where($where)->update($updateData);

        if(!$systemDouyinConfigObjectResult)
        {
           throw new CommonException('UpdateSystemDouyinConfigError');
        }

        CommonEvent::dispatch($admin,$systemDouyinConfigObject,'UpdateSystemDouyinConfig');

        $result = code(['code'=>0,'msg'=>'更新系统抖音配置成功']);

        return $result;
    }


    /**
     * 删除
     *
     * @param [type] $id
     * @param [type] $admin
     * @return void
     */
    public function deleteSystemDouyinConfig($validated,$admin)
    {
        //删除
        $result = code(config('admin_code.RestoreSystemDouyinConfigError'));

        $eventName = 'RestoreSystemDouyinConfig';

        if($validated['is_delete'])
        {
            $result = code(config('admin_code.DeleteSystemDouyinConfigError'));

            $eventName = 'DeleteSystemDouyinConfig';
        }

        if($validated['is_delete'])
        {
            $systemDouyinConfigObject = SystemDouyinConfig::find($validated['id']);

            if(!$systemDouyinConfigObject)
            {
                throw new CommonException('ThisDataNotExistsError');
            }

            $systemDouyinConfigObjectResult =  $systemDouyinConfigObject->delete();
        }
        else
        {
            //恢复
            $systemDouyinConfigObject = SystemDouyinConfig::withTrashed()->find($validated['id']);

            if(!$systemDouyinConfigObject)
            {
                throw new CommonException('ThisDataNotExistsError');
            }

            $systemDouyinConfigObjectResult =  $systemDouyinConfigObject->restore();

        }

        if(!$systemDouyinConfigObjectResult )
        {
            if($validated['is_delete'])
            {
                 throw new CommonException('DeleteSystemDouyinConfigError');
            }

            throw new CommonException('RestoreSystemDouyinConfigError');

        }

        CommonEvent::dispatch($admin,$validated['id'],$eventName);

        $result = code(['code'=>0,'msg'=>'恢复系统抖音配置成功']);

        if($validated['is_delete'])
        {
             $result = code(['code'=>0,'msg'=>'删除系统抖音配置成功']);
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
    public function multipleDeleteSystemDouyinConfig($validated,$admin)
    {

        if(isset($validated['selectId']) && count($validated['selectId']))
        {
            $result = code(config('admin_code.MultipleRestoreSystemDouyinConfigError'));

            $eventName = 'MultipleRestoreSystemDouyinConfig';

            if($validated['is_delete'])
            {
                $result = code(config('admin_code.MultipleDeleteSystemDouyinConfigError'));

                $eventName = 'MultipleDeleteSystemDouyinConfig';
            }

            //批量删除
            if($validated['is_delete'])
            {
                 $deleteResult = SystemDouyinConfig::whereIn('id',$validated['selectId'])->delete();
            }
            else
            {
                $deleteResult = SystemDouyinConfig::withTrashed()->whereIn('id',$validated['selectId'])->restore();
            }

            if(!$deleteResult)
            {
                if($validated['is_delete'])
                {
                    throw new CommonException('MultipleDeleteSystemDouyinConfigError');
                }

                throw new CommonException('MultipleRestoreSystemDouyinConfigError');
            }

            CommonEvent::dispatch($admin,$validated,$eventName);

            $result = code(['code'=>0,'msg'=>'批量恢复系统抖音配置成功']);

            if($validated['is_delete'])
            {
                $result = code(['code'=>0,'msg'=>'批量删除系统抖音配置成功']);
            }

        }

        return $result;
    }
}
