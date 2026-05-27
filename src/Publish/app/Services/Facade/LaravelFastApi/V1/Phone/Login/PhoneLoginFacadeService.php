<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-25 01:57:33
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Phone\Login\PhoneLoginFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\Login;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Exceptions\Phone\CommonException;
use App\Events\Phone\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\LoginByPhonePasswordDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\SendVerifyCodeDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\LoginByPhoneCodeDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\SendPasswordCodeDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\RestPasswordByPhoneDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\LoginByUserIdDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\SendBindCodeDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\BindPhoneDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\GetUserInfoDTO;
//Event
use App\Events\Common\V1\User\User\CommonUserCertificateEvent;
use App\Events\LaravelFastApi\V1\Phone\User\UserLoginEvent;
use App\Events\LaravelFastApi\V1\Phone\User\UserLogoutEvent;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Http\Resources\LaravelFastApi\V1\Es\Phone\User\User\UserLoginResource;
use App\Facades\Common\V1\User\User\CommonUserFacade;
use App\Facades\Common\V1\Login\CommonLoginFacade;
use App\Facades\Pub\V1\Sms\SmsFacade;

/**
 *  @see \App\Http\Controllers\LaravelFastApi\V1\Phone\Login\LoginController
 * @see \App\Facades\LaravelFastApi\V1\Phone\Login\PhoneLoginFacade
 */
class PhoneLoginFacadeService
{
    public function test()
    {
        echo "PhoneLoginFacadeService test";
    }


    /**
     * 用户通过手机号密码登录
     * @param  array $validated
     */
    public function loginByPhonePassword(LoginByPhonePasswordDTO $requestDTO)
    {
        $result = code(config('phone_code.LoginByUserError'));

        //设置默认守卫是phone
        Auth::setDefaultDriver('phone');

        $remember = true;//生成 remmber_token

        //验证手机号
        $dataPhoneArray['phone'] = $requestDTO->phone;
        $dataPhoneArray['password'] =  $requestDTO->password;
        $dataPhoneArray['account_status'] = 1;
        $verifyPhoneResult = Auth::attempt($dataPhoneArray, $remember);

        //有一个验证通过说明是存在的
        if (!$verifyPhoneResult) {
            throw new CommonException('LoginByUserError');
        }

        $userObject = Auth::user();

        $result = $this->commonLoginByPhone($requestDTO->phone, $userObject);

        return $result;
    }

    /**
     * 发送验证码
     *
     * @param array $validated
     * @return void
     */
    public function sendVerifyCode(SendVerifyCodeDTO $requestDTO)
    {
        $result = code(config('phone_code.SendPhoneCodeError'));

        $phone = $requestDTO->phone;

        $result = $this->commonSendCode($phone);

        return $result;
    }

    /**
     * 发送绑定验证码成功
     *
     * @param  SendBindCodeDTO $requestDTO
     */
    public function sendBindCode(SendBindCodeDTO $requestDTO)
    {
        $result = code(config('phone_code.SendBindCodeError'));

        $phone = $requestDTO->phone;

        $result = $this->commonSendCode($phone);

        return $result;
    }

    /**
     * 忘记密码
     *
     * @param  SendPasswordCodeDTO $requestDTO
     */
    public function sendPasswordCode(SendPasswordCodeDTO $requestDTO)
    {
        $result = code(config('phone_code.SendPhoneCodeError'));

        $phone = $requestDTO->phone;

        $result = $this->commonSendCode($phone);

        return $result;
    }

    protected function commonSendCode($phone)
    {
        $result = code(config('phone_code.SendBindCodeError'));
        //是否是开发模式
        $is_develop_mode = config('common.is_develop_mode');

        if ($is_develop_mode) {
            $code = init_number_code();

            $redisResult = Redis::setex("sms:{$phone}", 60 * 30, $code);

            if (!$redisResult) {
                throw new CommonException('SmsCodeSaveError');
            }

            $data['code'] = $code;

            $result = code(['code' => 0,'msg' => '发送验证码成功','data' => $data]);
        } else {
            //默认发送成功
            $sendResult = 1;
            //先查看是否已经发送过验证码
            $code = Redis::get("sms:{$phone}");

            if (!$code) {
                $sendResult = SmsFacade::sendVerifyCode($phone);
            }

            if ($sendResult) {
                $result =  code(['code' => 0,'msg' => '短信验证码发送成功!']);
            }
        }

        return $result;
    }



