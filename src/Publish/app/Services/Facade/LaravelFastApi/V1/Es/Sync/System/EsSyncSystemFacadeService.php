<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-20 14:49:20
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-06-14 04:50:39
 * @FilePath: \youhu-laravel-api-13\app\Services\Facade\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacadeService.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Es\Sync\System;

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
use App\Models\LaravelFastApi\V1\System\SystemConfig;
use App\Models\LaravelFastApi\V1\System\Permission\Permission;
use App\Models\LaravelFastApi\V1\System\SystemConfig\SystemVoiceConfig;
use App\Models\LaravelFastApi\V1\System\Role\Role;
use App\Models\LaravelFastApi\V1\System\Region\Region;
use App\Models\LaravelFastApi\V1\System\Module\Bank;
use App\Models\LaravelFastApi\V1\System\Platform\SystemWechatConfig;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;

/**
 * @see \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade
 */
class EsSyncSystemFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsSyncSystemFacadeService test";
    }

    public function __construct()
    {
    }

    /**
     * 执行数据同步
     */
    public function syncSystemConfig(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有system_configs数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.system.system_configs');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        SystemConfig::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $systemConfigCollection  = $chunk;

            $esDataArray = $systemConfigCollection->map(function ($systemConfigObject) {
                //p($systemConfigObject);
                return [
                    '_docId' => $systemConfigObject->id,
                    'id' => $systemConfigObject->id,
                    'parent_id' => $systemConfigObject->parent_id,
                    'deep' => $systemConfigObject->deep,
                    'item_type' => $systemConfigObject->item_type,
                    'item_label' => $systemConfigObject->item_label,
                    'item_value' => $systemConfigObject->item_value,
                    'item_price' => $systemConfigObject->item_price,
                    'item_path' => $systemConfigObject->item_path,
                    'item_introduction' => $systemConfigObject->item_introduction,
                    'sort' => $systemConfigObject->sort,
                    'created_time' => $systemConfigObject->created_time,
                    'updated_time' => $systemConfigObject->updated_time,
                    'created_at' => $systemConfigObject->created_at,
                    'updated_at' => $systemConfigObject->updated_at,
                    'deleted_at' => $systemConfigObject->deleted_at,
                ];
            })->toArray();
            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步系统配置失败','$result' => $result], 'EsSyncSystemConfigFacadeService', 'syncSystemConfigError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => '批量同步系统配置完成','total' => $total,'costTime' => $costTime], 'EsSyncSystemConfigFacadeService', 'syncSystemConfig');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有system_configs数据同步ES结束--2', 'info');
        }
    }

    /**
     *同步菜单
     */
    public function syncPermission(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有permissions数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.system.permissions');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        Permission::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $permissionCollection  = $chunk;

            $esDataArray = $permissionCollection->map(function ($permissionObject) {
                //p($systemConfigObject);
                return [
                    '_docId' => $permissionObject->id,
                    'id' => $permissionObject->id,
                    'parent_id' => $permissionObject->parent_id,
                    'deep' => $permissionObject->deep,
                    'type' => $permissionObject->type,
                    'route_name' => $permissionObject->route_name,
                    'route_path' => $permissionObject->route_path,
                    'component' => $permissionObject->component,
                    'hidden' => $permissionObject->hidden,
                    'always_show' => $permissionObject->always_show,
                    'redirect' => $permissionObject->redirect,
                    'permission_tag' => $permissionObject->permission_tag,
                    'meta_title' => $permissionObject->meta_title,
                    'meta_icon' => $permissionObject->meta_icon,
                    'meta_no_cache' => $permissionObject->meta_no_cache,
                    'meta_affix' => $permissionObject->meta_affix,
                    'meta_breadcrumb' => $permissionObject->meta_breadcrumb,
                    'meta_active_menu' => $permissionObject->meta_active_menu,
                    'switch' => $permissionObject->switch,
                    'sort' => $permissionObject->sort,
                    'created_time' => $permissionObject->created_time,
                    'updated_time' => $permissionObject->updated_time,
                    'created_at' => $permissionObject->created_at,
                    'updated_at' => $permissionObject->updated_at,
                    'deleted_at' => $permissionObject->deleted_at,
                ];
            })->toArray();
            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步权限菜单失败','$result' => $result], 'EsSyncSystemConfigFacadeService', 'syncPermissionError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => 'es批量同步权限菜单','total' => $total,'costTime' => $costTime], 'EsSyncSystemConfigFacadeService', 'syncPermission');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有permissions数据同步ES结束--2', 'info');
        }
    }

    /**
     *同步提示音配置
     */
    public function syncSystemVoiceCOnfig(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有system_voice_configs数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.system.system_voice_configs');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        SystemVoiceConfig::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $systemVoiceConfigCollection  = $chunk;

            $esDataArray = $systemVoiceConfigCollection->map(function ($systemVoiceConfigObject) {
                //p($systemConfigObject);
                return [
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
            })->toArray();
            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步系统提示音失败','$result' => $result], 'EsSyncSystemConfigFacadeService', 'syncSystemVoiceCOnfigError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => 'es批量同步系统提示音','total' => $total,'costTime' => $costTime], 'EsSyncSystemConfigFacadeService', 'syncSystemVoiceCOnfig');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有system_voice_configs数据同步ES结束--2', 'info');
        }
    }

    /**
     *同步角色
     */
    public function syncRole(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有roles数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.system.roles');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        Role::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $roleCollection  = $chunk;

            $esDataArray = $roleCollection->map(function ($roleObject) {
                //p($systemConfigObject);
                return [
                    '_docId' => $roleObject->id,
                    'id' => $roleObject->id,
                    'parent_id' => $roleObject->parent_id,
                    'deep' => $roleObject->deep,
                    'type' => $roleObject->type,
                    'is_system' => $roleObject->is_system,
                    'switch' => $roleObject->switch,
                    'role_name' => $roleObject->role_name,
                    'logic_name' => $roleObject->logic_name,
                    'sort' => $roleObject->sort,
                    'created_time' => $roleObject->created_time,
                    'updated_time' => $roleObject->updated_time,
                    'created_at' => $roleObject->created_at,
                    'updated_at' => $roleObject->updated_at,
                    'deleted_at' => $roleObject->deleted_at,
                ];
            })->toArray();
            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步角色失败','$result' => $result], 'EsSyncSystemConfigFacadeService', 'syncRoleError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => 'es批量同步角色','total' => $total,'costTime' => $costTime], 'EsSyncSystemConfigFacadeService', 'syncRole');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有roles数据同步ES结束--2', 'info');
        }
    }

    /**
     *同步地区
     */
    public function syncRegion(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有regions数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.system.regions');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        Region::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $RegionCollection  = $chunk;

            $esDataArray = $RegionCollection->map(function ($regionObject) {
                //p($systemConfigObject);
                return [
                    '_docId' => $regionObject->id,
                    'id' => $regionObject->id,
                    'parent_id' => $regionObject->parent_id,
                    'deep' => $regionObject->deep,
                    'region_name' => $regionObject->region_name,
                    'region_area' => $regionObject->region_area,
                    'latitude' => $regionObject->latitude,
                    'longitude' => $regionObject->longitude,
                    'sort' => $regionObject->sort,
                    'created_time' => $regionObject->created_time,
                    'updated_time' => $regionObject->updated_time,
                    'created_at' => $regionObject->created_at,
                    'updated_at' => $regionObject->updated_at,
                    'deleted_at' => $regionObject->deleted_at,
                ];
            })->toArray();
            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步地区失败','$result' => $result], 'EsSyncSystemConfigFacadeService', 'syncRegionError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => 'es批量同步地区','total' => $total,'costTime' => $costTime], 'EsSyncSystemConfigFacadeService', 'syncRegion');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有regions数据同步ES结束--2', 'info');
        }
    }

    /**
     *同步银行
     */
    public function syncBank(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有banks数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.system.banks');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        Bank::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $bankCollection  = $chunk;

            $esDataArray = $bankCollection->map(function ($bankObject) {
                //p($systemConfigObject);
                return [
                    '_docId' => $bankObject->id,
                    'id' => $bankObject->id,
                    'bank_name' => $bankObject->bank_name,
                    'bank_code' => $bankObject->bank_code,
                    'is_default' => $bankObject->is_default,
                    'sort' => $bankObject->sort,
                    'created_time' => $bankObject->created_time,
                    'updated_time' => $bankObject->updated_time,
                    'created_at' => $bankObject->created_at,
                    'updated_at' => $bankObject->updated_at,
                    'deleted_at' => $bankObject->deleted_at,
                ];
            })->toArray();
            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步银行失败','$result' => $result], 'EsSyncSystemConfigFacadeService', 'syncBankError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => 'es批量同步银行','total' => $total,'costTime' => $costTime], 'EsSyncSystemConfigFacadeService', 'syncBank');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有banks数据同步ES结束--2', 'info');
        }
    }

    /**
     * 同步系统微信配置
     */
    public function syncSystemWechatConfig(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有system_wechat_configs数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.system.system_wechat_configs');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        SystemWechatConfig::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $systemWechatConfigCollection  = $chunk;

            $esDataArray = $systemWechatConfigCollection->map(function ($systemWechatConfigObject) {
                //p($systemConfigObject);
                return [
                    '_docId' => $systemWechatConfigObject->id,
                    'id' => $systemWechatConfigObject->id,
                    'name' => $systemWechatConfigObject->name,
                    'type' => $systemWechatConfigObject->type,
                    'appid' => $systemWechatConfigObject->appid,
                    'appsecret' => $systemWechatConfigObject->appsecret,
                    'note' => $systemWechatConfigObject->note,
                    'sort' => $systemWechatConfigObject->sort,
                    'created_time' => $systemWechatConfigObject->created_time,
                    'updated_time' => $systemWechatConfigObject->updated_time,
                    'created_at' => $systemWechatConfigObject->created_at,
                    'updated_at' => $systemWechatConfigObject->updated_at,
                    'deleted_at' => $systemWechatConfigObject->deleted_at,
                ];
            })->toArray();
            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步系统微信配置失败','$result' => $result], 'EsSyncSystemConfigFacadeService', 'syncSystemWechatConfigError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => 'es批量同步系统微信配置','total' => $total,'costTime' => $costTime], 'EsSyncSystemConfigFacadeService', 'syncSystemWechatConfig');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有banks数据同步ES结束--2', 'info');
        }
    }

}
