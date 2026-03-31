<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-27 10:40:52
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-20 01:26:48
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Phone\Login\PhoneLoginFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\Login;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Exceptions\Phone\CommonException;
use App\Events\Phone\CommonEvent;
//用户登录
use App\Events\LaravelFastApi\V1\Phone\User\UserLoginEvent;
use App\Events\LaravelFastApi\V1\Phone\User\UserLogoutEvent;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Facades\Common\V1\User\User\CommonUserFacade;
use App\Facades\Common\V1\Login\CommonLoginFacade;
use App\Facades\Pub\V1\Sms\SmsFacade;

/**
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
    public function loginByPhonePassword($validated = [])
    {
        $result = code(config('phone_code.LoginByUserError'));

        if (!isset($validated['phone']) || !isset($validated['password'])) {
            throw new CommonException('LoginByUserError');
        }

        //设置默认守卫是phone
        Auth::setDefaultDriver('phone');

        $remember = true;//生成 remmber_token

        //验证手机号
        $dataPhone['phone'] =  $validated['phone'];
        $dataPhone['password'] =  $validated['password'];
        $dataPhone['switch'] = 1;
        $verifyPhoneResult = Auth::attempt($dataPhone, $remember);

        //有一个验证通过说明是存在的
        if (!$verifyPhoneResult) {
            throw new CommonException('LoginByUserError');
        }

        $userObject = Auth::user();

        $result = $this->commonLoginByPhone($dataPhone['phone'], $userObject);

        return $result;
    }

    /**
     * 发送验证码
     *
     * @param array $validated
     * @return void
     */
    public function sendVerifyCode($validated = [])
    {
        $result = code(config('phone_code.SendPhoneCodeError'));

        $sendResult = 1;

        //如果是开发模式就不校验短信验证码
        if (!config('youhujun.develop_mode')) {
            //先获取验证码,如果没有再发送
            $code =  Redis::get("sms:{$validated['phone']}");

            if (!$code) {
                $sendResult = SmsFacade::sendVerifyCode($validated['phone']);
            }
        }

        if ($sendResult) {
            $result =  code(['code' => 0,'msg' => '短信验证码发送成功!']);
        }

        return $result;
    }

    /**
     * 通过手机号验证码登录
     *
     * @param array $validated
     * @return void
     */
    public function loginByPhoneCode($validated = [])
    {
        $result = code(config('phone_code.LoginByPhoneError'));

        //如果是开发模式就不校验短信验证码
        if (!config('youhujun.develop_mode')) {
            //先获取验证码并进行比对
            $code = SmsFacade::getVerifyCode($validated['phone']);

            //p($code);die;

            if ($code != $validated['code']) {
                throw new CommonException('PhoneCodeError');
            }
        }

        $phone = $validated['phone'];

        //验证手机号 是否有手机用户
        $userObject = User::where([['phone','=',$phone],['switch','=',1]])->first();

        if (!$userObject) {
            throw new CommonException('PhoneUserError');
        }

        //登录
        $result = $this->commonLoginByPhone($phone, $userObject);

        return $result;
    }

    /**
     * 重置手机密码
     */
    public function restPasswordByPhone($validated = [])
    {
        $result = code(\config('phone_code.RestPasswordByPhoneError'));

        //p(config('youhujun.develop_mode'));die;

        //如果是开发模式就不校验短信验证码
        if (!config('youhujun.develop_mode')) {
            //先获取验证码并进行比对
            $code = SmsFacade::getVerifyCode($validated['phone']);

            if ($code != $validated['code']) {
                throw new CommonException('PhoneCodeError');
            }
        }

        $userObject = User::where('phone', $validated['phone'])->first();

        if (!$userObject) {
            throw new CommonException('RestPasswordPoneIsNullError');
        }

        $password = $validated['password'];

        $userObject->password = Hash::make($password);

        $userResult = $userObject->save();

        if (!$userResult) {
            throw new CommonException('RestPasswordByPhoneError');
        }

        CommonEvent::dispatch($userObject, $validated, 'RestPasswordByPhone');

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
    protected function commonLoginByPhone($phone, $userObject = null)
    {
        Auth::setDefaultDriver('phone');

        $remember = true;//生成 remmber_token

        if (optional($userObject)->phone) {
            //如果有手机号 直接登录
            Auth::login($userObject, $remember);
        } else {
            //注册登录
            //验证手机号
            $dataPhone['phone'] =  $phone;
            $dataPhone['password'] = 'abc123';
            $dataPhone['switch'] = 1;

            $verifyPhoneResult = Auth::attempt($dataPhone, $remember);

            if (!$verifyPhoneResult) {
                throw new CommonException('LoginPhoneError');
            }

            $userObject = Auth::user();
        }

        //验证现在这个用户是否是登录状态
        CommonLoginFacade::checkResetLogin($userObject);

        $data['data'] = [];

        if (!empty($userObject) && isset($userObject->remember_token)) {
            $data['data']['token'] = $userObject->remember_token;
            $data['data']['userid'] = $userObject->id;
            $data['data']['openid'] = CommonUserFacade::getUserOpenid($userObject);
        }

        // 20是 用户登录 source
        UserLoginEvent::dispatch($userObject);

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
    public function loginByUserId($validated = [])
    {
        $result = code(config('phone_code.LoginByUserIdError'));

        if (!isset($validated['user_id'])) {
            throw new CommonException('LoginByUserIdError');
        }

        $userObject = User::find($validated['user_id']);

        if (!$userObject) {
            throw new CommonException('LoginByUserIdError');
        }

        Auth::setDefaultDriver('phone');

        $remember = true;

        Auth::login($userObject, $remember);

        //验证现在这个用户是否是登录状态
        CommonLoginFacade::checkResetLogin($userObject);

        UserLoginEvent::dispatch($userObject);

        $data['data']['token'] = $userObject->remember_token;
        $data['data']['userid'] = $userObject->id;
        $data['data']['openid'] = CommonUserFacade::getUserOpenid($userObject);

        $result = code(['code' => 0,'msg' => '用户登录成功!'], $data);

        return $result;
    }

    /**
     * 登录用户获取信息
     *
     * @return void
     */
    public function getUserInfo($validated, $userObject)
    {
        $result = code(config('phone_code.GetUserInfoError'));

        $data = [
            'introduction' => '',
            'avatar' => '',
            'name' => '',
            'roles' => [],
            'phone' => '',
            'created_at' => '',
            'sex' => 0,
            'real_auth_status' => 10,
            'openid' => null
        ];

        $userInfoData = null;

        //先检测redis 缓存有没有数据 如果有redis缓存数据
        $redisData = Redis::hget("phone_user_info:user_info", $userObject->id);


        if (isset($redisData) && !empty($redisData)) {
            $userInfoData = \json_decode($redisData);
            $data = $userInfoData;
        }

        //走到这里说明没redis缓存中没有
        if (!isset($userInfoData) || empty($userInfoData)) {
            $userInfo = UserInfo::where('user_id', $userObject->id)->first();

            if (!$userInfo) {
                throw new CommonException('GetUserInfoError');
            }

            if (isset($userInfo->nick_name)) {
                $data['name'] = $userInfo->nick_name;
            }

            if (isset($userInfo->introduction)) {
                $data['introduction'] = $userInfo->introduction;
            }

            $data['userid'] = $userObject->id;

            $data['avatar'] = CommonUserFacade::getUserAvatar($userObject);

            $data['roles'] = getUserRoles($userObject);

            $data['phone'] = $userObject->phone;

            $data['created_at'] = $userObject->created_at;

            $data['real_auth_status'] = $userObject->real_auth_status;

            $openid_type = $validated['openid_type'];

            $data['openid'] = CommonUserFacade::getUserOpenid($userObject, $openid_type);



            if (isset($userInfo->sex)) {
                $data['sex'] = $userInfo->sex;
            }

            Redis::hset("phone_user_info:user_info", $userObject->id, json_encode($data));
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
    public function bindPhone($validated, $userObject)
    {
        $result = code(config('phone_code.UserBindPhoneError'));

        //如果是开发模式就不校验短信验证码
        if (!config('youhujun.develop_mode')) {
            //先获取验证码并进行比对
            $code = SmsFacade::getVerifyCode($validated['phone']);

            if ($code != $validated['code']) {
                throw new CommonException('PhoneCodeError');
            }
        }

        $phone = $validated['phone'];

        $password =  $validated['password'];

        //绑定手机和密码

        $userObject->phone = $phone;

        $userObject->password = Hash::make($password);

        $userResult = $userObject->save();

        if (!$userResult) {
            throw new CommonException('UserBindPhoneError');
        }

        CommonEvent::dispatch($userObject, $validated, 'UserBindPhone');

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
    public function logout($userObject)
    {
        $result = code(config('phone_code.LogoutError'));

        $token = $userObject->remember_token;

        $userObject->remember_token = null;

        //UserUpdateToken
        $logoutResult = $userObject->save();

        if (!$logoutResult) {
            throw new CommonException('LogoutError');
        }

        UserLogoutEvent::dispatch($userObject, $token);

        $result = code(['code' => 0 ,'msg' => '用户退出成功!']);

        return $result;
    }
}