    /**
     * 通过手机号验证码登录
     *
     * @param array $validated
     * @return void
     */
    public function loginByPhoneCode(LoginByPhoneCodeDTO $requestDTO)
    {
        $result = code(config('phone_code.LoginByPhoneError'));

        $phone = $requestDTO->phone;

        $code = SmsFacade::getVerifyCode($phone);

        if ($code != $requestDTO->code) {
            throw new CommonException('PhoneCodeError');
        }

        $userObject = null;

        //登录
        $result = $this->commonLoginByPhone($phone, $userObject);

        return $result;
    }

    /**
     * 重置手机密码
     */
    public function restPasswordByPhone(RestPasswordByPhoneDTO $requestDTO)
    {
        $result = code(\config('phone_code.RestPasswordByPhoneError'));

        $phone = $requestDTO->phone;
        $password = $requestDTO->password;
        //先获取验证码并进行比对
        $code = SmsFacade::getVerifyCode($phone);

        if ($code != $requestDTO->code) {
            throw new CommonException('PhoneCodeError');
        }

        $indexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('phone', $phone)->where('account_status', 1)->get()->first();

        if (!isset($esUserObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $newUserObject = User::queryByShard($esUserObject->user_uid)->where('user_uid', $esUserObject->user_uid)->where('account_status', 1)->first();

        if (!isset($newUserObject->biz_id)) {
            throw new CommonException('ThatDataNotExistError');
        }

        $updateDataArray = [
            'password' => Hash::make($password)
        ];

        $updateResult = $newUserObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('RestPasswordByPhoneError');
        }

        $newUserObject = $newUserObject->fresh();

        CommonEvent::dispatch($newUserObject, $requestDTO, 'RestPasswordByPhone');

        $indexName = config('common_es.indices.user.users');

        $udpateDataArray = [
            'pasword' => $newUserObject->pasword,
            'updated_at' => $newUserObject->updated_at,
            'updated_time' => $newUserObject->updated_time,
        ];

        $esResult = EsFacade::updateDoc($indexName, $newUserObject->biz_id, $udpateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es更新用户密码','$newUserObject' => $newUserObject,'$esResult' => $esResult], 'PhoneLoginFacadeService', 'restPasswordByPhoneError');
             throw new CommonException('EsRestPasswordByPhoneError');
        }

        $result = ['code' => 0 ,'msg' => '重置密码成功!'];

        return $result;
    }

    /**
     * 内部通过手机号登录
     *
     * @param [type] $phone
     * @param [type] $ip
     * @param string $password
     * @return void
     */
    protected function commonLoginByPhone(string $phone, User $userObject = null)
    {
        Auth::setDefaultDriver('phone');

        $remember = true;//生成 remmber_token

        if (optional($userObject)->phone) {
            //如果有手机号 直接登录
            Auth::login($userObject, $remember);
        } else {
            //注册登录
            //验证手机号
            $dataPhoneArray['phone'] =  $phone;
            $dataPhoneArray['password'] = config('common.default_password');
            $dataPhoneArray['account_status'] = 1;

            $verifyPhoneResult = Auth::attempt($dataPhoneArray, $remember);

            if (!$verifyPhoneResult) {
                throw new CommonException('LoginPhoneError');
            }

            $userObject = Auth::user();
        }

        //验证现在这个用户是否是登录状态
        CommonLoginFacade::checkResetLogin($userObject);


        $userObject = $userObject->fresh();

        // 20是 用户登录 source
        UserLoginEvent::dispatch($userObject);

        $indexName = config('common_es.indices.user.users');

        $udpateDataArray = [
            'remember_token' => $userObject->remember_token,
            'updated_at' => $userObject->updated_at,
            'updated_time' => $userObject->updated_time,
        ];

        $esResult = EsFacade::updateDoc($indexName, $userObject->biz_id, $udpateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es更新用户登录remember_token','$userObject' => $userObject,'$esResult' => $esResult], 'PhoneLoginFacadeService', 'commonLoginByPhoneError');
             throw new CommonException('EsLoginPhoneError');
        }

        $data['data'] = new UserLoginResource($userObject);

        $result = code(['code' => 0,'msg' => '用户登录成功!'], $data);

        return $result;
    }

    /**
     * 通过三大运营商一键登录
     *
     * @param array $validated
     * @return void
     */
    /* public function univerifyLogin($validated = [])
    {
         $result = code(config('phone_code.LoginUniVerifyError'));

         // Log::debug(['validated'=>$validated]);

         $ip = '127.0.0.1';

         if(isset($validated['ip']))
         {
             $ip = $validated['ip'];
         }

         //先处理手机端传递过来的参数 获取手机号
         $sercret = trim(Cache::get('uni_app.univerifyLogin.sercret'));

         if(!$sercret)
         {
             throw new CommonException('LoginUniVerifyNoSercretError');
         }

         $hamc = hash_init('sha256',1, $sercret);

         if(!(isset($validated['openid']) && isset($validated['access_token'])))
         {
             throw new CommonException('LoginUniVerifyParamError');
         }

         $singStr = "access_token={$validated['access_token']}&openid={$validated['openid']}";

         hash_update($hamc, $singStr);

         $sign = \hash_final($hamc);

         //Log::debug(['sign'=>$sign]);

         $uni_app_cloud_url = trim(Cache::get('uni_app.univerifyLogin.url'));

         if(!$uni_app_cloud_url)
         {
             throw new CommonException('LoginUniVerifyNoCloudUrlError');
         }

         $loginUrl = $uni_app_cloud_url."?{$singStr}&sign=${sign}";

         // Log::debug(['url'=>$loginUrl]);

         $loginResult = http_get($loginUrl);

         // Log::debug(['$loginResult'=>$loginResult]);

         //如果不为空才算成功
         if(!empty($loginResult))
         {
             $loginResultArray = \json_decode($loginResult,true);

             if($loginResultArray['code'] != 0 || !$loginResultArray['success'])
             {
                 throw new CommonException('LoginUniVerifyError');
             }

             $phone = $loginResultArray['phoneNumber'];

             //获取到手机号以后,有两种情况 一种是注册 再登录 一种是直接登录

             //先判断手机号是否在数据库中
             $userObject = User::withTrashed()->where('phone',$phone)->first();

             if($userObject)
             {
                 if($userObject->switch == 1 && $userObject->deleted_at == null)
                 {
                     //登录
                     $result = $this->commonLoginByPhone($phone,$ip,$userObject);
                 }
                 else
                 {
                      throw new CommonException('LoginUniVerifyDisabledUserError');
                 }

             }
             else
             {
                 //注册
                 DB::beginTransaction();

                 $userObject = new User;

                 $userObject->phone = $validated['phone'];

                 $userObject->password = Hash::make('abc123');

                 //用户级别最低
                 $userObject->level_id = 1;

                 //用户未实名认证
                 $userObject->real_auth_status = 10;

                 $userObject->switch = 1;

                 $userObject->created_at = time();

                 $userObject->created_time = time();

                 $userObject->account_name = \bin2hex(\random_bytes(4));

                 $userObject->auth_token = Str::random(20);

                 $userResult = $userObject->save();

                 // 邀请码
                 $user_uid = $userObject->id;

                 if(mb_strlen($user_uid) < 4)
                 {
                     $userObject->invite_code = str_pad($user_uid,4,'0',STR_PAD_LEFT);
                 }
                 else
                 {
                     $userObject->invite_code = $user_uid;
                 }

                 $userResult = $userObject->save();

                 if(!$userResult)
                 {
                     DB::rollBack();
                     throw new CommonException('AddUserByUniverifyError');
                 }

                 UserRegisterEvent::dispatch($userObject,$validated,1);

                 SystemDistributeUserEvent::dispatch($userObject,$validated,1);

                 CommonEvent::dispatch($userObject,$validated,'AddUserByUniverify',1);

                 //提交
                 DB::commit();

                 //登录
                 $result = $this->commonLoginByPhone($phone,$ip);
             }

         }

         //Log::debug(['$result'=>$result]);

         return $result;

    } */

    /**
     * 通过用户id登录 默认已经绑定微信的openid
     *
     * @param  array $validated
     */
    public function loginByUserId(LoginByUserIdDTO $requestDTO)
    {
        $result = code(config('phone_code.LoginByUserIdError'));

        $indexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid', $requestDTO->user_uid)->get()->first();

        if (!$esUserObject) {
            throw new CommonException('ServiceBusyError');
        }

        $userObject = User::queryByShard($requestDTO->user_uid)->where('user_uid', $requestDTO->user_uid)->first();

        if (!$userObject) {
            throw new CommonException('ThatDataNotExistError');
        }

        Auth::setDefaultDriver('phone');

        $remember = true;

        Auth::login($userObject, $remember);

        //验证现在这个用户是否是登录状态

        CommonLoginFacade::checkResetLogin($userObject);

        UserLoginEvent::dispatch($userObject);

        /**
        * @see \App\Services\Facade\Common\V1\User\User\CommonUserFacadeService
        */
        $data['data']['openid'] = CommonUserFacade::getUserOpenid($userObject);

        $indexName = config('common_es.indices.user.users');

        $udpateDataArray = [
            'remember_token' => $userObject->remember_token,
            'updated_at' => $userObject->updated_at,
            'updated_time' => $userObject->updated_time,
        ];

        $esResult = EsFacade::updateDoc($indexName, $userObject->biz_id, $udpateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es更新用户登录remember_token','$userObject' => $userObject,'$esResult' => $esResult], 'PhoneLoginFacadeService', 'commonLoginByPhoneError');
             throw new CommonException('EsLoginPhoneError');
        }

        $data['data'] = new UserLoginResource($userObject);

        $result = code(['code' => 0,'msg' => '用户登录成功!'], $data);

        return $result;
    }

    /**
     * 登录用户获取信息
     *
     * @return void
     */
    public function getUserInfo(GetUserInfoDTO $requestDTO, User $userObject)
    {
        $result = code(config('phone_code.GetUserInfoError'));

        $redisJsonString = Redis::hget('phone_user_info:user_info', $userObject->user_uid);

        if ($redisJsonString) {
            $data = json_decode($redisJsonString, true);
        } else {
            $userIndexName = config('common_es.indices.user.users');

            $esUserObject = EsQueryFacade::index($userIndexName)->where('user_uid', $userObject->biz_id)->get()->first();

            if (!$esUserObject) {
                throw new CommonException('ServiceBusyError');
            }

            $userObject = User::queryByShard($userObject->biz_id)->where('user_uid', $userObject->biz_id)->first();

            $data = [
                'user_uid' => $userObject->biz_id,
                'introduction' => '',
                'phone' => '',
                'name' => '',
                'created_at' => '',
                'sex' => 0,
                'roles' => [],
                'real_auth_status' => 10,
                'avatar' => '',
                'openid' => null,
                'unionid' => null
            ];

            $data['introduction'] = $esUserObject->introduction;
            $data['phone'] = $esUserObject->phone;
            $data['name'] = $esUserObject->nick_name ?? $esUserObject->real_name;
            $data['created_at'] = $esUserObject->created_at;
            $data['real_auth_status'] = $esUserObject->real_auth_status;
            $data['sex'] = $esUserObject->sex;

            $data['roles'] = get_user_roles($userObject);

            /**
             * @see \App\Services\Facade\Common\V1\User\User\CommonUserFacadeService
             */
            $data['avatar'] = CommonUserFacade::getUserAvatar($userObject);

            $openid_type = $requestDTO->openid_type;

            $data['openid'] = CommonUserFacade::getUserOpenid($userObject, $openid_type);

            $data['unionid'] = CommonUserFacade::getUserUnionid($userObject);

            Redis::hset('phone_user_info:user_info', $userObject->user_uid, json_encode($data));
        }

        $result = code(['code' => 0,'msg' => '获取用户信息成功'], ['data' => $data]);

        return $result;
    }

    /**
     * 微信登录后绑定手机
     *
     * @param  [type] $validated
     * @param  [type] $userObject
     */
    public function bindPhone(BindPhoneDTO $requestDTO, User $userObject)
    {
        $result = code(config('phone_code.UserBindPhoneError'));

        $phone = $requestDTO->phone;

        //先获取验证码并进行比对
        $code = SmsFacade::getVerifyCode($phone);

        if ($code != $requestDTO->code) {
            throw new CommonException('PhoneCodeError');
        }


        $password =  $requestDTO->password;

        //必须查数据库
        $newUserObject = User::queryByShard($userObject->user_uid)->where('user_uid', $userObject->user_uid)->first();

        if (!$newUserObject) {
            throw new CommonException('ThatDataNotExistsError');
        }

        $updateDataArray = [
            'phone' => $phone,
            'password' => Hash::make($password),
        ];

        $updateResult = $newUserObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('UserBindPhoneError');
        }

        $newUserObject = $newUserObject->fresh();

        $indexName = config('common_es.indices.user.users');

        $udpateDataArray = [
            'phone' => $newUserObject->phone,
            'password' => $newUserObject->password,
            'updated_at' => $newUserObject->updated_at,
            'updated_time' => $newUserObject->updated_time,
        ];

        $esResult = EsFacade::updateDoc($indexName, $newUserObject->biz_id, $udpateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es绑定用户手机号','$newUserObject' => $newUserObject,'$esResult' => $esResult], 'PhoneLoginFacadeService', 'bindPhoneError');
            throw new CommonException('EsUserBindPhoneError');
        }

        $certificateArray = [
            'user_certification_uid' => get_snow_flake_id(),
            'cert_type' => 10,
            'cert_status' => 30,
            'certified_time' => time(),
            'certified_at' => date('Y-m-d H:i:s'),
            'cert_remark' => '手机号认证通过'
        ];
        CommonUserCertificateEvent::dispatch($newUserObject, $certificateArray);

        CommonEvent::dispatch($newUserObject, $requestDTO, 'UserBindPhone');

        $data = [];

        $data['phone'] = $phone;

        $result = ['code' => 0 ,'msg' => '用户绑定手机号成功!','data' => $data];

        return $result;
    }

