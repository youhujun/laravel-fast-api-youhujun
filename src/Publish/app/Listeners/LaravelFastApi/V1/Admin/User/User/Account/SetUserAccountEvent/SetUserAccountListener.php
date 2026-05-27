<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-11 14:12:02
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-17 01:04:46
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\User\User\Account\SetUserAccountEvent\SetUserAccountListener.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\User\User\Account\SetUserAccountEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Account\SetUserAccountDTO;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\Log\UserAmountLog;
use App\Models\LaravelFastApi\V1\User\Log\UserCoinLog;
use App\Models\LaravelFastApi\V1\User\Log\UserScoreLog;
use App\Models\LaravelFastApi\V1\User\Info\UserAmount;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\User\User\Account\SetUserAccountEvent
 */
class SetUserAccountListener
{
    protected $modelMapArray = [
        10 => UserAmountLog::class,
        20 => UserCoinLog::class,
        30 => UserScoreLog::class,
    ];

    protected $esIndexMapArray = [
        10 => 'user_amount_logs',
        20 => 'user_coin_logs',
        30 => 'user_score_logs'
    ];

    protected $noteMapArray = [
        10 => [
            10 => '后台充值余额',
            20 => '后台充值系统币',
            30 => '后台增加积分'
        ],
        20 => [
            10 => '后台扣除余额',
            20 => '后台扣除系统币',
            30 => '后台扣除积分'
        ]
    ];

    protected $fieldMapArray = [
        10 => 'amount',
        20 => 'coin',
        30 => 'score'
    ];

    protected $primaryMapArray = [
        10 => 'user_amount_log_uid',
        20 => 'user_coin_log_uid',
        30 => 'user_score_log_uid'
    ];
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $adminObject = $event->adminObject;
        $requestDTO = $event->requestDTO;
        $isTransation = $event->isTransation;

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;
        $account_type = $requestDTO->account_type;
        $action_type = $requestDTO->action_type;
        $amount = $requestDTO->amount;

        $userAmountObject = UserAmount::queryByShard($user_uid)->where('user_uid', $user_uid)->first();

        // 记录变更前的金额
        $beforeAmount = 0;
        $beforeAmountLogObject = $this->modelMapArray[$account_type]::queryByShard($user_uid)->where('user_uid', $user_uid)->orderBy('created_time', 'desc')->first();

        if ($beforeAmountLogObject) {
            $beforeAmount = $beforeAmountLogObject->amount;
        } else {
            $beforeAmount = 0;
        }

        //记录现在的金额
        $fieldName = $this->fieldMapArray[$account_type];

        $insertDataArray = [
            'change_value' => $amount,
            'sort' => 100,
            'change_type' => $action_type,
            'user_uid' => $user_uid,
            'before_amount' => $beforeAmount,
            'amount' => $userAmountObject->{$fieldName},
            'note' => $this->noteMapArray[$action_type][$account_type]
        ];

        $amountLogObject = ShardHelperFacade::createWithShard($this->modelMapArray[$account_type], $user_uid, $insertDataArray);

        if (!isset($amountLogObject->biz_id)) {
            plog(['error' => '设置用户账户日志错误','$adminObject' => '$adminObject','$requestDTO' => $requestDTO], 'SetUserAccountListener', 'handleError');

            if($isTransation){
                DB::rollBack();
            }
           
            throw new CommonException('SetUserAccountLogError');
        }

        $indexName = config("common_es.indices.logs.{$this->esIndexMapArray[$account_type]}");

        $configKey = get_shard_config_key();

        $esDataArray = [
            '_docId' => $amountLogObject->biz_id,
            $this->primaryMapArray[$account_type] => $amountLogObject->biz_id,
            'shard_key' => $amountLogObject->shard_key,
            'shard_db' => ShardFacade::getDbName($user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($user_uid, $this->esIndexMapArray[$account_type], $configKey),
            'created_at' => $amountLogObject->created_at,
            'created_time' => $amountLogObject->created_time,
            'updated_at' => $amountLogObject->updated_at,
            'updated_time' => $amountLogObject->updated_time,
            'deleted_at' => $amountLogObject->deleted_at,
        ];

        $esInsertDataArray = array_merge($insertDataArray, $esDataArray);

        $esResult = EsFacade::createDoc($indexName, $esInsertDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => '设置用户账户日志错误','$adminObject' =>$adminObject,'$requestDTO' => $requestDTO,'$indexName' => $indexName,'$esInsertDataArray' => $esInsertDataArray], 'SetUserAccountListener', 'handleError');

           if($isTransation){
                DB::rollBack();
            }
           
            throw new CommonException('EsSetUserAccountLogError');
        }
    }
}
