<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-31 15:42:03
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-05 17:25:45
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemDouyinConfigFacadeService.php
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
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\DouyinConfig\AddSystemDouyinConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\DouyinConfig\GetSystemDouyinConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\DouyinConfig\UpdateSystemDouyinConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\DouyinConfig\DeleteSystemDouyinConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\DouyinConfig\MultipleDeleteSystemDouyinConfigDTO;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Platform\SystemDouyinConfig;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\OtherPlatform\EsSystemDouyinConfigResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\OtherPlatform\EsSystemDouyinConfigCollection;
use YouHuJun\Tool\App\Facades\V1\Excel\ExcelFacade;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemDouyinConfigController
 * @see \App\Facades\Admin\System\SystemConfig\OtherPlatform\SystemDouyinConfigFacade
 */
class SystemDouyinConfigFacadeService
{
    public function test()
    {
        echo "SystemDouyinConfigFacadeService test";
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
                    'mysql_SystemDouyinConfig_name' => $value[0],
                    'mysql_SystemDouyinConfig_code' => empty($value[1]) ? null : $value[1],
                    'is_default' => empty($value[2]) ? 0 : $value[2],
                    'sort' => empty($value[3]) ? 100 : $value[3]
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

        foreach ($systemDouyinConfigObjectList as $key => $value) {
            $list = [];

            $list[] = $value->mysql_SystemDouyinConfig_name;
            $list[] = $value->mysql_SystemDouyinConfig_code;
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
    public function getSystemDouyinConfig(GetSystemDouyinConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetSystemDouyinConfigError'));

        $validated = $requestDTO->toArray();

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.system.system_douyin_configs');

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

        $systemDouyinConfigPaginator = $esQuery->page($currentPage, $perPage)->paginate();

        if (\optional($systemDouyinConfigPaginator)) {
            $result = new EsSystemDouyinConfigCollection($systemDouyinConfigPaginator, ['code' => 0,'msg' => '获取系统抖音配置列表成功!'], $download);
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
    public function addSystemDouyinConfig(AddSystemDouyinConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddSystemDouyinConfigError'));

        $systemDouyinConfigObject = new SystemDouyinConfig();

        $validated = $requestDTO->toArray();

        foreach ($validated as $key => $value) {
            if (isset($value)) {
                $systemDouyinConfigObject->$key = $value;
            }
        }

        $systemDouyinConfigObject->created_time = time();
        $systemDouyinConfigObject->created_at = time();

        $systemDouyinConfigObjectResult = $systemDouyinConfigObject->save();

        if (!$systemDouyinConfigObjectResult) {
            throw new CommonException('AddSystemDouyinConfigError');
        }

        $indexName = config('common_es.indices.system.system_douyin_configs');

        $insertDataArray = [
            '_docId' => $systemDouyinConfigObject->id,
            'id' => $systemDouyinConfigObject->id,
            'name' => $systemDouyinConfigObject->name,
            'type' => $systemDouyinConfigObject->type,
            'appid' => $systemDouyinConfigObject->appid,
            'appsecret' => $systemDouyinConfigObject->appsecret,
            'note' => $systemDouyinConfigObject->note,
            'sort' => $systemDouyinConfigObject->sort,
            'created_at' => $systemDouyinConfigObject->created_at,
            'created_time' => $systemDouyinConfigObject->created_time,
            'updated_at' => $systemDouyinConfigObject->updated_at,
            'updated_time' => $systemDouyinConfigObject->updated_time,
            'deleted_at' => $systemDouyinConfigObject->deleted_at
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $systemDouyinConfigObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加系统抖音配置失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$systemDouyinConfigObject' => $systemDouyinConfigObject,'$adminObject' => $adminObject], 'SystemDouyinConfigFacadeService', 'handleError');

            throw new CommonException('EsAddSystemDouyinConfigError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'AddSystemDouyinConfig');

        $result = code(['code' => 0,'msg' => '添加系统抖音配置成功']);

        return $result;
    }


    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updateSystemDouyinConfig(UpdateSystemDouyinConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateSystemDouyinConfigError'));

        $validated = $requestDTO->toArray();

        $systemDouyinConfigObject = SystemDouyinConfig::find($validated['id']);

        if (!$systemDouyinConfigObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$systemDouyinConfigObject ->revision];

        foreach ($validated as $key => $value) {
            if ($key === 'id') {
                $where[] = ['id','=',$value];
                continue;
            }

            if (isset($value)) {
                $updateDataArray[$key] = $value;
            }
        }

        $updateDataArray['revision'] = $systemDouyinConfigObject ->revision + 1;

        $updateDataArray['updated_time'] = time();

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        $systemDouyinConfigObjectResult = SystemDouyinConfig::where($where)->update($updateDataArray);

        if (!$systemDouyinConfigObjectResult) {
            throw new CommonException('UpdateSystemDouyinConfigError');
        }

        $systemDouyinConfigObject = $systemDouyinConfigObject->fresh();

        $indexName = config('common_es.indices.system.system_douyin_configs');

        $updateDataArray = [
            'id' => $systemDouyinConfigObject->id,
            'name' => $systemDouyinConfigObject->name,
            'type' => $systemDouyinConfigObject->type,
            'appid' => $systemDouyinConfigObject->appid,
            'appsecret' => $systemDouyinConfigObject->appsecret,
            'note' => $systemDouyinConfigObject->note,
            'sort' => $systemDouyinConfigObject->sort,
            'created_at' => $systemDouyinConfigObject->created_at,
            'created_time' => $systemDouyinConfigObject->created_time,
            'updated_at' => $systemDouyinConfigObject->updated_at,
            'updated_time' => $systemDouyinConfigObject->updated_time,
            'deleted_at' => $systemDouyinConfigObject->deleted_at
        ];

        $esResult = EsFacade::updateDoc($indexName, $systemDouyinConfigObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新系统抖音配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$systemDouyinConfigObject' => $systemDouyinConfigObject,'$adminObject' => $adminObject], 'SystemDouyinConfigFacadeService', 'handleError');
            throw new CommonException('EsUpdateSystemDouyinConfigError');
        }

        CommonEvent::dispatch($adminObject, $systemDouyinConfigObject, 'UpdateSystemDouyinConfig');

        $result = code(['code' => 0,'msg' => '更新系统抖音配置成功']);

        return $result;
    }


    /**
     * 删除
     *
     * @param [type] $id
     * @param [type] $adminObject
     * @return void
     */
    public function deleteSystemDouyinConfig(DeleteSystemDouyinConfigDTO $requestDTO, Admin $adminObject)
    {
        //删除
        $result = code(config('admin_code.RestoreSystemDouyinConfigError'));

        $validated = $requestDTO->toArray();

        $eventName = 'RestoreSystemDouyinConfig';

        if ($validated['is_delete']) {
            $result = code(config('admin_code.DeleteSystemDouyinConfigError'));

            $eventName = 'DeleteSystemDouyinConfig';
        }

        if ($validated['is_delete']) {
            $systemDouyinConfigObject = SystemDouyinConfig::find($validated['id']);

            if (!$systemDouyinConfigObject) {
                throw new CommonException('ThisDataNotExistsError');
            }

            $systemDouyinConfigObjectResult =  $systemDouyinConfigObject->delete();
        } else {
            //恢复
            $systemDouyinConfigObject = SystemDouyinConfig::withTrashed()->find($validated['id']);

            if (!$systemDouyinConfigObject) {
                throw new CommonException('ThisDataNotExistsError');
            }

            $systemDouyinConfigObjectResult =  $systemDouyinConfigObject->restore();
        }

        if (!$systemDouyinConfigObjectResult) {
            if ($validated['is_delete']) {
                throw new CommonException('DeleteSystemDouyinConfigError');
            }

            throw new CommonException('RestoreSystemDouyinConfigError');
        }

        $systemDouyinConfigObject = $systemDouyinConfigObject->fresh();

        $indexName = config('common_es.indices.system.system_douyin_configs');

        $updateDataArray = [
            'deleted_at' => $systemDouyinConfigObject->deleted_at
        ];

        $esResult = EsFacade::updateDoc($indexName, $systemDouyinConfigObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新系统抖音配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$systemDouyinConfigObject' => $systemDouyinConfigObject,'$adminObject' => $adminObject], 'SystemDouyinConfigFacadeService', 'handleError');
            throw new CommonException('EsDeleteSystemDouyinConfigError');
        }

        CommonEvent::dispatch($adminObject, $validated['id'], $eventName);

        $result = code(['code' => 0,'msg' => '恢复系统抖音配置成功']);

        if ($validated['is_delete']) {
            $result = code(['code' => 0,'msg' => '删除系统抖音配置成功']);
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
    public function multipleDeleteSystemDouyinConfig(MultipleDeleteSystemDouyinConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MultipleRestoreSystemDouyinConfigError'));
        $validated = $requestDTO->toArray();

        $select_id_array = $requestDTO->select_id_array;

        $eventName = 'MultipleRestoreSystemDouyinConfig';

        if ($validated['is_delete']) {
            $result = code(config('admin_code.MultipleDeleteSystemDouyinConfigError'));

            $eventName = 'MultipleDeleteSystemDouyinConfig';
        }

        //批量删除
        if ($validated['is_delete']) {
            $deleteResult = SystemDouyinConfig::whereIn('id', $select_id_array)->delete();
        } else {
            $deleteResult = SystemDouyinConfig::withTrashed()->whereIn('id', $select_id_array)->restore();
        }

        if (!$deleteResult) {
            if ($validated['is_delete']) {
                throw new CommonException('MultipleDeleteSystemDouyinConfigError');
            }

            throw new CommonException('MultipleRestoreSystemDouyinConfigError');
        }

        $indexName = config('common_es.indices.system.system_douyin_configs');

        $systemDouyinConfigCollection = SystemDouyinConfig::withTrashed()->whereIn('id', $select_id_array)->get();

        $updateDataArray = [];

        foreach ($systemDouyinConfigCollection as $systemDouyinConfigObject) {
            $updateDataArray[] =
                [
                    '_docId' => $systemDouyinConfigObject->id,
                    'id' => $systemDouyinConfigObject->id,
                    'name' => $systemDouyinConfigObject->name,
                    'type' => $systemDouyinConfigObject->type,
                    'appid' => $systemDouyinConfigObject->appid,
                    'appsecret' => $systemDouyinConfigObject->appsecret,
                    'note' => $systemDouyinConfigObject->note,
                    'sort' => $systemDouyinConfigObject->sort,
                    'created_at' => $systemDouyinConfigObject->created_at,
                    'created_time' => $systemDouyinConfigObject->created_time,
                    'updated_at' => $systemDouyinConfigObject->updated_at,
                    'updated_time' => $systemDouyinConfigObject->updated_time,
                    'deleted_at' => $systemDouyinConfigObject->deleted_at
                ];
        }

        $esResult = EsFacade::batchActDoc($indexName, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es批量更新抖音系统配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$requestDTO' => $requestDTO,'$adminObject' => $adminObject], 'SystemDouyinConfigFacadeService', 'handleError');
            throw new CommonException('EsMultipleDeleteSystemDouyinConfigError');
        }

        CommonEvent::dispatch($adminObject, $validated, $eventName);

        $result = code(['code' => 0,'msg' => '批量恢复系统抖音配置成功']);

        if ($validated['is_delete']) {
            $result = code(['code' => 0,'msg' => '批量删除系统抖音配置成功']);
        }

        return $result;
    }
}
