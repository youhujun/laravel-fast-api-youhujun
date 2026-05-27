<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-14 15:27:06
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-10 16:08:39
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Service\Platform\AdminPhoneBannerFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Service\Platform;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Services\Facade\Traits\V1\Es\EsAlwaysService;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\GetPhoneBannerDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\AddPhoneBannerDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\UpdatePhoneBannerDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\DeletePhoneBannerDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\MultipleDeletePhoneBannerDTO;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Phone\PhoneBanner;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Service\PhoneBanner\EsPhoneBannerCollection;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Platform\PhoneBannerController
 * @see \App\Facades\LaravelFastApi\V1\Admin\Service\Platform\AdminPhoneBannerFacade
 */
class AdminPhoneBannerFacadeService
{
    public function test()
    {
        echo "AdminPhoneBannerFacadeService test";
    }

    protected static $sortMapArray = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];



    /**
     * 获取首页轮播图
     * @param {*} $validated
     * @param {*} $adminObject
     * @return {*}
     */
    public function getPhoneBanner(GetPhoneBannerDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetPhoneBannerError'));

        $validated = $requestDTO->toArray();

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;


        $indexName = config('common_es.indices.business.phone_banners');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

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

        $download = null;

        $phoneBannerPaginator = $esQuery->page($currentPage, $perPage)->paginate();

        if (\optional($phoneBannerPaginator)) {
            $result = new EsPhoneBannerCollection($phoneBannerPaginator, ['code' => 0,'msg' => '后台获取手机轮播图列表成功']);
        }

        return  $result;
    }

    /**
    * @添加首页轮播图:
    * @param {*} $validated
    * @param {*} $adminObject
    * @return {*}
    */
    public function addPhoneBanner(AddPhoneBannerDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddPhoneBannerError'));

        $validated = $requestDTO->toArray();

        $phoneBannerObject = new PhoneBanner();

        foreach ($validated as $key => $value) {
            if (\is_null($value)) {
                $value = "";
            }

            $phoneBannerObject->$key = $value;
        }

        $phoneBannerObject->created_time = time();
        $phoneBannerObject->created_at = time();

        $phoneBannerResult = $phoneBannerObject->save();

        if (!$phoneBannerResult) {
            throw new CommonException('AddPhoneBannerError');
        }

        $indexName = config('common_es.indices.business.phone_banners');

        $insertDataArray = [
            '_docId' => $phoneBannerObject->id,
            'id' => $phoneBannerObject->id,
            'album_picture_uid' => $phoneBannerObject->album_picture_uid,
            'redirect_url' => $phoneBannerObject->redirect_url,
            'note' => $phoneBannerObject->note,
            'sort' => $phoneBannerObject->sort,
            'created_time' => $phoneBannerObject->created_time,
            'updated_time' => $phoneBannerObject->updated_time,
            'created_at' => $phoneBannerObject->created_at,
            'updated_at' => $phoneBannerObject->updated_at,
            'deleted_at' => $phoneBannerObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $phoneBannerObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加手机轮播图失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$phoneBannerObject' => $phoneBannerObject,'$adminObject' => $adminObject], 'AdminPhoneBannerFacadeService', 'handleError');
            throw new CommonException('EsAddPhoneBannerError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'AddPhoneBanner');

        $result = code(['code' => 0,'msg' => '添加轮播图成功!']);

        return $result;
    }


    /**
     * 更新轮播图
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updatePhoneBanner(UpdatePhoneBannerDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdatePhoneBannerError'));

        $validated = $requestDTO->toArray();

        $phoneBannerObject = PhoneBanner::find($validated['id']);

        if (!optional($phoneBannerObject)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$phoneBannerObject ->revision];

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

        $updateDataArray['revision'] = $phoneBannerObject ->revision + 1;

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        $updateDataArray['updated_time'] = time();

        $phoneBannerResult = PhoneBanner::where($where)->update($updateDataArray);

        if (!$phoneBannerResult) {
            throw new CommonException('UpdatePhoneBannerError');
        }

        $phoneBannerObject = $phoneBannerObject->fresh();

        $indexName = config('common_es.indices.business.phone_banners');

        $updateDataArray = [
            '_docId' => $phoneBannerObject->id,
            'id' => $phoneBannerObject->id,
            'album_picture_uid' => $phoneBannerObject->album_picture_uid,
            'redirect_url' => $phoneBannerObject->redirect_url,
            'note' => $phoneBannerObject->note,
            'sort' => $phoneBannerObject->sort,
            'created_time' => $phoneBannerObject->created_time,
            'updated_time' => $phoneBannerObject->updated_time,
            'created_at' => $phoneBannerObject->created_at,
            'updated_at' => $phoneBannerObject->updated_at,
            'deleted_at' => $phoneBannerObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($indexName, $phoneBannerObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新手机轮播图失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$phoneBannerObject' => $phoneBannerObject,'$adminObject' => $adminObject], 'AdminPhoneBannerFacadeService', 'handleError');
            throw new CommonException('EsUpdatePhoneBannerError');
        }

        CommonEvent::dispatch($adminObject, $phoneBannerObject, 'UpdatePhoneBanner');

        $result = code(['code' => 0,'msg' => '修改轮播图成功!']);

        return $result;
    }

    /**
     * 删除首页轮播图
     * @param {*} $validated
     * @param {*} $adminObject
     * @return {*}
     */
    public function deletePhoneBanner(DeletePhoneBannerDTO $requestDTO, Admin $adminObject)
    {
        //删除
        $result = code(config('admin_code.RestorePhoneBannerError'));

        $validated = $requestDTO->toArray();

        $eventName = 'RestorePhoneBanner';

        if ($validated['is_delete']) {
            $result = code(config('admin_code.DeletePhoneBannerError'));

            $eventName = 'DeletePhoneBanner';
        }

        if ($validated['is_delete']) {
            $phoneBannerObject = PhoneBanner::find($validated['id']);

            if (!optional($phoneBannerObject)) {
                throw new CommonException('ThisDataNotExistsError');
            }


            $phoneBannerObject->deleted_at = date('Y-m-d H:i:s', time());

            $phoneBannerResult =  $phoneBannerObject->save();
        } else {
            //恢复
            $phoneBannerObject = PhoneBanner::withTrashed()->find($validated['id']);

            $phoneBannerResult =  $phoneBannerObject->restore();
        }

        if (!$phoneBannerResult) {
            if ($validated['is_delete']) {
                throw new CommonException('DeletePhoneBannerError');
            }

            throw new CommonException('RestorePhoneBannerError');
        }

        $phoneBannerObject = $phoneBannerObject->fresh();

        $indexName = config('common_es.indices.business.phone_banners');

        $updateDataArray = [
            '_docId' => $phoneBannerObject->id,
            'id' => $phoneBannerObject->id,
            'album_picture_uid' => $phoneBannerObject->album_picture_uid,
            'redirect_url' => $phoneBannerObject->redirect_url,
            'note' => $phoneBannerObject->note,
            'sort' => $phoneBannerObject->sort,
            'created_time' => $phoneBannerObject->created_time,
            'updated_time' => $phoneBannerObject->updated_time,
            'created_at' => $phoneBannerObject->created_at,
            'updated_at' => $phoneBannerObject->updated_at,
            'deleted_at' => $phoneBannerObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($indexName, $phoneBannerObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es删除手机轮播图失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$phoneBannerObject' => $phoneBannerObject,'$adminObject' => $adminObject], 'AdminPhoneBannerFacadeService', 'handleError');
            throw new CommonException('EsDeletePhoneBannerError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, $eventName);

        $result = code(['code' => 0,'msg' => '恢复轮播图成功!']);

        if ($validated['is_delete']) {
            $result = code(['code' => 0,'msg' => '删除轮播图成功!']);
        }

        return $result;
    }

    /**
     * 批量删除
     *
     * @param  [type] $validated
     * @param  [type] $adminObject
     */
    public function multipleDeletePhoneBanner(MultipleDeletePhoneBannerDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MultipleDeletePhoneBannerError'));

        $validated = $requestDTO->toArray();

        $select_id_array = $requestDTO->select_id_array;


        $deleteResult = PhoneBanner::whereIn('id', $select_id_array)->delete();

        if (!$deleteResult) {
            throw new CommonException('MultipleDeletePhoneBannerError');
        }

        $indexName = config('common_es.indices.business.phone_banners');

        $phoneBannerCollection = PhoneBanner::withTrashed()->whereIn('id', $select_id_array)->get();

        $updateDataArray = [];

        foreach ($phoneBannerCollection as $phoneBannerObject) {
            $updateDataArray[] = [
                '_docId' => $phoneBannerObject->id,
                'id' => $phoneBannerObject->id,
                'album_picture_uid' => $phoneBannerObject->album_picture_uid,
                'redirect_url' => $phoneBannerObject->redirect_url,
                'note' => $phoneBannerObject->note,
                'sort' => $phoneBannerObject->sort,
                'created_time' => $phoneBannerObject->created_time,
                'updated_time' => $phoneBannerObject->updated_time,
                'created_at' => $phoneBannerObject->created_at,
                'updated_at' => $phoneBannerObject->updated_at,
                'deleted_at' => $phoneBannerObject->deleted_at,
            ];
        }

        $esResult = EsFacade::batchActDoc($indexName, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es批量更新手机轮播图失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$requestDTO' => $requestDTO,'$adminObject' => $adminObject], 'AdminPhoneBannerFacadeService', 'handleError');
            throw new CommonException('EsMutipleDeletePhoneBannerError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'MultipleDeletePhoneBanner');

        $result = code(['code' => 0,'msg' => '批量删除轮播图成功!']);


        return $result;
    }
}
