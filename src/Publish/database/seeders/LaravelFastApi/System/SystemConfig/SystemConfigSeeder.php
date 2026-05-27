<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-25 09:47:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-30 00:51:21
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\System\SystemConfig\SystemConfigSeeder.php
 */

namespace Database\Seeders\LaravelFastApi\System\SystemConfig;

use App\Models\LaravelFastApi\V1\System\SystemConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Secret\AESFacade;

class SystemConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SystemConfig::truncate();

        $this->command->info('开始填充laravel-fast-api系统配置');

        $secretKey = KeyManagerFacade::generateSecureSecretKey(40, ['letters_upper', 'letters_lower', 'numbers']);

        $aesKey = config('common.aes.key');

        $encryptedSecretKey = AESFacade::encrypt($secretKey, $aesKey);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'verifySign.secretKey',
            'item_value' => $encryptedSecretKey,
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '签名秘钥',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        // 微信商户配置
        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'wechat.merchant.merchantId',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '微信商户Id',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'wechat.merchant.api_v3_key',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '用户加密解密',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'wechat.merchant.merchantSerialNumber',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '商户API证书序列号',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 40,
            'item_label' => 'wechat.merchant.merchantPrivateKey',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '商户私钥文件路径',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 40,
            'item_label' => 'wechat.merchant.wechatpayCertificate',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '微信支付平台证书文件路径',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'wechat.merchant.notifyUrl.JsPayNotifyUrl',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => 'JsPay支付回调通知地址',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        // uni_app 一键登录
        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'uni_app.univerifyLogin.sercret',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => 'uni_app一键登录自定义秘钥',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'uni_app.univerifyLogin.url',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => 'uni_app一键登录云函数地址',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        // 默认短信平台设置为腾讯云
        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'sms',
            'item_value' => 'tencent',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '短信平台',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        // 短信配置
        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'tencent.secretId',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '腾讯应用id',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'tencent.secretKey',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '腾讯应用key',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'tencent.sms.ap_config',
            'item_value' => 'ap-beijing',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '北京地域',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'tencent.sms.sdkAppId',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '腾讯云短信应用ID选项-默认',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'tencent.sms.singName',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '腾讯云短信签名选项-默认',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'tencent.sms.templateId.userRegister',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '短信模版-用户注册',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'tencent.sms.templateId.userLogin',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '短信模版-用户登录',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'tencent.sms.PhonePre',
            'item_value' => '+86',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '腾讯云短信手机号前缀选项(国内)',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        // 腾讯地图
        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'tencent.map.key',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '腾讯地图key',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'tencent.map.api.regionUrl',
            'item_value' => 'https://apis.map.qq.com/ws/geocoder/v1/',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '逆地址解析接口',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        // 二维码
        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'qrcode.redirectUrl',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '二维码跳转链接',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 40,
            'item_label' => 'qrcode.logo',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => 'config/file/config/default_logo.png',
            'item_introduction' => '二维码logo',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'qrcode.noticeTitle',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '二维码提示字',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        // 存储桶
        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'cloud.store',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '云存储平台',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'qiniu.accessKey',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '七牛云的accessKey',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'qiniu.secretKey',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '七牛云的secretKey',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 20,
            'item_label' => 'qiniu.cdn_url',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => 'cdn加速域名',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);

        SystemConfig::create([
            'item_type' => 10,
            'item_label' => 'qiniu.bucket.default',
            'item_value' => '',
            'item_price' => 0,
            'item_path' => '',
            'item_introduction' => '七牛云的存储桶',
            'created_time' => time(),
			'created_at'=>date('Y-m-d H:i:s'),
            'sort' => 100,
        ]);


        $this->command->info('✅填充laravel-fast-api系统配置数据完成');
    }
}