    /**
     * 用户退出登录
     *
     * @return void
     */
    public function logout(User $userObject)
    {
        $result = code(config('phone_code.LogoutError'));

        //这里必须重新查数据库
        $newUserObject =  User::queryByShard($userObject->user_uid)->where('user_uid', $userObject->user_uid)->first();

        $token = $newUserObject->remember_token;

        if (!$newUserObject) {
            throw new CommonException('ThatDataNotExistError');
        }

        $updateDataArray = [
            'remember_token' => null
        ];

        //UserUpdateToken
        $logoutResult = $newUserObject->updateWithShard($updateDataArray);

        if (!$logoutResult) {
            throw new CommonException('LogoutError');
        }

        UserLogoutEvent::dispatch($newUserObject, $token);

        $indexName = config('common_es.indices.user.users');

        $udpateDataArray = [
            'remember_token' => null,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_time' => time()

        ];

        $result = EsFacade::updateDoc($indexName, $newUserObject->user_uid, $udpateDataArray);

        if (!isset($result['code']) || $result['code'] != 0) {
            plog(['error' => 'es添加用户退出失败','$result' => $result,'$udpateDataArray' => $udpateDataArray], 'PhoneLoginFacadeService', 'handleError');
            throw new CommonException('EsLogoutError');
        }

        $result = code(['code' => 0 ,'msg' => '用户退出成功!']);

        return $result;
    }
}
