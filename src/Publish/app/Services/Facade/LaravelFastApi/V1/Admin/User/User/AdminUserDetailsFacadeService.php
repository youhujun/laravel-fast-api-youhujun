<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-09 19:48:15
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-15 01:15:35
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserDetailsFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use YouHuJun\Tool\App\Facades\V1\Calendar\CalendarFacade;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\UpdateUserPhoneDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\UpdateUserRealNameDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\UpdateUserNickNameDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\UpdateUserSexDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\ChangeUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\UpdateUserBirthdayTimeDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\GetUserQrcodeDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\ResetUserPasswordDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\MakeUserQrcodeDTO;

//模型
use App\Models\LaravelFastApi\V1\Admin\Admin;
//用户
use App\Models\LaravelFastApi\V1\User\User;
//用户地址
use App\Models\LaravelFastApi\V1\User\Info\UserAddress;
//用户详情
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
//用户银行卡
use App\Models\LaravelFastApi\V1\User\Info\UserBank;
use App\Models\LaravelFastApi\V1\User\Info\UserIdCard;
//用户申请实名认证表
use App\Models\LaravelFastApi\V1\User\Log\UserRealAuthLog;
//用户父关联表
use App\Models\LaravelFastApi\V1\User\Union\UserSourceUnion;
//用户二维码表
use App\Models\LaravelFastApi\V1\User\Info\UserQrcode;
//生成用户二维码
use App\Events\LaravelFastApi\V1\Admin\User\User\MakeUserQrcodeEvent;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserDetailsController
 * @see \App\Facades\Admin\User\User\AdminUserDetailsFacade
 */
class AdminUserDetailsFacadeService
{
    public function test()
    {
        echo "AdminUserDetailsFacadeService test";
    }

