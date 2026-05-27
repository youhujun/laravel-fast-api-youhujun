<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-11-28 14:42:33
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-06 22:22:08
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminWithdrawConfigFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\WithdrawConfig\UpdateWithdrawConfigDTO;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\SystemConfig\WithdrawConfig;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\Withdraw\EsSystemWithdrawConfigResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\Withdraw\EsSystemWithdrawConfigCollection;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\WithdrawConfigController
 * @see \App\Facades\LaravelFastApi\V1\Admin\System\SystemConfig\AdminWithdrawConfigFacade
 */
class AdminWithdrawConfigFacadeService
{
    public function test()
    {
        echo "AdminWithdrawConfigFacadeService test";
    }

    /**
     * 获取提现配置
     *
     * @param  [type] $validated
     * @param  [type] $adminObject
     */
    public function getWithdrawConfig(Admin $adminObject)
    {
        $result = code(config('admin_code.GetWithdrawConfigError'));

        $indexName = config('common_es.indices.business.system_withdraw_configs');

        $withdrawConfigCollection = EsQueryFacade::index($indexName)->limit(1000)->get();

        //p($withdrawConfigCollection);die;

        $result = new EsSystemWithdrawConfigCollection($withdrawConfigCollection, ['code' => 0,'msg' => '获取系统提现配置成功!']);

        return $result;
    }

    /**
     * 修改提现配置
     */
    public function updateWithdrawConfig(UpdateWithdrawConfigDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateWithdrawConfigError'));

        $indexName = config('common_es.indices.business.system_withdraw_configs');

        $esWithdrawConfigObject = EsQueryFacade::index($indexName)->where('id', $requestDTO->id)->get()->first();

        if (!$esWithdrawConfigObject) {
            throw new CommonException('ServiceBusyError');
        }

        $withdrawConfigObject = WithdrawConfig::find($requestDTO->id);

        if (!$withdrawConfigObject) {
            throw new CommonException('ThatDataNotExistError');
        }

        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$withdrawConfigObject ->revision];
        $where[] = ['id','=',$withdrawConfigObject ->id];

        $updateDataArray['revision'] = $withdrawConfigObject ->revision + 1;

        $updateDataArray['updated_time'] = time();

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        $updateDataArray['item_value']  = $requestDTO->item_value;

        $updateResult = WithdrawConfig::where($where)->update($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('UpdateWithdrawConfigError');
        }

        $withdrawConfigObject = $withdrawConfigObject->fresh();

        CommonEvent::dispatch($adminObject, $requestDTO, 'UpdateWithdrawConfig');

        $indexName = config('common_es.indices.business.system_withdraw_configs');

        $updateDataArray = [
            'item_name' => $withdrawConfigObject->item_name,
            'item_value' => $withdrawConfigObject->item_value,
            'value_type' => $withdrawConfigObject->value_type,
            'note' => $withdrawConfigObject->note,
            'sort' => $withdrawConfigObject->sort,
            'updated_time' => $withdrawConfigObject->updated_time,
            'updated_at' => $withdrawConfigObject->updated_at,
        ];

        $esResult = EsFacade::updateDoc($indexName, $withdrawConfigObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新提现配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$withdrawConfigObject' => $withdrawConfigObject,'$adminObject' => $adminObject], 'AdminWithdrawConfigFacadeService', 'updateWithdrawConfigError');
            throw new CommonException('EsUpdateWithdrawConfigError');
        }

        $result = code(['code' => 0,'msg' => '修改系统提现配置成功']);

        return $result;
    }
}
