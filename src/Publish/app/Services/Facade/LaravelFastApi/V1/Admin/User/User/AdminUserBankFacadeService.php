<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-09 19:48:46
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-12 18:58:45
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserBankFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Bank\AddUserBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Bank\SetUserDefaultBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Bank\DeleteUserBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Bank\GetUserBankDTO;
//event
use App\Events\LaravelFastApi\V1\Admin\User\User\UserBank\EsAddUserBankEvent;

//用户银行卡
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\Info\UserBank;
use App\Models\LaravelFastApi\V1\System\Module\Bank;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\User\UserBank\EsUserBankCollection;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserBankController
 * @see \App\Facades\Admin\User\User\AdminUserBankFacade
 */
class AdminUserBankFacadeService
{
    public function test()
    {
        echo "AdminUserBankFacadeService test";
    }

    protected static $sort = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];

    /**
      * 添加用户银行卡
      *
      * @param [type] $validated
      * @param [type] $adminObject
      * @return void
      */
    public function addUserBank(AddUserBankDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddUserBankError'));

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;
        $is_default = $requestDTO->is_default;

        $indexName = config('common_es.indices.user.user_banks');

        //如果设置为默认银行卡
        if ($is_default == 1) {
            //先查询是否有默认银行卡
            $esOriginUserBankObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid',$user_uid)->where('is_default', 1)->get()->first();

            if (isset($esOriginUserBankObject->user_uid) ) {
               
                    //先把数据库默认银行卡改为非默认
                    $originUserBankObject = UserBank::queryByShard($user_uid)->where('is_default', 1)->where('user_uid', $user_uid)->first();

                    $originUpdateArray = [
                        'is_default' => 0
                    ];

                    $originUpdateResult = $originUserBankObject->updateWithShard($originUpdateArray);

                    if (!$originUpdateResult) {
                        throw new CommonException('CancelDefaultUserBankError');
                    }

                    $originUserBankObject = $originUserBankObject->fresh();

                    $indexName = config('common_es.indices.user.user_banks');

                    $updateDataArray = [
                        'is_default' => 0,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_time' => time(),
                    ];

                    $esResult = EsFacade::updateDoc($indexName, $originUserBankObject->biz_id, $updateDataArray);

                    if (!isset($esResult['code']) || $esResult['code'] != 0) {
                        plog(['error' => 'es取消默认银行卡失败','esResult' => $esResult,'originUserBankObject' => $originUserBankObject,'adminObject' => $adminObject], 'AdminUserBankFacadeService', 'addUserBankError');

                        throw new CommonException('EsCancelDefaultUserBankError');
                    }
                
            }
        }

        $insertDataArray = [
            'user_uid' => $validated['user_uid'],
            'bank_id' => $validated['bank_id'],
            'bank_number' => $validated['bank_number'],
            'bank_account' => $validated['bank_account'],
            'bank_address' => $validated['bank_address'],
            'bank_front_uid' => $validated['bank_front_uid'],
            'bank_back_uid' => $validated['bank_back_uid'],
            'is_default' => $validated['is_default'],
            'sort' => $validated['sort'],
        ];

        $userBankObject = ShardHelperFacade::createWithShard(UserBank::class, $validated['user_uid'], $insertDataArray);

        if (!isset($userBankObject->biz_id)) {
            throw new CommonException('AddUserBankError');
        }

        EsAddUserBankEvent::dispatch($userBankObject, $adminObject,true);

        CommonEvent::dispatch($adminObject, $validated, 'AddUserBank');

        $result =  code(['code' => 0,'msg' => '添加用户银行卡成功!']);

        return $result;
    }


    /**
     * 设置默认银行
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function setUserDefaultBank(SetUserDefaultBankDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.SetDefaultUserBankError'));

        $validated = $requestDTO->toArray();
        $user_uid = $requestDTO->user_uid;
        $user_bank_uid = $requestDTO->user_bank_uid;

        //先查询是否有默认银行卡
        $indexName = config('common_es.indices.user.user_banks');

        //先查询是否有默认银行卡
        //把现在的银行卡设置为默认
        //先查询是否有默认银行卡
        $esOriginUserBankObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid',$user_uid)->where('is_default', 1)->get()->first();

        if (isset($esOriginUserBankObject->user_uid) ) {
               
                //先把数据库默认银行卡改为非默认
                $originUserBankObject = UserBank::queryByShard($user_uid)->where('is_default', 1)->where('user_uid', $user_uid)->first();

                $originUpdateArray = [
                    'is_default' => 0
                ];

                $originUpdateResult = $originUserBankObject->updateWithShard($originUpdateArray);

                if (!$originUpdateResult) {
                    throw new CommonException('CancelDefaultUserBankError');
                }

                $originUserBankObject = $originUserBankObject->fresh();

                $indexName = config('common_es.indices.user.user_banks');

                $updateDataArray = [
                    'is_default' => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_time' => time(),
                ];

                $esResult = EsFacade::updateDoc($indexName, $originUserBankObject->biz_id, $updateDataArray);

                if (!isset($esResult['code']) || $esResult['code'] != 0) {
                    plog(['error' => 'es取消默认银行卡失败','esResult' => $esResult,'originUserBankObject' => $originUserBankObject,'adminObject' => $adminObject], 'AdminUserBankFacadeService', 'addUserBankError');

                    throw new CommonException('EsCancelDefaultUserBankError');
                }
                
        }

        $userBankObject = UserBank::queryByShard($user_uid)->where('is_default', 0)->where('user_bank_uid', $user_bank_uid)->first();

        if (!isset($userBankObject->biz_id)) {
            throw new CommonException('ThatDataNotExistError');
        }

        $updateDataArray = [
            'is_default' => 1
        ];

        $updateResult = $userBankObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('SetDefaultUserBankError');
        }

        $userBankObject = $userBankObject->fresh();

        $indexName = config('common_es.indices.user.user_banks');

        $updateDataArray = [
            'is_default' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_time' => time(),
        ];

        $esResult = EsFacade::updateDoc($indexName, $userBankObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es设置默认银行卡失败','esResult' => $esResult,'userBankObject' => $userBankObject,'adminObject' => $adminObject], 'AdminUserBankFacadeService', 'setUserDefaultBankError');

            throw new CommonException('EsSetDefaultUserBankError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'SetDefaultUserBank');

        $result =  code(['code' => 0,'msg' => '设置用户默认银行卡成功!']);

        return $result;
    }

    /**
    * 删除银行
    *
    * @param [type] $validated
    * @param [type] $adminObject
    * @return void
    */
    public function deleteUserBank(DeleteUserBankDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteUserBankError'));

        $validated = $requestDTO->toArray();

        $user_bank_uid = $requestDTO->user_bank_uid;

        $indexName = config('common_es.indices.user.user_banks');

        $esUserBankObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_bank_uid', $user_bank_uid)->get()->first();

        //降级熔断
        if (!isset($esUserBankObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $userBankObject = UserBank::queryByShard($esUserBankObject->user_uid)->where('user_bank_uid', $user_bank_uid)->first();

        if (!isset($userBankObject->biz_id)) {
            throw new CommonException('ThatDataNotExistError');
        }

        $deleteResult = $userBankObject->delete();

        if (!$deleteResult) {
            throw new CommonException('DeleteUserBankError');
        }

        $userBankObject = $userBankObject->fresh();

        $indexName = config('common_es.indices.user.user_banks');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_time' => time(),
        ];

        $esResult = EsFacade::updateDoc($indexName, $userBankObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es删除银行卡失败','esResult' => $esResult,'userBankObject' => $userBankObject,'adminObject' => $adminObject], 'AdminUserBankFacadeService', 'deleteUserBankError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'DeleteUserBank');

        $result = code(['code' => 0,'msg' => '删除用户银行卡成功!']);

        return $result;
    }


    /**
     *获取用户银行卡
     *
     * @return void
     */
    public function getUserBank(GetUserBankDTO $requestDTO, Admin $adminObject)
    {
        $result = ['code' => 10000,'msg' => '获取用户银行卡失败!'];

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;

        $indexName = config('common_es.indices.user.user_banks');

        $esQuery = EsQueryFacade::index($indexName);

        $esQuery->whereNull('deleted_at');

        $userBankColelction = $esQuery->where('user_uid', $user_uid)->get();

        if (\optional($userBankColelction)) {
            $result = new EsUserBankCollection($userBankColelction, ['code' => 0,'msg' => '获取用户银行卡成功!']);
        }

        return $result;
    }
}
