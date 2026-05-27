<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-19 10:23:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-05 14:38:31
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminVoiceConfigFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfig\AddVoiceConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfig\getVoiceConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfig\UpdateVoiceConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfig\DeleteVoiceConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfig\MultipleDeleteVoiceConfigDTO;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\SystemConfig\SystemVoiceConfig;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\Voice\EsSystemVoiceConfigResouce;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\Voice\EsSystemVoiceConfigCollection;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfigController
 * @see \App\Facades\LaravelFastApi\V1\Admin\System\SystemConfig\AdminVoiceConfigFacade
 */
class AdminVoiceConfigFacadeService
{
    public function test()
    {
        echo "AdminVoiceConfigFacadeService test";
    }

    protected static $sortMapArray = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];

    protected static $searchItemArray = [
        'voice_title',
        'channle_name',
        'channle_event'
    ];

    protected static $storage_public_path = DIRECTORY_SEPARATOR.'app'. DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;


    /**
     * 获取所有提示配置
     * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Login\LoginAfterController
     * @param  [type] $validated
     * @param  [type] $adminObject
     */
    public function getAllSystemVoiceConfig(Admin $adminObject)
    {
        $result = code(config('admin_cdoe.GetSystemVoiceConfigError'));

        $indexName = config('common_es.indices.system.system_voice_configs');

        $esQuerry = EsQueryFacade::index($indexName);

        $esQuerry->whereNull('deleted_at');

        $max_size = config('common_es.max_result_window');

        $sytstemVoiceConfigCollection =  $esQuerry->limit($max_size)->get();

        // p($sytstemVoiceConfigCollection);
        // die;

        if (\optional($sytstemVoiceConfigCollection)) {
            $download = null;

            $result = new EsSystemVoiceConfigCollection($sytstemVoiceConfigCollection, ['code' => 0,'msg' => '获取提示配置成功'], $download);
        }

        return  $result;
    }
    /**
     * 查询
     *
     * @param [type] $validated
     * @return void
     */
    public function getVoiceConfig(GetVoiceConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_cdoe.GetSystemVoiceConfigError'));

        $validated = $requestDTO->toArray();

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.system.system_voice_configs');

        $esQuery = EsQueryFacade::index($indexName);

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

        $sytstemVoiceConfigPaginator = $esQuery->page($currentPage, $perPage)->paginate();

        // p($sytstemVoiceConfigList);die;

        if (\optional($sytstemVoiceConfigPaginator)) {
            $result = new EsSystemVoiceConfigCollection($sytstemVoiceConfigPaginator, ['code' => 0,'msg' => '获取提示配置成功'], $download);
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
    public function addVoiceConfig(AddVoiceConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_cdoe.AddSystemVoiceConfigError'));

        $validated = $requestDTO->toArray();

        $systemVoiceConfigObject = new SystemVoiceConfig();

        foreach ($validated as $key => $value) {
            if (isset($value)) {
                $systemVoiceConfigObject->$key = $value;
            }
        }

        $systemVoiceConfigObject->created_time = time();
        $systemVoiceConfigObject->created_at = time();

        $systemVoiceConfigResult = $systemVoiceConfigObject->save();

        if (!$systemVoiceConfigResult) {
            throw new CommonException('AddSystemVoiceConfigError');
        }

        $indexName = config('common_es.indices.system.system_voice_configs');

        $insertDataArray = [
			'_docId'=>$systemVoiceConfigObject->id,
            'id' => $systemVoiceConfigObject->id,
            'voice_title' => $systemVoiceConfigObject->voice_title,
            'channle_name' => $systemVoiceConfigObject->channle_name,
            'channle_event' => $systemVoiceConfigObject->channle_event,
            'voice_save_type' => $systemVoiceConfigObject->voice_save_type,
            'voice_url' => $systemVoiceConfigObject->voice_url,
            'voice_path' => $systemVoiceConfigObject->voice_path,
            'voice_file' => $systemVoiceConfigObject->voice_file,
            'note' => $systemVoiceConfigObject->note,
            'sort' => $systemVoiceConfigObject->sort,
            'created_time' => $systemVoiceConfigObject->created_time,
            'updated_time' => $systemVoiceConfigObject->updated_time,
            'created_at' => $systemVoiceConfigObject->created_at,
            'updated_at' => $systemVoiceConfigObject->updated_at,
            'deleted_at' => $systemVoiceConfigObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $systemVoiceConfigObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加系统提示音配置失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$systemVoiceConfigObject' => $systemVoiceConfigObject,'$adminObject' => $adminObject], 'AdminVoiceConfigFacadeService', 'handleError');

            throw new CommonException('EsAddSystemVoiceConfigError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'AddSystemVoiceConfig');

        $result = code(['code' => 0,'msg' => '添加提示音配置成功']);

        return $result;
    }


    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function UpdateVoiceConfig(UpdateVoiceConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_cdoe.UpdateSystemVoiceConfigError'));

        $validated = $requestDTO->toArray();

        $systemVoiceConfigObject = SystemVoiceConfig::find($validated['id']);

        if (!$systemVoiceConfigObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$systemVoiceConfigObject ->revision];

        foreach ($validated as $key => $value) {
            if ($key === 'id') {
                $where[] = ['id','=',$value];
                continue;
            }

            if (isset($value)) {
                $updateDataArray[$key] = $value;
            }
        }

        $updateDataArray['revision'] = $systemVoiceConfigObject ->revision + 1;

        $updateDataArray['updated_time'] = time();

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        $systemVoiceConfigResult = SystemVoiceConfig::where($where)->update($updateDataArray);

        if (!$systemVoiceConfigResult) {
            throw new CommonException('UpdateSystemVoiceConfigError');
        }

        $systemVoiceConfigObject = $systemVoiceConfigObject->fresh();

        $indexName = config('common_es.indices.system.system_voice_configs');

        $updateDataArray = [
            'voice_title' => $systemVoiceConfigObject->voice_title,
            'channle_name' => $systemVoiceConfigObject->channle_name,
            'channle_event' => $systemVoiceConfigObject->channle_event,
            'voice_save_type' => $systemVoiceConfigObject->voice_save_type,
            'voice_url' => $systemVoiceConfigObject->voice_url,
            'voice_path' => $systemVoiceConfigObject->voice_path,
            'voice_file' => $systemVoiceConfigObject->voice_file,
            'note' => $systemVoiceConfigObject->note,
            'sort' => $systemVoiceConfigObject->sort,
            'created_time' => $systemVoiceConfigObject->created_time,
            'updated_time' => $systemVoiceConfigObject->updated_time,
            'created_at' => $systemVoiceConfigObject->created_at,
            'updated_at' => $systemVoiceConfigObject->updated_at,
            'deleted_at' => $systemVoiceConfigObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($indexName, $systemVoiceConfigObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新系统提示音配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$systemVoiceConfigObject' => $systemVoiceConfigObject,'$adminObject' => $adminObject], 'AdminVoiceConfigFacadeService', 'handleError');

            throw new CommonException('EsAddSystemVoiceConfigError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'UpdateSystemVoiceConfig');

        $result = code(['code' => 0,'msg' => '修改提示配置成功']);

        return $result;
    }


    /**
     * 删除
     *
     * @param [type] $id
     * @param [type] $adminObject
     * @return void
     */
    public function deleteVoiceConfig(DeleteVoiceConfigDTO $requestDTO, Admin $adminObject)
    {
        //删除
        $result = code(config('admin_cdoe.DeleteSystemVoiceConfigError'));

        $validated = $requestDTO->toArray();

        $systemVoiceConfigObject = SystemVoiceConfig::find($validated['id']);

        if (!$systemVoiceConfigObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $systemVoiceConfigResult =  $systemVoiceConfigObject->delete();

        if (!$systemVoiceConfigResult) {
            throw new CommonException('DeleteSystemVoiceConfigError');
        }


        $indexName = config('common_es.indices.system.system_voice_configs');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s'),
        ];

        $esResult = EsFacade::updateDoc($indexName, $systemVoiceConfigObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es删除系统提示音配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$systemVoiceConfigObject' => $systemVoiceConfigObject,'$adminObject' => $adminObject], 'AdminVoiceConfigFacadeService', 'handleError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'DeleteSystemVoiceConfig');

        $result = code(['code' => 0,'msg' => '删除提示配置成功']);

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function multipleDeleteVoiceConfig(MultipleDeleteVoiceConfigDTO $requestDTO, Admin $adminObject)
    {
        $validated = $requestDTO->toArray();

        $result = code(config('admin_cdoe.MultipleDeleteSystemVoiceConfigError'));

        $select_id_array = $requestDTO->select_id_array;

        $systemVoiceConfigResult = SystemVoiceConfig::whereIn('id', $select_id_array)->delete();


        if (!$systemVoiceConfigResult) {
            throw new CommonException('MultipleDeleteSystemVoiceConfigError');
        }

        $systemVoiceConfigCollection = SystemVoiceConfig::withTrashed()->whereIn('id', $select_id_array)->get();

        $indexName = config('common_es.indices.system.system_voice_configs');

        $updateDataArray = [];

        foreach ($systemVoiceConfigCollection as $systemVoiceConfigObject) {
            $updateDataArray[] =
                [
                    '_docId' => $systemVoiceConfigObject->id,
                    'id' => $systemVoiceConfigObject->id,
                    'voice_title' => $systemVoiceConfigObject->voice_title,
                    'channle_name' => $systemVoiceConfigObject->channle_name,
                    'channle_event' => $systemVoiceConfigObject->channle_event,
                    'voice_save_type' => $systemVoiceConfigObject->voice_save_type,
                    'voice_url' => $systemVoiceConfigObject->voice_url,
                    'voice_path' => $systemVoiceConfigObject->voice_path,
                    'voice_file' => $systemVoiceConfigObject->voice_file,
                    'note' => $systemVoiceConfigObject->note,
                    'sort' => $systemVoiceConfigObject->sort,
                    'created_time' => $systemVoiceConfigObject->created_time,
                    'updated_time' => $systemVoiceConfigObject->updated_time,
                    'created_at' => $systemVoiceConfigObject->created_at,
                    'updated_at' => $systemVoiceConfigObject->updated_at,
                    'deleted_at' => $systemVoiceConfigObject->deleted_at,
                ];
        }

        $esResult = EsFacade::batchActDoc($indexName, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新系统配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$requestDTO' => $requestDTO,'$adminObject' => $adminObject], 'AdminVoiceConfigFacadeService', 'handleError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'MultipleDeleteSystemVoiceConfig');

        $result = code(['code' => 0,'msg' => '批量删除提示配置成功']);

        return $result;
    }
}
