<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-07 10:25:15
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-30 03:27:01
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Pub\V1\Wechat\WechatOfficialFacadeService.php
 */

namespace App\Services\Facade\Pub\V1\Wechat;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Exceptions\Common\CommonException;
use App\Facades\Common\V1\Es\EsQueryFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\Wechat\ToGetCodeUrlDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\Wechat\AuthToLoginByCodeDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\Wechat\ToGetCodeUrlWithLoginDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\Wechat\AuthToLoginByCodeWithLoginDTO;
/**
 * @see App\Models\LaravelFastApi\V1\System\Platform\SystemWechatConfig
 */
use App\Models\LaravelFastApi\V1\System\Platform\SystemWechatConfig;
use YouHuJun\Tool\App\Facades\V1\Wechat\Official\WechatOfficialWebAuthFacade;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\Login\Wechat\LoginController
 * @see \App\Facades\Pub\V1\Wechat\WechatOfficialFacade
 */
class WechatOfficialFacadeService
{
    public function test()
    {
        echo "WechatOfficialFacadeService test";
    }

    //系统平台微信配置对象
    protected $wechatOfficialObject;

    // 10 静默授权 20主动授权
    protected $scope_array  = [10 => 'snsapi_base',20 => 'snsapi_userinfo'];


    /**
    * 初始化
    *
    * @param [type] $scopeIndex
    * @return void
    */
    private function init()
    {
        $wechatOfficialObject = null;

        $cacheWechatOfficialObject = unserialize(Cache::get('wechat.official.object'));

        $wechatOfficialObject = $cacheWechatOfficialObject;

        if (!$wechatOfficialObject) {
            $indexName = config('wechat.indices.system.system_wechat_configs');

            $esWechatOfficialObject =  EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('type', 30)->get()->first();

            if ($esWechatOfficialObject) {
                $wechatOfficialObject = $esWechatOfficialObject;

                Cache::put('wechat.official.object', serialize($esWechatOfficialObject));
            }
        }

        if ((!isset($wechatOfficialObject->appid)) || !isset($wechatOfficialObject->appsecret)) {
            throw new CommonException('WechatOfficialConfigError');
        }

        $this->wechatOfficialObject = $wechatOfficialObject;
    }

    /**
     * 开发环境初始化
     *
     * @param  [int] $scope_type 默认是用户授权
     */
    private function devWechatInit()
    {
        $wechatOfficialObject = null;

        $cacheWechatOfficialObject = unserialize(Cache::get('wechat.official.object.dev'));

        $wechatOfficialObject = $cacheWechatOfficialObject;

        if (!$wechatOfficialObject) {
            $indexName = config('wechat.indices.system.system_wechat_configs');

            $esWechatOfficialObject =  EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('type', 40)->get()->first();

            if ($esWechatOfficialObject) {
                $wechatOfficialObject = $esWechatOfficialObject;

                Cache::put('wechat.official.object.dev', serialize($esWechatOfficialObject));
            }
        }

        if (!isset($wechatOfficialObject->appid) || !isset($wechatOfficialObject->appsecret)) {
            throw new CommonException('WechatOfficialConfigError');
        }

        $this->wechatOfficialObject = $wechatOfficialObject;
    }


    /**
     * 获取微信授权码的 URL
     *
     * @param array $validated 包含以下键值对的数组：
     *      - scope_type: 授权范围类型
     *      - auth_redirect_url: 授权后重定向的 URL
     *      - state: 用于保持请求和回调的状态
     *
     * @return array 返回包含授权 URL 的结果数组，格式为：
     *      - code: 状态码
     *      - msg: 消息说明
     *      - data: 包含 'url' 的数组
     */
    public function toGetCodeUrl(ToGetCodeUrlDTO $requestDTO)
    {
        ['scope_type' => $scope_type,'auth_redirect_url' => $auth_redirect_url,'state' => $state] = $requestDTO->toArray();

        $result =  code(config('common_code.WechatOfficialGetCodeError'));

        $wechat_official_develop_mode = config('common.wechat_official_develop_mode');

        if (!$wechat_official_develop_mode) {
            //生产环境初始化
            $this->init();
        } else {
            //开发环境初始化
            $this->devWechatInit();
        }

        $wechatOfficialObject = $this->wechatOfficialObject;

        $appid =   trim($wechatOfficialObject->appid);
        $appsecret = trim($wechatOfficialObject->appsecret);

        $config = [
            'appid' => $appid,
            'appsecret' => $appsecret
        ];

        $url = WechatOfficialWebAuthFacade::getAuthUrl(
            $config,
            $scope_type,
            $auth_redirect_url,
            $state
        );

        // Log::debug(['$url'=>$url]);

        $data['url'] = $url;

        $result =  code(['code' => 0,'msg' => '微信公众号发起授权成功!'], ['data' => $data]);

        return $result;
    }



    /**
     * 通过授权码进行登录认证
     *
     * 此方法使用微信的 OAuth2.0 授权码获取用户的访问令牌和用户信息。
     *
     * @param array $validated 包含授权码的数组，必须包含 'code' 键。
     *
     * @return \Illuminate\Support\Collection 返回包含认证结果和微信官方配置的集合。
     *
     * @throws CommonException 如果获取访问令牌时发生错误，将抛出异常。
     */
    public function authToLoginByCode(AuthToLoginByCodeDTO $requestDTO)
    {
        $result =  code(config('common_code.WechatOfficialAuthError'));

        $wechat_official_develop_mode = config('common.wechat_official_develop_mode');

        if (!$wechat_official_develop_mode) {
            //生产环境初始化
            $this->init();
        } else {
            //开发环境初始化
            $this->devWechatInit();
        }

        $wechatOfficialObject = $this->wechatOfficialObject;

        $appid =   trim($wechatOfficialObject->appid);
        $appsecret = trim($wechatOfficialObject->appsecret);

        $config = [
            'appid' => $appid,
            'appsecret' => $appsercet
        ];

        // 一键式授权
        $result = WechatOfficialWebAuthFacade::authorize(
            $config,
            $requestDTO->code,
            true
        );

        return collect(['authResultArray' => $result,'wechatOfficialObject' => $wechatOfficialObject]);
    }
}
