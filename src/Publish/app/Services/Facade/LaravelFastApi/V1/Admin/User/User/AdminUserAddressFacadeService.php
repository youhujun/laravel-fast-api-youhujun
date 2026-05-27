<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-09 19:48:35
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-12 14:09:43
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserAddressFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
//Job
use App\Jobs\LaravelFastApi\V1\Admin\User\User\EsCancelDefaultUserAddressJob;
use App\Jobs\LaravelFastApi\V1\Admin\User\User\EsAddUserAddressJob;
use App\Jobs\LaravelFastApi\V1\Admin\User\User\EsSetDefaultUserAddreessJob;
use App\Jobs\LaravelFastApi\V1\Admin\User\User\EsDeleteUserAddreessJob;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Address\AddUserAddressDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Address\GetUserAddressDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Address\DeleteUserAddressDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Address\SetDefaultUserAddressDTO;
use App\Services\Facade\Traits\V1\QueryService;
//用户地址
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserAddress;
use App\Models\LaravelFastApi\V1\System\Region\Region;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\UserAddress\EsUserAddressCollection;

/**

 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserAddressController
 * @see  \App\Facades\Admin\User\User\AdminUserAddressFacade
 */
class AdminUserAddressFacadeService
{
    use QueryService;
    public function test()
    {
        echo "AdminUserAddressFacadeService test";
    }

