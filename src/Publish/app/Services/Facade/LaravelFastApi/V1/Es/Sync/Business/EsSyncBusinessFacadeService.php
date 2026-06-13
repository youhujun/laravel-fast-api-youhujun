<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-15 13:12:40
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-20 15:16:37
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Sync\Business\EsSyncBusinessFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Es\Sync\Business;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\Facade\Common\V1\Es\Console\Traits\EsFacadeServiceBaseTrait;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Attributes\Common\DocNote;
use App\Models\LaravelFastApi\V1\System\Module\Article\Category;
use App\Models\LaravelFastApi\V1\System\Module\Goods\GoodsClass;
use App\Models\LaravelFastApi\V1\System\Module\Label;
use App\Models\LaravelFastApi\V1\System\Level\LevelItem;
use App\Models\LaravelFastApi\V1\System\Level\UserLevel;
use App\Models\LaravelFastApi\V1\System\SystemConfig\WithdrawConfig;

/**
 * @see \App\Facades\LaravelFastApi\V1\Es\Sync\Business\EsSyncBusinessFacade
 */
class EsSyncBusinessFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsSyncBusinessFacadeService test";
    }

    /**
     *同步商品2分类
     */
    public function syncGoodsClass(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有goods_classes数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.business.goods_classes');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        GoodsClass::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $goodsClassCollection  = $chunk;

            $esDataArray = $goodsClassCollection->map(function ($goodsClassObject) {
                //p($systemConfigObject);
                return [
                    '_docId' => $goodsClassObject->id,
                    'id' => $goodsClassObject->id,
                    'parent_id' => $goodsClassObject->parent_id,
                    'deep' => $goodsClassObject->deep,
                    'switch' => $goodsClassObject->switch,
                    'rate' => $goodsClassObject->rate,
                    'goods_class_name' => $goodsClassObject->goods_class_name,
                    'goods_class_code' => $goodsClassObject->goods_class_code,
                    'goods_class_picture_uid' => $goodsClassObject->goods_class_picture_uid,
                    'is_certificate' => $goodsClassObject->is_certificate,
                    'certificate_number' => $goodsClassObject->certificate_number,
                    'note' => $goodsClassObject->note,
                    'sort' => $goodsClassObject->sort,
                    'created_time' => $goodsClassObject->created_time,
                    'updated_time' => $goodsClassObject->updated_time,
                    'created_at' => $goodsClassObject->created_at,
                    'updated_at' => $goodsClassObject->updated_at,
                    'deleted_at' => $goodsClassObject->deleted_at,
                ];
            })->toArray();
            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步商品分类失败','$result' => $result], 'EsSyncBusinessFacadeService', 'syncGoodsClassError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => 'es批量同步商品分类','total' => $total,'costTime' => $costTime], 'EsSyncBusinessFacadeService', 'syncGoodsClass');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有goods_classes数据同步ES结束--2', 'info');
        }
    }

    /**
     *同步文章分类
     */
    public function syncCategory(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有article_categories数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.business.article_categories');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        Category::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $categoryCollection  = $chunk;

            $esDataArray = $categoryCollection->map(function ($categoryObject) {
                //p($systemConfigObject);
                return [
                    '_docId' => $categoryObject->id,
                    'id' => $categoryObject->id,
                    'parent_id' => $categoryObject->parent_id,
                    'deep' => $categoryObject->deep,
                    'switch' => $categoryObject->switch,
                    'rate' => $categoryObject->rate,
                    'category_name' => $categoryObject->category_name,
                    'category_code' => $categoryObject->category_code,
                    'category_picture_uid' => $categoryObject->category_picture_uid,
                    'note' => $categoryObject->note,
                    'sort' => $categoryObject->sort,
                    'created_time' => $categoryObject->created_time,
                    'updated_time' => $categoryObject->updated_time,
                    'created_at' => $categoryObject->created_at,
                    'updated_at' => $categoryObject->updated_at,
                    'deleted_at' => $categoryObject->deleted_at,
                ];
            })->toArray();
            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步文章分类失败','$result' => $result], 'EsSyncBusinessFacadeService', 'syncCategoryError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => 'es批量同步同步文章分类','total' => $total,'costTime' => $costTime], 'EsSyncBusinessFacadeService', 'syncCategory');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有article_categories数据同步ES结束--2', 'info');
        }
    }

    /**
     *同步标签
     */
    public function syncLabel(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有labels数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.business.labels');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        Label::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $labelCollection = $chunk;

            $esDataArray = $labelCollection->map(function ($labelObject) {
                //p($systemConfigObject);
                return [
                    '_docId' => $labelObject->id,
                    'id' => $labelObject->id,
                    'parent_id' => $labelObject->parent_id,
                    'deep' => $labelObject->deep,
                    'switch' => $labelObject->switch,
                    'label_name' => $labelObject->label_name,
                    'label_code' => $labelObject->label_code,
                    'label_picture_uid' => $labelObject->label_picture_uid,
                    'note' => $labelObject->note,
                    'sort' => $labelObject->sort,
                    'created_time' => $labelObject->created_time,
                    'updated_time' => $labelObject->updated_time,
                    'created_at' => $labelObject->created_at,
                    'updated_at' => $labelObject->updated_at,
                    'deleted_at' => $labelObject->deleted_at,
                ];
            })->toArray();
            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步标签失败','$result' => $result], 'EsSyncBusinessFacadeService', 'syncLabelError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => 'es批量同步同步标签','total' => $total,'costTime' => $costTime], 'EsSyncBusinessFacadeService', 'syncLabel');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有labels数据同步ES结束--2', 'info');
        }
    }

    /**
     *同步级别配置项
     */
    public function syncLevelItem(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有level_items数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.business.level_items');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        LevelItem::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $levelItemCollection  = $chunk;

            $esDataArray = $levelItemCollection->map(function ($levelItemObject) {
                //p($systemConfigObject);
                return [
                    '_docId' => $levelItemObject->id,
                    'id' => $levelItemObject->id,
                    'type' => $levelItemObject->type,
                    'item_name' => $levelItemObject->item_name,
                    'item_code' => $levelItemObject->item_code,
                    'description' => $levelItemObject->description,
                    'created_time' => $levelItemObject->created_time,
                    'updated_time' => $levelItemObject->updated_time,
                    'created_at' => $levelItemObject->created_at,
                    'updated_at' => $levelItemObject->updated_at,
                    'deleted_at' => $levelItemObject->deleted_at,
                ];
            })->toArray();
            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步级别配置项失败','$result' => $result], 'EsSyncBusinessFacadeService', 'syncLevelItemError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => 'es批量同步级别配置项','total' => $total,'costTime' => $costTime], 'EsSyncBusinessFacadeService', 'syncLevelItem');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有article_categories数据同步ES结束--2', 'info');
        }
    }

    /**
     *同步用户级别
     */
    public function syncUserLevel(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有user_levels数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.business.user_levels');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        UserLevel::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $userLevelCollection  = $chunk;

            $esDataArray = $userLevelCollection->map(function ($userLevelObject) {
                //p($systemConfigObject);
                return [
                    '_docId' => $userLevelObject->id,
                    'id' => $userLevelObject->id,
                    'level_name' => $userLevelObject->level_name,
                    'level_code' => $userLevelObject->level_code,
                    'amount' => $userLevelObject->amount,
                    'background_picture_uid' => $userLevelObject->background_picture_uid,
                    'note' => $userLevelObject->note,
                    'sort' => $userLevelObject->sort,
                    'created_time' => $userLevelObject->created_time,
                    'updated_time' => $userLevelObject->updated_time,
                    'created_at' => $userLevelObject->created_at,
                    'updated_at' => $userLevelObject->updated_at,
                    'deleted_at' => $userLevelObject->deleted_at,
                ];
            })->toArray();
            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步用户级别失败','$result' => $result], 'EsSyncBusinessFacadeService', 'syncUserLevelError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => 'es批量同步用户级别','total' => $total,'costTime' => $costTime], 'EsSyncBusinessFacadeService', 'syncUserLevel');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有user_levels数据同步ES结束--2', 'info');
        }
    }

    /**
     *同步用户级别
     */
    public function syncWithdrawConfig(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有system_withdraw_configs数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.business.system_withdraw_configs');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        WithdrawConfig::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $withdrawConfigCollection  = $chunk;

            $esDataArray = $withdrawConfigCollection->map(function ($withdrawConfigObject) {
                //p($systemConfigObject);
                return [
                    '_docId' => $withdrawConfigObject->id,
                    'id' => $withdrawConfigObject->id,
                    'item_name' => $withdrawConfigObject->item_name,
                    'item_value' => $withdrawConfigObject->item_value,
                    'value_type' => $withdrawConfigObject->value_type,
                    'note' => $withdrawConfigObject->note,
                    'sort' => $withdrawConfigObject->sort,
                    'created_time' => $withdrawConfigObject->created_time,
                    'updated_time' => $withdrawConfigObject->updated_time,
                    'created_at' => $withdrawConfigObject->created_at,
                    'updated_at' => $withdrawConfigObject->updated_at,
                    'deleted_at' => $withdrawConfigObject->deleted_at,
                ];
            })->toArray();
            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步提现配置失败','$result' => $result], 'EsSyncBusinessFacadeService', 'syncWithdrawConfigError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => 'es批量同步提现配置','total' => $total,'costTime' => $costTime], 'EsSyncBusinessFacadeService', 'syncWithdrawConfig');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有system_withdraw_configs数据同步ES结束--2', 'info');
        }
    }
}
