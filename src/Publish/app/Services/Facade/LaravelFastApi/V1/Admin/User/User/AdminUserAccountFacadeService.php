<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-09 19:49:11
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-17 00:43:01
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserAccountFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use App\Events\LaravelFastApi\V1\Admin\User\User\Account\SetUserAccountEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Account\SetUserAccountDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Account\GetUserAccountLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Account\GetUserAccountInfoDTO;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\Info\UserAmount;
use App\Models\LaravelFastApi\V1\User\Log\UserAmountLog;
use App\Models\LaravelFastApi\V1\User\Log\UserCoinLog;
use App\Models\LaravelFastApi\V1\User\Log\UserScoreLog;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\UserInfo\EsUserAccountLogCollection;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\UserInfo\EsUserAmountArrayResource;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserAccountController
 * @see \App\Facades\Admin\User\User\AdminUserAccountFacade
 */
class AdminUserAccountFacadeService
{
    public function test()
    {
        echo "AdminUserAccountFacadeService test";
    }

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

    protected $fieldMapArray = [
        10 => 'amount',
        20 => 'coin',
        30 => 'score'
    ];

    
    protected static $sortMapArray = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];

    

    /**
     * 后台修改用户账户余额
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function setUserAccount(SetUserAccountDTO $requestDTO, Admin $adminObject)
    {
        $result = code(\config('admin_code.SetUserAccountError'));

        $validated = $requestDTO->toArray();

        $action_type = $requestDTO->action_type;
        $account_type = $requestDTO->account_type;
        $amount = $requestDTO->amount;

        //操作类型 10充值 20扣除
        $action_type_check_array = [10,20];

        if (!in_array($action_type, $action_type_check_array)) {
            throw new CommonException('SetUserAccountActionTypeError');
        }

        //账户类型 10 余额 20系统币 30积分
        $account_type_check_array = [10,20,30];

        if (!in_array($account_type, $account_type_check_array)) {
            throw new CommonException('SetUserAccountTypeError');
        }

        $userAmountObject = UserAmount::queryByShard($requestDTO->user_uid)->where('user_uid', $requestDTO->user_uid)->first();

        if (!$userAmountObject) {
            throw new CommonException('ThisUserNotExistsError');
        }

        //账户映射数组
        $account_map_array = [10 => $userAmountObject->amount,20 => $userAmountObject->coin, 30 => $userAmountObject->score];

        $updateDataArray = [];

        //判断操作类型 10 充值 20扣除
        if ($action_type == 10) {
            $updateDataArray[$this->fieldMapArray[$account_type]] = \bcadd($account_map_array[$account_type], $requestDTO->amount, 2);
        }

        if ($action_type == 20) {
            //相减的话需要比较一下大小 不能减出负数
            // 0== > 1 < -1
            $bccompResult = bccomp($requestDTO->amount, $account_map_array[$account_type]);

            if ($bccompResult == 1) {
                throw new CommonException('SetUserAccountNotEnoughError');
            }

            $updateDataArray[$this->fieldMapArray[$account_type]] = \bcsub($account_map_array[$account_type], $requestDTO->amount, 2);
        }

        $accountResult = $userAmountObject->updateWithShard($updateDataArray);

        if (!$accountResult) {
            DB::rollBack();

            throw new CommonException('SetUserAccountError');
        }

        $userAmountObject = $userAmountObject->fresh();

        //es更新用户账户信息
        $indexName = config('common_es.indices.user.user_amounts');

        $updateDataArray = [
            'amount'=>$userAmountObject->amount,
            'coin'=>$userAmountObject->coin,
            'score'=>$userAmountObject->score,
            'bonus'=>$userAmountObject->bonus,
            'prepare_bonus'=>$userAmountObject->prepare_bonus,
            'updated_at'=>$userAmountObject->updated_at,
            'updated_time'=>$userAmountObject->updated_time,
            'note'=>$userAmountObject->note,
            'sort'=>$userAmountObject->sort,
        ];

        $esResult = EsFacade::updateDoc($indexName, $userAmountObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es设置用户账户错误','$userAmountObject' => $userAmountObject,'$requestDTO' => $requestDTO,'$indexName' => $indexName,'$updateDataArray' => $updateDataArray], 'AdminUserAccountFacadeService', 'handleError');

           if($isTransation){
                DB::rollBack();
            }
           
            throw new CommonException('EsSetUserAccountError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'SetUserAccount', true);

        SetUserAccountEvent::dispatch($adminObject, $requestDTO,true);

        DB::commit();

        $result = code(['code' => 0,'msg' => '设置用户账户成功!']);

        return $result;
    }

    /**
     * 获取用户账户日志
     *
     * @param  [type] $validated
     * @param  [type] $adminObject
     */
    public function getUserAccountLog(GetUserAccountLogDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetUserAccountLogError'));

        $validated = $requestDTO->toArray();

        //分页参数
        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;
        $pageName = 'page';

        $indexName = config("common_es.indices.logs.{$this->esIndexMapArray[$validated['account_type']]}");

        $esQuery = EsQueryFacade::index($indexName);

        $esQuery->whereNull('deleted_at');

        $esQuery->where('user_uid', $validated['user_uid']);

        if (isset($requestDTO->action_type) && $requestDTO->action_type) {
            $esQuery->where('change_type', $requestDTO->action_type);
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

        $accountLogPaginator = $esQuery->paginate($perPage, $currentPage);

        //p($accountLogList);die;

        if (\optional($accountLogPaginator)) {
            $result = new EsUserAccountLogCollection($accountLogPaginator, ['code' => 0,'msg' => '获取用户账户成功!'], null);
        }

        return  $result;
    }

    /**
     * 获取用户账户信息
     */
    public function getUserAccountInfo(GetUserAccountInfoDTO $requestDTO, Admin $adminObject)
    {
        $result = ['code'=>10000,'msg'=>'获取用户账户信息失败'];

        $validated = $requestDTO->toArray();

        $indexName = config('common_es.indices.user.user_amounts');

        $esUserAccountObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid',$requestDTO->user_uid)->get()->first();

        //熔断降级
        if (!isset($esUserAccountObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        // p($userAmuont);die;
        $result = ['code' => 0,'msg' => '获取用户账户信息成功','data' => new EsUserAmountArrayResource($esUserAccountObject)];

        return $result;
    }
}
