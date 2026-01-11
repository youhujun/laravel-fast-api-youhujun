<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-28 18:22:35
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-10 11:51:26
 * @FilePath: \app\Service\Facade\Pub\V1\Sms\TencentSmsFacadeService.php
 */

namespace App\Service\Facade\Pub\V1\Sms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\Common\CommonException;
use YouHuJun\Tool\App\Facade\V1\SMS\Tencent\TencentSMSFacade;

/**
 * @see \App\Facade\Pub\V1\Sms\TencentSmsFacade
 */
class TencentSmsFacadeService
{
    public function test()
    {
        echo "TencentSmsFacadeService test";
    }

    //腾讯云应用 id和key
    protected $secretId;
    protected $secretKey;

    //短信验证码
    //地域
    protected $smsApConfig;
    //sdkAppId
    protected $smsSdkAppId;
    //短信签名
    protected $signName;
    //短信模版id
    protected $templateId;
    //手机号前缀
    protected $phonePre;
    //curl请求 GET|POST
    protected $curlMethods;
    //sing签名加密方式 'TC3-HMAC-SHA256'
    protected $singMethods;



    /**
     * 初始化
     *
     * @return void
     */
    public function init()
    {
        $this->secretId = trim(Cache::get('tencent.secretId'));

        if (!$this->secretId) {
            throw new CommonException('TencentSecretIdError');
        }

        $this->secretKey = trim(Cache::get('tencent.secretKey'));

        if (!$this->secretKey) {
            throw new CommonException('TencentSecretKeyError');
        }
    }

    /**
     * 发送短息验证码
     *
     * @param array $smsContent  模板内容同包含验证码
     * @param array $phoneNumber 手机号
     * @param string $smsApConfig 地域
     * @param string $smsSdkAppId appID
     * @param string $signName  签名模板
     * @param string $templateId 短信模板id
     * @param string $phonePre  手机号前缀
     * @return void
     */
    public function sendSms($smsParam)
    {
        //定义发送结果
        $sendResult = 0;

        $this->init();

        //p($smsParam);die;

        ['smsContent' => $smsContent,'phoneNumber' => $phoneNumber] = $smsParam;

        if (!isset($smsParam['smsContent']) || count($smsParam['smsContent']) == 0) {
            throw new CommonException('TencentSmsContentError');
        }

        if (!isset($smsParam['phoneNumber']) || count($smsParam['phoneNumber']) == 0) {
            throw new CommonException('TencentSmsPhoneNumberError');
        }

        if (isset($smsParam['smsApConfig'])) {
            $smsApConfig = $smsParam['smsApConfig'];
        }

        if (isset($smsParam['curlMethods'])) {
            $curlMethods = $smsParam['curlMethods'];
        }

        if (isset($smsParam['signMethods'])) {
            $signMethods = $smsParam['signMethods'];
        }

        if (isset($smsParam['smsSdkAppId'])) {
            $smsSdkAppId = $smsParam['smsSdkAppId'];
        }

        if (isset($smsParam['signName'])) {
            $signName = $smsParam['signName'];
        }

        if (isset($smsParam['phonePre'])) {
            $phonePre = $smsParam['phonePre'];
        }

        if (isset($smsParam['templateId'])) {
            $templateId = $smsParam['templateId'];
        }

        if (!isset($smsApConfig) || empty($smsApConfig)) {
            $smsApConfig = trim(Cache::get("tencent.sms.ap_config"));

            if (!$smsApConfig) {
                throw new CommonException('TencentSmsApConfigError');
            }
        }

        if (!isset($smsSdkAppId) || empty($smsSdkAppId)) {
            $smsSdkAppId =  trim(Cache::get("tencent.sms.sdkAppId"));

            if (!$smsSdkAppId) {
                throw new CommonException('TencentSmsSdkAppIdError');
            }
        }

        if (!isset($signName) || empty($signName)) {
            $signName =  trim(Cache::get("tencent.sms.singName"));

            if (!$signName) {
                throw new CommonException('TencentSmsSignNameError');
            }
        }

        if (!isset($templateId) || empty($templateId)) {
            $templateId = trim(Cache::get("tencent.sms.templateId.userLogin"));

            if (!$templateId) {
                throw new CommonException('TencentSmsTemplateIdError');
            }
        }


        if (!isset($phonePre) || empty($phonePre)) {
            $phonePre = trim(Cache::get("tencent.sms.PhonePre"));

            if (!$phonePre) {
                throw new CommonException('TencentSmsPhonePreError');
            }
        }

        if (!isset($curlMethods) || empty($curlMethods)) {
            $curlMethods = 'POST';
        }

        if (!isset($singMethods) || empty($singMethods)) {
            $singMethods = 'TC3-HMAC-SHA256';
        }

        $this->smsApConfig = $smsApConfig;
        $this->smsSdkAppId = $smsSdkAppId;
        $this->signName = $signName;
        $this->templateId = $templateId;
        $this->phonePre =  $phonePre;
        $this->curlMethods = $curlMethods;
        $this->singMethods = $singMethods;

        //p($this);die;

        //p($phoneNumber);die;

        foreach ($phoneNumber as $key => &$value) {
            $value =  $this->phonePre.$value;
        }

        //p($phoneNumber);die;


        // 配置腾讯云参数
        $config = [
            'secretId' => $this->secretId,   // 替换为你的腾讯云SecretId
            'secretKey' => $secretKey, // 替换为你的腾讯云SecretKey
            'smsApConfig' => $this->smsApConfig,
            'smsSdkAppId' =>  $this->smsSdkAppId,
            'signName' => $this->signName,
            'templateId' => $this->templateId,
            'phonePre' =>  $this->phonePre,
        ];

        // 短信发送参数
        $smsParam = [
            'smsContent' => $smsContent,              // 模板中的验证码
            'phoneNumber' => $phoneNumber,     // 发送的手机号
        ];


        $result = TencentSMSFacade::sendSms($config, $smsParam);

        return $result;
    }
}
