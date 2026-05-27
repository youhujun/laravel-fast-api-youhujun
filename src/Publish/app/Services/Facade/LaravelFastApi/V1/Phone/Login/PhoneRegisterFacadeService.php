<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-01 09:58:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 17:43:15
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Phone\Login\PhoneRegisterFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\Login;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use App\Exceptions\Phone\CommonException;
use App\Events\Phone\CommonEvent;
use App\Events\Common\V1\User\User\EsAddUserEvent;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Phone\User\Register\SendPhoneRegisterCodeDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Register\UserRegisterDTO;
use App\DTOs\Common\V1\User\User\BusinessRegisterUserDTO;
use App\DTOs\Contracts\V1\User\User\AddUserHandlerContractDTO;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\System\Level\UserLevel;
use App\Facades\Pub\V1\Sms\SmsFacade;
use App\Facades\Common\V1\User\User\CommonUserFacade;
use App\Contracts\LaravelFastApi\V1\Common\User\AddUserHandlerContract;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\Login\RegisterController
 * @see \App\Facades\LaravelFastApi\V1\Phone\Login\PhoneRegisterFacade
 */
class PhoneRegisterFacadeService
{
    public function test()
    {
        echo "PhoneRegisterFacadeService test";
    }

    /**
     * 发送注册验证码
     */
    public function sendUserRegisterCode(SendPhoneRegisterCodeDTO $requestDTO)
    {
        $result = code(config('phone_code.SendUserRegisterCodeError'));

        $phone = $requestDTO->phone;

        //是否是开发模式
        $is_develop_mode = config('common.is_develop_mode');

        if ($is_develop_mode) {
            $code = init_number_code();

            $redisResult = Redis::setex("sms-register:{$phone}", 60 * 30, $code);

            if (!$redisResult) {
                throw new CommonException('SmsCodeSaveError');
            }

            $data['code'] = $code;

            $result = code(['code' => 0,'msg' => '发送注册验证码成功','data' => $data]);
        } else {
            //默认发送成功
            $sendResult = 1;
            //先查看是否已经发送过验证码
            $registerCode = Redis::get("sms-register:{$phone}");

            $code = init_number_code();

            $redisResult = Redis::setex("sms-register:{$phone}", 60 * 30, $code);

            //如果没有再发送
            if (!$registerCode) {
                /**
                 * @see \App\Services\Facade\Pub\V1\Sms\SmsFacadeService
                 */
                $sendResult = SmsFacade::sendUserRegisterCode($phone);
            }

            if ($sendResult) {
                $result =  code(['code' => 0,'msg' => '发送注册验证码成功']);
            }
        }

        return $result;
    }

    /**
     * 添加用户
     */
    public function userRegister(UserRegisterDTO $requestDTO)
    {
        $result = code(config('phone_code.AddUserError'));


        $phone = $requestDTO->phone;
        //1先比对用户的验证码
        /**
         * @see \App\Services\Facade\Pub\V1\Sms\SmsFacadeService
         */
        $redisCode = SmsFacade::getUserRegisterCode($phone);

        if (!$redisCode) {
            throw new CommonException('UserRegisterCodeTimeError');
        }

        if ($redisCode != $requestDTO->register_code) {
            throw new CommonException('UserRegisterCodeError');
        }

        $userLevelObject = UserLevel::where('level_code','V0')->get()->first();

        DB::beginTransaction();

        $user_uid = get_snow_flake_id();
        $insertDataArray = [
            'user_uid' => $user_uid,
            'phone' => $phone,
            'password' => Hash::make($requestDTO->password),
            'level_id' => $userLevelObject->id,
            'real_auth_status' => 10,
            'account_status' => 1,
            'account_name' => \bin2hex(\random_bytes(4)),
            'auth_token' => Str::uuid()->toString(),
            'source' => $requestDTO->source ?? 30
        ];

        $userObject = ShardHelperFacade::createWithShard(User::class, $user_uid, $insertDataArray);

        if (!isset($userObject->biz_id)) {
            DB::rollBack();
            throw new CommonException('AddUserError');
        }

        $userObject = $userObject->fresh();

        //处理传递参数
        $businessDTO = new BusinessRegisterUserDTO();

        $businessDTO->$userObject = $userObject;
        $businessDTO->invite_id = isset($requestDTO->invite_id)?$requestDTO->invite_id:0;
        $businessDTO->invite_code = isset($requestDTO->invite_code)?$requestDTO->invite_code:'';
        $businessDTO->phone = isset($requestDTO->phone)?$requestDTO->phone:'';
        $businessDTO->password = isset($requestDTO->password)?$requestDTO->password:'';
        $businessDTO->source = isset($requestDTO->source)?$requestDTO->source:0;

        /**
         * @see \App\Services\Facade\Common\V1\User\User\CommonUserFacadeService
         */
        CommonUserFacade::registerUser($businessDTO, $userObject);

         //es添加用户
        EsAddUserEvent::dispatch($userObject,true);

        CommonEvent::dispatch($userObject, $requestDTO, 'AddUser', true);

        //提交
        DB::commit();

        $result = code(['code' => 0,'msg' => '注册用户成功!']);

        return $result;
    }
}
