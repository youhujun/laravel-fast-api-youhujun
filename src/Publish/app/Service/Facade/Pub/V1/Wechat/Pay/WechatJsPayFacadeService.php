<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 09:52:28
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 02:19:59
 * @FilePath: \app\Service\Facade\Pub\V1\Wechat\Pay\WechatJsPayFacadeService.php
 */

namespace App\Service\Facade\Pub\V1\Wechat\Pay;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;


use App\Exceptions\Common\CommonException;

use App\Models\LaravelFastApi\V1\System\Platform\SystemWechatConfig;

use YouHuJun\Tool\App\Facade\V1\Wechat\Pay\JSAPI\WechatPayByJSAPIFacade;

/**
 * @see \App\Facade\Pub\V1\Wechat\Pay\WechatJsPayFacade
 */
class WechatJsPayFacadeService
{
   public function test()
   {
       echo "WechatJsPayFacadeService test";
   }

   private $config = [
    'merchantId' => '',
    'merchantSerialNumber' => '',
    'merchantPrivateKeyPath' => '',
    'wechatpayCertificatePath' => '',
    'officialAppid' => '',
    'notifyUrl' => ''
];

   /**
    * 初始化
    *
    * @return void
    */
   public function init()
   {
        $this->initClient();

        $this->OtherInit();
   }


   /**
    * 客户端初始化
    *
    * @return void
    */
   public function initClient()
   {
        // 商户相关配置，
		$this->config['merchantId'] =  trim(Cache::get('wechat.merchant.merchantId'));
       
        if(!$this->config['merchantId'])
        {
            throw new CommonException('WechatMerchantMerchantIdError');
        }

		$this->config['merchantSerialNumber'] = trim(Cache::get('wechat.merchant.merchantSerialNumber'));
        
        if(!$this->config['merchantSerialNumber'])
        {
            throw new CommonException('WechatMerchantMerchantSerialNumberError');
        }

        // 注意 linux下 要给到 777 权限
        //apiclient_key.pem
		$this->config['merchantPrivateKeyPath'] = storage_path('app/public/').trim(Cache::get('wechat.merchant.merchantPrivateKey'));
       
        if(!$this->config['merchantPrivateKeyPath'] || !file_exists($this->config['merchantPrivateKeyPath']))
        {
            throw new CommonException('WechatMerchantMerchantPrivateKeyError');
        }

       $this->config['wechatpayCertificatePath'] = storage_path('app/public/').trim(Cache::get('wechat.merchant.wechatpayCertificate'));
        //wechatpay.pem

        if(! $this->config['wechatpayCertificatePath'] || !file_exists( $this->config['wechatpayCertificatePath']))
        {
            throw new CommonException('WechatMerchantWechatpayCertificateError');
        }
   }

   /**
    * 其他需要初始化的配置
    *
    * @return void
    */
   public function OtherInit()
   {
		$wechatOfficial = null;

		$cacheWechatOfficial = unserialize(Cache::get('wechat.official.object'));

		$wechatOfficial = $cacheWechatOfficial;

		if(!$wechatOfficial)
		{
			$newWechatOfficial =  SystemWechatConfig::where('type',30)->first();

			if($newWechatOfficial)
			{
				$wechatOfficial = $newWechatOfficial;

				Cache::put('wechat.official.object',serialize($newWechatOfficial));
			}
		}

		$this->config['officialAppid']  =  $wechatOfficial->appid;


		if(!$this->config['officialAppid'])
		{
			throw new CommonException('WechatOfficialAppIdError');
		}

		$this->config['notifyUrl'] = trim(Cache::get('wechat.merchant.notifyUrl.JsPayNotifyUrl'));

		if(!$this->config['notifyUrl'])
		{
			throw new CommonException('WecahtMerchantNotifyUrlJsPayNotifyUrlError');
		}
   }

   /**
    * JSAPI下单
    *
    * @param [type] $jsonData
    * @return void
    */
   public function prePayOrder($jsonData)
   {
        $result = code(config('common_code.PrePayOrderByWechatJsError'));

        $this->init();

		$result = WechatPayByJSAPIFacade::prePayOrder($this->config, $jsonData);

		return $result;

   }

}
