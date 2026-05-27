<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-31 15:27:03
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-05 23:54:42
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\WechatConfig\GetSystemWechatConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\WechatConfig\AddSystemWechatConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\WechatConfig\UpdateSystemWechatConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\WechatConfig\DeleteSystemWechatConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\WechatConfig\MultipleDeleteSystemWechatConfigDTO;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Platform\SystemWechatConfig;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\OtherPlatform\EsSystemWechatConfigResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\OtherPlatform\EsSystemWechatConfigCollection;
use App\Facades\Pub\Excel\ExcelFacade;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigController
 * @see \App\Facades\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigFacade
 */
class SystemWechatConfigFacadeService
{
    public function test()
    {
        echo "SystemWechatConfigFacadeService test";
    }

    protected static $sortMapArray = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];

    protected static $searchItemMapArray = [
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

        if ($exists) {
            ExcelFacade::initReadExcel(storage_path(self::$storage_public_path.$path));

            ExcelFacade::setWorkSheet(0);

            $excelData = ExcelFacade::getDataByRow();

            array_shift($excelData);

            $insertData = [];

            foreach ($excelData as $key => $value) {
                $insertData[] =
                [
                    'mysql_SystemWechatConfig_name' => $value[0],
                    'mysql_SystemWechatConfig_code' => empty($value[1]) ? null : $value[1],
                    'is_default' => empty($value[2]) ? 0 : $value[2],
                    'sort' => empty($value[3]) ? 100 : $value[3]
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

        foreach ($systemWechatConfigObjectList as $key => $value) {
            $list = [];

            $list[] = $value->mysql_SystemWechatConfig_name;
            $list[] = $value->mysql_SystemWechatConfig_code;
            $list[] = $value->is_dfault == 1 ? '是' : '否';
            $list[] = $value->created_at;

            $data[] =  $list;
        }

        $title = "标题名称";

        ExcelFacade::exportExcelData($cloumn, $data, $title, 1);

        return $title;
    }


    /**
     * 查询
     *
     * @param [type] $validated
     * @return void
     */
    public function getSystemWechatConfig(GetSystemWechatConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetSystemWechatConfigError'));

        $validated = $requestDTO->toArray();
        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.system.system_wechat_configs');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户

        $esQuery->whereNull('deleted_at');
        

        // 模糊搜索（手机号/姓名等）
        if (isset($requestDTO->findSelectIndex) && isset($requestDTO->find) && !empty($requestDTO->find)) {
            $findIndex = $requestDTO->findSelectIndex;
            $findValue = $requestDTO->find;
            $searchField = self::$searchItemMapArray[$findIndex] ?? '';
            if ($searchField) {
                $esQuery->whereLike($searchField, $findValue);
            }
        }

        // 时间范围
        if (isset($requestDTO->timeRange) && \count($requestDTO->timeRange)) {
            $startTime = strtotime($requestDTO->timeRange[0]);
            $endTime = strtotime($requestDTO->timeRange[1]);
            $esQuery->whereBetween('created_time', [$startTime, $endTime]);
        }
        //排序
        if (isset($requestDTO->sortType)) {
            $sortType = $requestDTO->sortType;

            $esQuery->orderBy(self::$sortMapArray[$sortType][0], self::$sortMapArray[$sortType][1]);
        }

        $download = null;

        if (isset($requestDTO->isExport) && $requestDTO->isExport == 1) {
            if ($requestDTO->isExport) {
                // 本页导出
                if ($requestDTO->isExport == 10) {
                    // 直接用已配置的 esQuery get()
                    $exportList = $esQuery->page($currentPage, $perPage)->get();
                    $this->exportData($exportList); // 直接下载，中断，不回头
                }

                // 全部导出
                if ($requestDTO->isExport == 20) {
                    // 不带分页，get() 自动用 10000 兜底
                    $exportList = $esQuery->get();
                    $this->exportData($exportList); // 直接下载，中断
                }
            }
        }

        $systemWechatConfigPaginator = $esQuery->page($currentPage, $perPage)->paginate();

        if (\optional($systemWechatConfigPaginator)) {
            $result = new EsSystemWechatConfigCollection($systemWechatConfigPaginator, ['code' => 0,'msg' => '获取系统微信配置列表成功!'], $download);
            //如果要增加其他返回参数,需要在SystemWechatConfigCollection处理
        }

        return  $result;
    }

    /**
     * 添加
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function addSystemWechatConfig(AddSystemWechatConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddSystemWechatConfigError'));

        $validated = $requestDTO->toArray();

        $systemWechatConfigObject = new SystemWechatConfig();

        foreach ($validated as $key => $value) {
            if (isset($value)) {
                $systemWechatConfigObject->$key = $value;
            }
        }

        $systemWechatConfigObject->created_time = time();
        $systemWechatConfigObject->created_at = time();

        $systemWechatConfigObjectResult = $systemWechatConfigObject->save();

        if (!$systemWechatConfigObjectResult) {
            throw new CommonException('AddSystemWechatConfigError');
        }

        $indexName = config('common_es.indices.system.system_wechat_configs');

        $insertDataArray = [
            '_docId' => $systemWechatConfigObject->id,
            'id' => $systemWechatConfigObject->id,
            'name' => $systemWechatConfigObject->name,
            'type' => $systemWechatConfigObject->type,
            'appid' => $systemWechatConfigObject->appid,
            'appsecret' => $systemWechatConfigObject->appsecret,
            'note' => $systemWechatConfigObject->note,
            'sort' => $systemWechatConfigObject->sort,
            'created_at' => $systemWechatConfigObject->created_at,
            'created_time' => $systemWechatConfigObject->created_time,
            'updated_at' => $systemWechatConfigObject->updated_at,
            'updated_time' => $systemWechatConfigObject->updated_time,
            'deleted_at' => $systemWechatConfigObject->deleted_at
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $systemWechatConfigObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加系统微信配置失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$systemWechatConfigObject' => $systemWechatConfigObject,'$adminObject' => $adminObject], 'SystemWechatConfigFacadeService', 'handleError');
            throw new CommonException('EsAddSystemWechatConfigError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'AddSystemWechatConfig');

        $result = code(['code' => 0,'msg' => '添加系统微信配置成功']);

        return $result;
    }


    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updateSystemWechatConfig(UpdateSystemWechatConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateSystemWechatConfigError'));

        $validated = $requestDTO->toArray();

        $systemWechatConfigObject = SystemWechatConfig::find($validated['id']);

        if (!$systemWechatConfigObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$systemWechatConfigObject ->revision];

        foreach ($validated as $key => $value) {
            if ($key === 'id') {
                $where[] = ['id','=',$value];
                continue;
            }

            if (isset($value)) {
                $updateDataArray[$key] = $value;
            }
        }

        $updateDataArray['revision'] = $systemWechatConfigObject ->revision + 1;

        $updateDataArray['updated_time'] = time();

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        $systemWechatConfigObjectResult = SystemWechatConfig::where($where)->update($updateDataArray);

        if (!$systemWechatConfigObjectResult) {
            throw new CommonException('UpdateSystemWechatConfigError');
        }

        $systemWechatConfigObject = $systemWechatConfigObject->fresh();

        $indexName = config('common_es.indices.system.system_wechat_configs');

        $updateDataArray = [
            'id' => $systemWechatConfigObject->id,
            'name' => $systemWechatConfigObject->name,
            'type' => $systemWechatConfigObject->type,
            'appid' => $systemWechatConfigObject->appid,
            'appsecret' => $systemWechatConfigObject->appsecret,
            'note' => $systemWechatConfigObject->note,
            'sort' => $systemWechatConfigObject->sort,
            'created_at' => $systemWechatConfigObject->created_at,
            'created_time' => $systemWechatConfigObject->created_time,
            'updated_at' => $systemWechatConfigObject->updated_at,
            'updated_time' => $systemWechatConfigObject->updated_time,
            'deleted_at' => $systemWechatConfigObject->deleted_at
        ];

        $esResult = EsFacade::updateDoc($indexName, $systemWechatConfigObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新系统微信配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$systemWechatConfigObject' => $systemWechatConfigObject,'$adminObject' => $adminObject], 'SystemWechatConfigFacadeService', 'handleError');

            throw new CommonException('EsAUpdateSystemWechatConfigError');
        }

        CommonEvent::dispatch($adminObject, $systemWechatConfigObject, 'UpdateSystemWechatConfig');

        $result = code(['code' => 0,'msg' => '更新系统微信配置成功']);

        return $result;
    }


    /**
     * 删除
     *
     * @param [type] $id
     * @param [type] $adminObject
     * @return void
     */
    public function deleteSystemWechatConfig(DeleteSystemWechatConfigDTO $requestDTO, Admin $adminObject)
    {
        //删除
        $result = code(config('admin_code.RestoreSystemWechatConfigError'));

        $validated = $requestDTO->toArray();

        $eventName = 'RestoreSystemWechatConfig';

        if ($requestDTO->is_delete) {
            $result = code(config('admin_code.DeleteSystemWechatConfigError'));

            $eventName = 'DeleteSystemWechatConfig';
        }

        if ($requestDTO->is_delete) {
            $systemWechatConfigObject = SystemWechatConfig::find($requestDTO->id);

            if (!$systemWechatConfigObject) {
                throw new CommonException('ThisDataNotExistsError');
            }

            $systemWechatConfigObjectResult =  $systemWechatConfigObject->delete();
        } else {
            //恢复
            $systemWechatConfigObject = SystemWechatConfig::withTrashed()->find($requestDTO->id);

            if (!$systemWechatConfigObject) {
                throw new CommonException('ThisDataNotExistsError');
            }

            $systemWechatConfigObjectResult =  $systemWechatConfigObject->restore();
        }

        if (!$systemWechatConfigObjectResult) {
            if ($validated['is_delete']) {
                throw new CommonException('DeleteSystemWechatConfigError');
            }

            throw new CommonException('RestoreSystemWechatConfigError');
        }

        $indexName = config('common_es.indices.system.system_wechat_configs');

        $updateDataArray = [
            'deleted_at' => $systemWechatConfigObject->deleted_at
        ];

        $esResult = EsFacade::updateDoc($indexName, $systemWechatConfigObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新系统微信配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$systemWechatConfigObject' => $systemWechatConfigObject,'$adminObject' => $adminObject], 'SystemWechatConfigFacadeService', 'handleError');
            throw new CommonException('EsDeleteSystemWechatConfigError');
        }

        CommonEvent::dispatch($adminObject, $validated['id'], $eventName);

        $result = code(['code' => 0,'msg' => '恢复系统微信配置成功']);

        if ($validated['is_delete']) {
            $result = code(['code' => 0,'msg' => '删除系统微信配置成功']);
        }

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function multipleDeleteSystemWechatConfig(MultipleDeleteSystemWechatConfigDTO $requestDTO, Admin $adminObject)
    {
        $validated = $requestDTO->toArray();

        $select_id_array = $requestDTO->select_id_array;

        $result = code(config('admin_code.MultipleRestoreSystemWechatConfigError'));

        $eventName = 'MultipleRestoreSystemWechatConfig';

        if($requestDTO->is_delete) {
            $result = code(config('admin_code.MultipleDeleteSystemWechatConfigError'));

            $eventName = 'MultipleDeleteSystemWechatConfig';
        }

        //批量删除
        if ($requestDTO->is_delete) {
            $deleteResult = SystemWechatConfig::whereIn('id', $select_id_array)->delete();
        } else {
            $deleteResult = SystemWechatConfig::withTrashed()->whereIn('id', $select_id_array)->restore();
        }

        if (!$deleteResult) {
            if ($requestDTO->is_delete) {
                throw new CommonException('MultipleDeleteSystemWechatConfigError');
            }

            throw new CommonException('MultipleRestoreSystemWechatConfigError');
        }

        $indexName = config('common_es.indices.system.system_wechat_configs');

        $systemWechatConfigCollection = SystemWechatConfig::withTrashed()->whereIn('id', $select_id_array)->get();

        $updateDataArray = [];

        foreach ($systemWechatConfigCollection as $systemWechatConfigObject) {
            $updateDataArray[] =
                [
                    '_docId' => $systemWechatConfigObject->id,
                    'id' => $systemWechatConfigObject->id,
                    'name' => $systemWechatConfigObject->name,
                    'type' => $systemWechatConfigObject->type,
                    'appid' => $systemWechatConfigObject->appid,
                    'appsecret' => $systemWechatConfigObject->appsecret,
                    'note' => $systemWechatConfigObject->note,
                    'sort' => $systemWechatConfigObject->sort,
                    'created_at' => $systemWechatConfigObject->created_at,
                    'created_time' => $systemWechatConfigObject->created_time,
                    'updated_at' => $systemWechatConfigObject->updated_at,
                    'updated_time' => $systemWechatConfigObject->updated_time,
                    'deleted_at' => $systemWechatConfigObject->deleted_at
                ];
        }

        $esResult = EsFacade::batchActDoc($indexName, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es批量更新系统配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$requestDTO' => $requestDTO,'$adminObject' => $adminObject], 'SystemWechatConfigFacadeService', 'handleError');
            throw new CommonException('EsMultipleDeleteSystemWechatConfigError');
        }

        CommonEvent::dispatch($adminObject, $validated, $eventName);

        $result = code(['code' => 0,'msg' => '批量恢复系统微信配置成功']);

        if ($requestDTO->is_delete) {
            $result = code(['code' => 0,'msg' => '批量删除系统微信配置成功']);
        }

        return $result;
    }
}
