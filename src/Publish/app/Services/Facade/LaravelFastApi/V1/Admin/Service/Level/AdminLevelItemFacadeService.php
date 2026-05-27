<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-26 22:32:30
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-09 16:14:22
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Service\Level\AdminLevelItemFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Service\Level;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Services\Facade\Traits\V1\Es\EsAlwaysService;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\DefaultLevelItemDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\FindLevelItemDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\GetLevelItemDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\AddLevelItemDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\UpdateLevelItemDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\DeleteLevelItemDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\MultipleDeleteLevelItemDTO;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Level\LevelItem;
use App\Models\LaravelFastApi\V1\Admin\Log\AdminUploadFileLog;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Service\Level\EsLevelItemResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Service\Level\EsLevelItemCollection;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Level\LevelItemController
 * @see \App\Facades\LaravelFastApi\V1\Admin\Service\Level\AdminLevelItemFacade
 */
class AdminLevelItemFacadeService
{
    public function test()
    {
        echo "AdminLevelItemFacadeService test";
    }

    protected static $sortMapArray = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];

    protected static $searchItemMapArray = [
        'item_name',
        'item_code'
    ];

    protected static $storage_public_path = DIRECTORY_SEPARATOR.'app'. DIRECTORY_SEPARATOR.'public';

    /**
     * 获取常用
     *
     * @param [type] $userObject
     * @return void
     */
    public function defaultLevelItem(DefaultLevelItemDTO $requestDTO)
    {
        $result = code(config('admin_code.DefaultlevelItemError'));

        $validated = $requestDTO->toArray();

        $indexName = config('common_es.indices.business.level_items');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);
        $esQuery->whereNull('deleted_at');

        $esQuery->orderBy('id', 'asc');

        $max_size = config('common_es.max_result_window');

        $levelItemCollection = $esQuery->limit($max_size)->get();

        if (!optional($levelItemCollection)) {
            throw new CommonException('DefaultlevelItemError');
        }

        $result = new EsLevelItemCollection($levelItemCollection, ['code' => 0,'msg' => '获取默认级别配置项成功!']);

        return  $result;
    }

    /**
     * 搜索查找选项
     *
     * @param [type] $find
     * @return void
     */
    public function findLevelItem(FindLevelItemDTO $requestDTO)
    {
        $result = code(config('admin_code.FindlevelItemError'));

        $validated = $requestDTO->toArray();

        $indexName = config('common_es.indices.business.level_items');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);
        $esQuery->whereNull('deleted_at');
        if (!empty($validated['find'])) {
            $esQuery->whereLike('item_name', $validated['find']);
        }
        $esQuery->orderBy('id', 'asc');

        $levelItemCollection = $esQuery->limit(10)->get();

        if (!optional($levelItemCollection)) {
            throw new CommonException('FindlevelItemError');
        }

        $result = new EsLevelItemCollection($levelItemCollection, ['code' => 0,'msg' => '查找级别配置项成功!']);

        return  $result;
    }

    /**
     * 查询
     *
     * @param [type] $validated
     * @return void
     */
    public function getLevelItem(GetLevelItemDTO $requestDTO)
    {
        $result = code(config('admin_code.GetLevelItemError'));

        $validated = $requestDTO->toArray();

        $perPage = $validated['pageSize'];
        $currentPage = $validated['currentPage'];

        $indexName = config('common_es.indices.business.level_items');

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

        $esQuery->orderBy('id','desc');

        //排序
        if (isset($requestDTO->sortType)) {
            $sortType = $requestDTO->sortType;

            $esQuery->orderBy(self::$sortMapArray[$sortType][0], self::$sortMapArray[$sortType][1]);
        }

        // 导出逻辑（完全按你的真实业务）

        $download = null;

        if (isset($requestDTO->isExport) && $requestDTO->isExport == 1) {
            if ($requestDTO->exportType) {
                // 本页导出
                if ($requestDTO->exportType == 1) {
                    // 直接用已配置的 esQuery get()
                    $exportColelction = $esQuery->page($currentPage, $perPage)->get();
                    $title = $this->exportData($exportColelction); // 直接下载，中断，不回头
                }

                // 全部导出
                if ($requestDTO->exportType == 2) {
                    // 不带分页，get() 自动用 10000 兜底
                    $exportColelction = $esQuery->get();
                    $title = $this->exportData($exportColelction); // 直接下载，中断
                }

                $exists = Storage::disk('public')->exists("excel/{$title}.xlsx");

                if ($exists) {
                    $download = asset("storage/excel/{$title}.xlsx");
                }

                if($download){
                    $result = ['code' => 0,'msg' => '获取替换列表成功!','download'=>$download];
                }else{
                    $result = ['code' => 10000,'msg' => '获取替换列表失败!'];

                }
                return $result;
            }
        }


        $levelItemPaginator = $esQuery->page($currentPage, $perPage)->paginate();


        if (\optional($levelItemPaginator)) {
            $result = new EsLevelItemCollection($levelItemPaginator, ['code' => 0,'msg' => '获取级别配置项成功!']);
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
    public function addLevelItem(AddLevelItemDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddLevelItemError'));

        $validated = $requestDTO->toArray();

        $levelItemObject = new LevelItem();

        foreach ($validated as $key => $value) {
            if (\is_null($value)) {
                $value = "";
            }

            $levelItemObject->$key = $value;
        }

        $levelItemObject->created_time = time();
        $levelItemObject->created_at = time();

        $levelItemResult = $levelItemObject->save();

        if (!$levelItemResult) {
            throw new CommonException('AddLevelItemError');
        }

        $indexName = config('common_es.indices.business.level_items');

        $insertDataArray = [
            '_docId' => $levelItemObject->id,
            'id' => $levelItemObject->id,
            'type' => $levelItemObject->type,
            'item_name' => $levelItemObject->item_name,
            'item_code' => $levelItemObject->item_code,
            'description' => $levelItemObject->description,
            'sort' => $levelItemObject->sort,
            'created_time' => $levelItemObject->created_time,
            'updated_time' => $levelItemObject->updated_time,
            'created_at' => $levelItemObject->created_at,
            'updated_at' => $levelItemObject->updated_at,
            'deleted_at' => $levelItemObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $levelItemObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加级别配置项配置失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$levelItemObject' => $levelItemObject,'$adminObject' => $adminObject], 'AdminLevelItemFacadeService', 'handleError');
            throw new CommonException('EsAddLevelItemError');
        }

        CommonEvent::dispatch($adminObject, $levelItemObject, 'AddLevelItem');

        $result = code(['code' => 0,'msg' => '添加级别配置项成功!']);

        return $result;
    }


    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updateLevelItem(UpdateLevelItemDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateLevelItemError'));

        $validated = $requestDTO->toArray();

        $levelItemObject = LevelItem::find($validated['id']);

        if (!$levelItemObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$levelItemObject ->revision];

        foreach ($validated as $key => $value) {
            if ($key === 'id') {
                $where[] = ['id','=',$value];
                continue;
            }

            if (\is_null($value)) {
                $value = "";
            }

            $updateDataArray[$key] = $value;
        }

        $updateDataArray['revision'] = $levelItemObject ->revision + 1;

        $updateDataArray['updated_time'] = time();

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        $levelItemResult = LevelItem::where($where)->update($updateDataArray);

        if (!$levelItemResult) {
            throw new CommonException('UpdateLevelItemError');
        }

        $levelItemObject = $levelItemObject->fresh();

        $indexName = config('common_es.indices.business.level_items');

        $updateDataArray = [
            '_docId' => $levelItemObject->id,
            'id' => $levelItemObject->id,
            'type' => $levelItemObject->type,
            'item_name' => $levelItemObject->item_name,
            'item_code' => $levelItemObject->item_code,
            'description' => $levelItemObject->description,
            'sort' => $levelItemObject->sort,
            'created_time' => $levelItemObject->created_time,
            'updated_time' => $levelItemObject->updated_time,
            'created_at' => $levelItemObject->created_at,
            'updated_at' => $levelItemObject->updated_at,
            'deleted_at' => $levelItemObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($indexName, $levelItemObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新级别配置项配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$levelItemObject' => $levelItemObject,'$adminObject' => $adminObject], 'AdminLevelItemFacadeService', 'handleError');
            throw new CommonException('EsUpdatedLevelItemError');
        }

        CommonEvent::dispatch($adminObject, $levelItemObject, 'UpdateLevelItem');

        $result = code(['code' => 0,'msg' => '更改级别配置项成功!']);

        return $result;
    }


    /**
     * 删除
     *
     * @param [type] $id
     * @param [type] $adminObject
     * @return void
     */
    public function deleteLevelItem(DeleteLevelItemDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteLevelItemError'));

        $validated = $requestDTO->toArray();

        $levelItemObject = LevelItem::find($validated['id']);

        $levelItemObject->deleted_at = date('Y-m-d H:i:s');

        $levelItemResult =  $levelItemObject->save();

        if (!$levelItemResult) {
            throw new CommonException('DeleteLevelItemError');
        }

        $indexName = config('common_es.indices.business.level_items');

        $updateDataArray = [

            'deleted_at' =>date('Y-m-d H:i:s'),
        ];

        $esResult = EsFacade::updateDoc($indexName, $levelItemObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新级别配置项配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$levelItemObject' => $levelItemObject,'$adminObject' => $adminObject], 'AdminLevelItemFacadeService', 'handleError');
            throw new CommonException('EsDeletedLevelItemError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'DeleteLevelItem');

        $result = code(['code' => 0,'msg' => '删除级别配置项成功!']);

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function multipleDeleteLevelItem(MultipleDeleteLevelItemDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MultipleDeleteLevelItemError'));

        $validated = $requestDTO->toArray();

        $deleteResult = LevelItem::whereIn('id', $validated['select_id_array'])->delete();

        if (!$deleteResult) {
            throw new CommonException('MultipleDeleteLevelItemError');
        }

        $select_id_array = $requestDTO->select_id_array;

        $indexName = config('common_es.indices.business.level_items');

        $levelItemCollection = LevelItem::withTrashed()->whereIn('id', $select_id_array)->get();

        $updateDataArray = [];
        foreach ($levelItemCollection as $levelItemObject) {
            $updateDataArray[] = [
                '_docId' => $levelItemObject->id,
                'id' => $levelItemObject->id,
                'type' => $levelItemObject->type,
                'item_name' => $levelItemObject->item_name,
                'item_code' => $levelItemObject->item_code,
                'description' => $levelItemObject->description,
                'sort' => $levelItemObject->sort,
                'created_time' => $levelItemObject->created_time,
                'updated_time' => $levelItemObject->updated_time,
                'created_at' => $levelItemObject->created_at,
                'updated_at' => $levelItemObject->updated_at,
                'deleted_at' => $levelItemObject->deleted_at,
            ];
        }

        $esResult = EsFacade::batchActDoc($indexName,$updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es批量更新级别配置项配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$adminObject' => $adminObject], 'AdminLevelItemFacadeService', 'handleError');
            throw new CommonException('EsUpdatedLevelItemError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'MultipleDeleteLevelItem');

        $result = code(['code' => 0,'msg' => '批量删除级别配置项成功!']);


        return $result;
    }
}