    protected static $sort = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];


    /**
     * 修改用户手机号
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updateUserPhone(UpdateUserPhoneDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateUserPhoneError'));

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;

        $indexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid',$user_uid)->get()->first();

        //降级熔断
        if (!isset($esUserObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $userObject = User::queryByShard($user_uid)->where('user_uid', $user_uid)->first();

        if (!isset($userObject->biz_id)) {
            throw new CommonException('ThatDataNotExist');
        }

        $updateDataArray = [
            'phone' => $requestDTO->phone,
        ];

        $updatePhoneResult = $userObject->updateWithShard($updateDataArray);

        if (!$updatePhoneResult) {
            throw new CommonException('UpdateUserPhoneError');
        }

        $userObject = $userObject->fresh();

        $indexName = config('common_es.indices.user.users');

        $updateDataArray = [
            'phone' => $userObject->phone,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_time' => time()
        ];

        $esResult = EsFacade::updateDoc($indexName, $userObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['esResult' => $esResult,'userObject' => $userObject,'adminObject' => $adminObject], 'AdminUserDetailsFacadeService', 'UpdateUserPhoneError');

            throw new CommonException('EsUpdateUserPhoneError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'UpdateUserPhone');

        $result = code(['code' => 0,'msg' => '更新用户手机号成功']);

        return $result;
    }



    /**
     * 修改用户真实姓名
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updateUserRealName(UpdateUserRealNameDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateUserRealNameError'));

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;

        $indexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid',$user_uid)->get()->first();

        //降级熔断
        if (!isset($esUserObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $userInfoObject = UserInfo::queryByShard($validated['user_uid'])->where('user_uid', $validated['user_uid'])->first();

        if (!isset($userInfoObject->biz_id)) {
            throw new CommonException('ThatDataNotExist');
        }

        $updateDataArray = [
            'real_name' => $validated['real_name'],
        ];

        $updateRealNameResult = $userInfoObject->updateWithShard($updateDataArray);


        if (!$updateRealNameResult) {
            throw new CommonException('UpdateUserRealNameError');
        }

        $userInfoObject = $userInfoObject->fresh();

        $indexName = config('common_es.indices.user.users');

        $updateDataArray = [
            'real_name' => $userInfoObject->real_name,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_time' => time()
        ];

        $esResult = EsFacade::updateDoc($indexName, $userInfoObject->user_uid, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['esResult' => $esResult,'$userInfoObject' => $userInfoObject,'adminObject' => $adminObject], 'AdminUserDetailsFacadeService', 'UpdateUserRealNameError');
             throw new CommonException('EsUpdateUserRealNameError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'UpdateUserRealName');

        $result = code(['code' => 0,'msg' => '更新用户姓名成功']);

        return $result;
    }


    /**
     * 修改用户昵称
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updateUserNickName(UpdateUserNickNameDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateUserNickNameError'));

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;

        $indexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid',$user_uid)->get()->first();

        //降级熔断
        if (!isset($esUserObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $userInfoObject = UserInfo::queryByShard($validated['user_uid'])->where('user_uid', $user_uid)->first();

        if (!isset($userInfoObject->biz_id)) {
            throw new CommonException('ThatDataNotExist');
        }

        $updateDataArray = [
            'nick_name' => $validated['nick_name'],
        ];

        $updateNickNameResult = $userInfoObject->updateWithShard($updateDataArray);

        if (!$updateNickNameResult) {
            throw new CommonException('UpdateUserNickNameError');
        }

        $userInfoObject = $userInfoObject->fresh();

        $indexName = config('common_es.indices.user.users');

        $updateDataArray = [
            'nick_name' => $userInfoObject->nick_name,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_time' => time()
        ];

        $esResult = EsFacade::updateDoc($indexName, $userInfoObject->user_uid, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['esResult' => $esResult,'$userInfoObject' => $userInfoObject,'adminObject' => $adminObject], 'AdminUserDetailsFacadeService', 'UpdateUserNickNameError');

            throw new CommonException('EsUpdateUserNickNameError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'UpdateUserNickName');

        $result = code(['code' => 0,'msg' => '更新用户昵称成功']);

        return $result;
    }


    /**
     * 修改用户性别
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updateUserSex(UpdateUserSexDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateUserSexError'));

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;

        $indexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid',$user_uid)->get()->first();

        //降级熔断
        if (!isset($esUserObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $userInfoObject = UserInfo::queryByShard($validated['user_uid'])->where('user_uid', $validated['user_uid'])->first();

        if (!isset($userInfoObject->biz_id)) {
            throw new CommonException('ThatDataNotExist');
        }

        $updateDataArray = [
            'sex' => $validated['sex'],
        ];

        $updateSexResult = $userInfoObject->updateWithShard($updateDataArray);

        if (!$updateSexResult) {
            throw new CommonException('UpdateUserSexError');
        }

        $userInfoObject = $userInfoObject->fresh();

        $indexName = config('common_es.indices.user.users');

        $updateDataArray = [
            'sex' => $userInfoObject->sex,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_time' => time()
        ];

        $esResult = EsFacade::updateDoc($indexName, $userInfoObject->user_uid, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['esResult' => $esResult,'$userInfoObject' => $userInfoObject,'adminObject' => $adminObject], 'AdminUserDetailsFacadeService', 'UpdateUserSexError');
            throw new CommonException('EsUpdateUserSexError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'UpdateUserSex');

        $result = code(['code' => 0,'msg' => '更新用户性别成功']);

        return $result;
    }

    /**
    * 更改用户级别
    *
    * @param [type] $validated
    * @param [type] $adminObject
    * @return void
    */
    public function changeUserLevel(ChangeUserLevelDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.ChangeUserLevelError'));

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;

        $indexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid',$user_uid)->get()->first();

        //降级熔断
        if (!isset($esUserObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $userObject = User::queryByShard($user_uid)->where('user_uid',$user_uid)->first();

        if (!isset($userObject->biz_id)) {
            throw new CommonException('ThatDataNotExist');
        }

        $updateDataArray = [
            'level_id' => $validated['level_id'],
        ];

        $updateUserResult = $userObject->updateWithShard($updateDataArray);

        if (!$updateUserResult) {
            throw new CommonException('UpdateUserLevelError');
        }

        //清理redis缓存
        $redisKey = config('common_redis.user_level.key');
        $redisField = config('common_redis.user_level.field');

        Redis::hdel($redisKey, $redisField.$user_uid);

        $userObject = $userObject->fresh();

        $indexName = config('common_es.indices.user.users');

        $updateDataArray = [
            'level_id' => $userObject->level_id,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_time' => time()
        ];

        $esResult = EsFacade::updateDoc($indexName, $userObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['esResult' => $esResult,'userObject' => $userObject,'adminObject' => $adminObject], 'AdminUserDetailsFacadeService', 'UpdateUserLevelError');

             throw new CommonException('EsUpdateUserLevelError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'ChangeUserLevel');

        $result = code(['code' => 0,'msg' => '更新用户级别成功']);

        return $result;
    }

    /**
    * 修改用户性别
    *
    * @param [type] $validated
    * @param [type] $adminObject
    * @return void
    */
    public function updateUserBirthdayTime(UpdateUserBirthdayTimeDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateUserBirthdayTimeError'));

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;

        $indexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid',$user_uid)->get()->first();

        //降级熔断
        if (!isset($esUserObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $userInfoObject = UserInfo::queryByShard($validated['user_uid'])->where('user_uid', $validated['user_uid'])->first();

        if (!isset($userInfoObject->biz_id)) {
            throw new CommonException('ThatDataNotExist');
        }

        if (isset($validated['solar_birthday_time'])) {
            $lunar = CalendarFacade::solarToLunarString($validated['solar_birthday_time']);
        }

        $updateDataArray = [
            'solar_birthday_at' => $validated['solar_birthday_time'] ?? null,
            'solar_birthday_time' => strtotime($validated['solar_birthday_time']) ?? 0,
            'chinese_birthday_at' => $lunar ?? null,
            'chinese_birthday_time' => \strtotime($lunar)
        ];

        $updateResult = $userInfoObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('UpdateUserBirthdayTimeError');
        }

        $userInfoObject = $userInfoObject->fresh();

        $indexName = config('common_es.indices.user.users');

        $updateDataArray = [
            'solar_birthday_at' => $userInfoObject->solar_birthday_at,
            'chinese_birthday_at' => $userInfoObject->chinese_birthday_at,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_time' => time()
        ];

        $esResult = EsFacade::updateDoc($indexName, $userInfoObject->user_uid, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['esResult' => $esResult,'$userInfoObject' => $userInfoObject,'adminObject' => $adminObject], 'AdminUserDetailsFacadeService', 'UpdateUserBirthdayTimeError');

            throw new CommonException('EsUpdateUserBirthdayTimeError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'UpdateUserBirthdayTime');

        $result = code(['code' => 0,'msg' => '更新用户出生日期成功']);

        return $result;
    }

    /**
    * 重置用户密码
    *
    * @param [type] $validated
    * @param [type] $adminObject
    * @return void
    */
    public function resetUserPassword(ResetUserPasswordDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.ResetUserPasswordError'));

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;

        $indexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid',$user_uid)->get()->first();

        //降级熔断
        if (!isset($esUserObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $userObject = User::queryByShard($user_uid)->where('user_uid', $user_uid)->first();

        if (!isset($userObject->biz_id)) {
            throw new CommonException('ThatDataNotExist');
        }

        $updateDataArray = [
            'password' => Hash::make($validated['password']),
        ];

        $updateResult = $userObject->updateWithShard($updateDataArray);
        ;

        if (!$updateResult) {
            throw new CommonException('ResetUserPasswordError');
        }

        $userObject = $userObject->fresh();

        $indexName = config('common_es.indices.user.users');

        $updateDataArray = [
            'password' => $userObject->password,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_time' => time()
        ];

        $esResult = EsFacade::updateDoc($indexName, $userObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['esResult' => $esResult,'userObject' => $userObject,'adminObject' => $adminObject], 'AdminUserDetailsFacadeService', 'ResetUserPasswordError');

            throw new CommonException('EsResetUserPasswordError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'ResetUserPassword');

        $result = code(['code' => 0,'msg' => '重置用户密码成功']);

        return $result;
    }


    /**
     * 获取用户二维码
     */
    public function getUserQrcode(GetUserQrcodeDTO $requestDTO, Admin $adminObject)
    {
        $result = ['code' => 10000 ,'msg' => '获取用户二维码失败'];

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;

        $indexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid',$user_uid)->get()->first();

        //降级熔断
        if (!isset($esUserObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $data['user_uid'] = $esUserObject->user_uid;
        $data['qrcode'] = $esUserObject->qrcode;

        $result = code(['code' => 0 ,'msg' => '获取用户二维码成功'], ['data' => $data]);

        return $result;
    }

    /**
     * 生成用户二维码
     */
    public function makeUserQrcode(MakeUserQrcodeDTO $requestDTO, Admin $adminObject)
    {
        $result = ['code' => 10000 ,'msg' => '生成用户二维码失败'];

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;

        $indexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid',$user_uid)->get()->first();

        //降级熔断
        if (!isset($esUserObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $userObject = User::queryByShard($user_uid)->where('user_uid', $user_uid)->first();

        if (!isset($userObject->biz_id)) {
            throw new CommonException('ThatDataNotExist');
        }

        MakeUserQrcodeEvent::dispatch($requestDTO, $userObject,$adminObject);

        CommonEvent::dispatch($adminObject, $validated, 'MakeUserQrcode');

        $result = code(['code' => 0,'msg' => '用户二维码生成成功']);

        return $result;
    }
}
