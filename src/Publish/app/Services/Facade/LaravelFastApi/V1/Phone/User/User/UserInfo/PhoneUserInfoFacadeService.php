<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-05 10:35:13
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 03:54:48
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Phone\User\User\UserInfo\PhoneUserInfoFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\User\User\UserInfo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Services\Facade\Traits\QueryService;
use App\Exceptions\Phone\CommonException;
use App\Events\Phone\CommonEvent;
use App\DTOs\LaravelFastApi\V1\Phone\User\User\UserInfo\UpdateUserNickNameDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\User\UserInfo\UpdateUserPhoneDTO;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Events\Es\V1\User\User\EsUpdateUserNickNameEvent;
use App\Events\Es\V1\User\User\EsUpdateUserPhoneEvent;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\User\User\My\UserController
 * @see \App\Facades\LaravelFastApi\V1\Phone\User\User\UserInfo\PhoneUserInfoFacade
 */
class PhoneUserInfoFacadeService
{
    public function test()
    {
        echo "PhoneUserInfoFacadeService test";
    }


    /**
     * 更新用户昵称
     *
     */
    public function updateUserNickName(UpdateUserNickNameDTO $requestDTO, User $userObject)
    {
        $result = code(config('phone_code.UpdateUserNickNameError'));

        $userInfoObject = UserInfo::queryByShard($userObject->user_uid)->where('user_uid', $userObject->user_uid)->first();

        if (!isset($userInfoObject->biz_id)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $updateDataArray = [
            'nick_name' => $requestDTO->nick_name
        ];

        $updateResult = $userInfoObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('UpdateUserNickNameError');
        }

        $userInfoObject = $userInfoObject->fresh();

        EsUpdateUserNickNameEvent::dispatch($userObject, $userInfoObject);

        CommonEvent::dispatch($userObject, $requestDTO, 'UpdateUserNickName');

        $result = code(['code' => 0,'msg' => '更新用户昵称成功']);

        return $result;
    }

    /**
     * 更新用户手机号
     *
     */
    public function updateUserPhone(UpdateUserPhoneDTO $requestDTO, User $userObject)
    {
        $result = code(config('phone_code.UpdateUserPhoneError'));

        $newUserObject = User::queryByShard($userObject->user_uid)->where('user_uid', $userObject->user_uid)->first();

        if (!isset($newUserObject->biz_id)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $updateDataArray = [
            'phone' => $requestDTO->phone
        ];

        $updateResult = $newUserObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('UpdateUserPhoneError');
        }

        $newUserObject = $newUserObject->fresh();

        EsUpdateUserPhoneEvent::dispatch($newUserObject);

        CommonEvent::dispatch($userObject, $requestDTO, 'UpdateUserPhone');

        $result = code(['code' => 0,'msg' => '更新用户手机号成功']);

        return $result;
    }
}