    protected static $sortMapArray = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];


    /**
     * 添加用户地址
     *
     * @param [type] $validated
     * @param [type] $administrator
     * @return void
     */
    public function addUserAddress(AddUserAddressDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddUserAddressError'));

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;
        $user_name = $requestDTO->user_name;
        $phone = $requestDTO->phone;
        $is_default = $requestDTO->is_default;

        //es的用户索引
        $userIndexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($userIndexName)->where('user_uid', $user_uid)->get()->first();

        //熔断降级
        if (!isset($esUserObject->user_uid) ) {
            throw new CommonException('ServiceBusyError');
        }

        //如果未设置联系人和手机号,就使用默认的用户和手机号
        if (!isset($requestDTO->user_name) || !$requestDTO->user_name) {
            $user_name = $esUserObject->nick_name ?? '保密';
        }
        if (!isset($requestDTO->phone) || !$requestDTO->phone) {
            $phone = $esUserObject->phone ?? '';
        }

        //es的用户地址索引
        $userAddressIndexName = config('common_es.indices.user.user_addresses');

        //先判断改地址是否是默认地址
        //是的话先查询用户是否有默认地址,如果有的话,就把默认地址设置为非默认地址
        if ($requestDTO->is_default) {

            $esUserAddressObject = EsQueryFacade::index($userAddressIndexName)->whereNull('deleted_at')->where('user_uid', $user_uid)->where('is_default', 1)->get()->first();
            
            if (isset($esUserAddressObject->user_uid)) {
               
                    //1先把数据库的默认地址设置为非默认地址
                    $originDefaultUserAddressObject = UserAddress::queryByShard($esUserAddressObject->user_uid)->where('is_default', 1)->first();

                    if (!isset($originDefaultUserAddressObject->biz_id)) {
                        throw new CommonException('OriginDefaultUserAddressError');
                    }

                    $updateDataArray = [
                        'is_default' => 0,
                    ];

                    $updateResult = $originDefaultUserAddressObject->updateWithShard($updateDataArray);

                    if (!$updateResult) {
                        throw new CommonException('UpdateOriginDefaultUserAddressError');
                    }

                    $originDefaultUserAddressObject = $originDefaultUserAddressObject->fresh();

                    $userAddressIndexName = config('common_es.indices.user.user_addresses');

                    $updateData = [
                        'is_default' => 0,
                        'updated_time' => time(),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $esResult = EsFacade::updateDoc($userAddressIndexName, $originDefaultUserAddressObject->biz_id, $updateData);

                    if (isset($esResult['code']) || $esResult['code'] != 0) {
                        plog(['error' => 'es取消原来用户默认地址失败','$originDefaultUserAddressObject' => $originDefaultUserAddressObject,'$esResult' => $esResult], 'AdminUserAddressFacadeService', 'addUserAddressError');
                        
                        throw new CommonException('EsUpdateOriginDefaultUserAddressError');
                    }
                
            }
        }

        $insertDataArray = [
            'user_uid' => $user_uid,
            'address_type' => $requestDTO->address_type,
            'is_default' => $is_default,
            'address_info' => $requestDTO->address_info,
            'user_name' => $user_name,
            'phone' => $phone,
            'province_id' => $requestDTO->regionArray[0],
            'region_id' => $requestDTO->regionArray[1],
            'city_id' => $requestDTO->regionArray[2],
        ];

        $userAddressObject = ShardHelperFacade::createWithShard(UserAddress::class, $user_uid, $insertDataArray);

        if (!isset($userAddressObject->biz_id)) {
            throw new CommonException('AddUserAddressError');
        }

        $provinceRegionName = Region::find($userAddressObject->province_id)->region_name;
        $regionRegionName = Region::find($userAddressObject->region_id)->region_name;
        $cityRegionName = Region::find($userAddressObject->city_id)->region_name;

        $userAddressIndexName = config('common_es.indices.user.user_addresses');

        $configKey = get_shard_config_key();

        $insertData = [
            '_docId' => $userAddressObject->biz_id,
            'user_address_uid' => $userAddressObject->user_address_uid,
            'shard_key' => $userAddressObject->shard_key,
            'shard_db' => ShardFacade::getDbName($userAddressObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($userAddressObject->user_uid, 'user_addresses', $configKey),
            'created_at' => $userAddressObject->created_at,
            'created_time' => $userAddressObject->created_time,
            'updated_at' => $userAddressObject->updated_at,
            'updated_time' => $userAddressObject->updated_time,
            'deleted_at' => $userAddressObject->deleted_at,
            'user_uid' => $userAddressObject->user_uid,
            'address_type' => $userAddressObject->address_type,
            'is_default' => $userAddressObject->is_default,
            'is_top' => $userAddressObject->is_top,
            'address_info' => $userAddressObject->address_info,
            'user_name' => $userAddressObject->user_name,
            'phone' => $userAddressObject->phone,
            'province_id' => $userAddressObject->province_id,
            'region_id' => $userAddressObject->region_id,
            'city_id' => $userAddressObject->city_id,
            'sort' => $userAddressObject->sort,
            'province_name' => $provinceRegionName,
            'region_name' => $regionRegionName,
            'city_name' => $cityRegionName
        ];

        $esResult = EsFacade::createDoc($userAddressIndexName, $insertData, $userAddressObject->biz_id);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加用户地址失败','$userAddressObject' => $userAddressObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'AdminUserAddressFacadeService', 'addUserAddressError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'AddUserAddress');

        $result = code(['code' => 0,'msg' => '添加用户地址成功!']);

        return $result;
    }

    /**
     * 获取用户地址
     *
     * @param [type] $validated
     * @param [type] $administrator
     * @return void
     */
    public function getUserAddress(GetUserAddressDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetUserAddressError'));

        $validated = $requestDTO->toArray();

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.user.user_addresses');

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

        //排序
        if (isset($requestDTO->sortType)) {
            $sortType = $requestDTO->sortType;

            $esQuery->orderBy(self::$sortMapArray[$sortType][0], self::$sortMapArray[$sortType][1]);
        }


        $userAddressPaginator =  $esQuery->page($currentPage, $perPage)->paginate();

        $result = new EsUserAddressCollection($userAddressPaginator, ['code' => 0,'msg' => '获取用户地址成功!']);

        return $result;
    }

    /**
     * 删除用户地址
     *
     * @param [type] $validated
     * @param [type] $administrator
     * @return void
     */
    public function deleteUserAddress(DeleteUserAddressDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteUserAddressError'));

        $validated = $requestDTO->toArray();

        $user_address_uid = $requestDTO->user_address_uid;

        //es的用户地址索引
        $userAddressIndexName = config('common_es.indices.user.user_addresses');

        $esUserAddressObject = EsQueryFacade::index($userAddressIndexName)->where('user_address_uid', $user_address_uid)->get()->first();

        //降级熔断
        if (!isset($esUserAddressObject->user_address_uid) ) {
            throw new CommonException('ServiceBusyError');
        }

        $userAddressObject = UserAddress::queryByShard($esUserAddressObject->user_uid)->where('user_address_uid', $user_address_uid)->first();


        if (!isset($userAddressObject->biz_id)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $deleteUserAddressResult = $userAddressObject->delete();

        if (!$deleteUserAddressResult) {
            throw new CommonException('DeleteUserAddressError');
        }

        $userAddressIndexName = config('common_es.indices.user.user_addresses');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s'),
        ];

        $esResult = EsFacade::updateDoc($userAddressIndexName, $userAddressObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es删除用户地址失败','$userAddressObject' => $userAddressObject,'$esResult' => $esResult], 'AdminUserAddressFacadeService', 'deleteUserAddressError');

            throw new CommonException('EsDeleteUserAddressError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'DeleteUserAddress');

        $result = code(['code' => 0,'msg' => '删除用户地址成功!']);

        return $result;
    }

    /**
    * 设置用户默认地址
    *
    * @param [type] $validated
    * @param [type] $administrator
    * @return void
    */
    public function setDefaultUserAddress(SetDefaultUserAddressDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.SetDefaultUserAddressError'));

        $validated = $requestDTO->toArray();

        $user_address_uid = $requestDTO->user_address_uid;
        $user_uid = $requestDTO->user_uid;

        //es的用户地址索引
        $userAddressIndexName = config('common_es.indices.user.user_addresses');

        $esUserAddressObject = EsQueryFacade::index($userAddressIndexName)->where('user_address_uid', $user_address_uid)->get()->first();

        //降级熔断
        if (!isset($esUserAddressObject->user_address_uid) ) {
            throw new CommonException('ServiceBusyError');
        }

        //查看原来是否有默认地址
        $esOriginUserAddressObject = EsQueryFacade::index($userAddressIndexName)->whereNull('deleted_at')->where('user_uid', $user_uid)->where('is_default', 1)->get()->first();
       
        if (isset($esOriginUserAddressObject->user_uid)) {
            
                //1先把数据库的默认地址设置为非默认地址
                $originDefaultUserAddressObject = UserAddress::queryByShard($esOriginUserAddressObject->user_uid)->where('is_default', 1)->first();

                if (!isset($originDefaultUserAddressObject->biz_id)) {
                    throw new CommonException('OriginDefaultUserAddressError');
                }

                $updateDataArray = [
                    'is_default' => 0,
                ];

                $updateResult = $originDefaultUserAddressObject->updateWithShard($updateDataArray);

                if (!$updateResult) {
                    throw new CommonException('UpdateOriginDefaultUserAddressError');
                }

                $originDefaultUserAddressObject = $originDefaultUserAddressObject->fresh();

                $userAddressIndexName = config('common_es.indices.user.user_addresses');

                $updateData = [
                    'is_default' => 0,
                    'updated_time' => time(),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $esResult = EsFacade::updateDoc($userAddressIndexName, $originDefaultUserAddressObject->biz_id, $updateData);

                if (!isset($esResult['code']) || $esResult['code'] != 0) {
                    plog(['error' => 'es取消原来用户默认地址失败','$originDefaultUserAddressObject' => $originDefaultUserAddressObject,'$esResult' => $esResult], 'AdminUserAddressFacadeService', 'setDefaultUserAddressError');
                    
                    throw new CommonException('EsUpdateOriginDefaultUserAddressError');
                }
            
        }

        //把现在的地址设置为默认地址
        $newDefaultUserAddressObject = UserAddress::queryByShard($user_uid)->where('user_address_uid', $user_address_uid)->where('is_default', 0)->first();

        $newUpdateDataArray = [
            'is_default' => 1,
        ];

        $newUpdateResult = $newDefaultUserAddressObject->updateWithShard($newUpdateDataArray);

        if (!$newUpdateResult) {
            throw new CommonException('UpdateNewDefaultUserAddressError');
        }

        $newDefaultUserAddressObject = $newDefaultUserAddressObject->fresh();

        $userAddressIndexName = config('common_es.indices.user.user_addresses');

        $updateData = [
            'is_default' => 1,
            'updated_time' => time(),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $esResult = EsFacade::updateDoc($userAddressIndexName, $newDefaultUserAddressObject->biz_id, $updateData);

        if (isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es设置用户默认地址失败','$newDefaultUserAddressObject' => $newDefaultUserAddressObject,'$esResult' => $esResult], 'AdminUserAddressFacadeService', 'SAetDefaultUserAddresseError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'SetDefaultUserAddress');

        $result = code(['code' => 0,'msg' => '设置用户默认地址成功!']);

        return $result;
    }
}
