<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 13:12:32
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 02:16:46
 * @FilePath: \App\Services\Facade\Pub\V1\Wechat\Pay\WechatJsPayDecryptFacadeService.php
 */

namespace App\Services\Facade\Pub\V1\Wechat\Pay;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use App\Exceptions\Common\CommonException;

use YouHuJun\Tool\App\Facades\V1\Wechat\Pay\JSAPI\WechatPayDecryptFacade;


/**
 * @see \App\Facades\Pub\V1\Wechat\Pay\WechatJsPayDecryptFacade
 */
class WechatJsPayDecryptFacadeService
{
   public function test()
   {
       echo "WechatJsPayDecryptFacadeService test";
   }

   /**
    * 将微信支付回调解密
    *
    * @param Request $request
    * @return void
    */
   public function decryptData($notifyData)
   {

		$apiv3Key = trim(Cache::get('wechat.merchant.api_v3_key'));// 在商户平台上设置的APIv3密钥

        if(!$apiv3Key)
        {
			plog(['apiv3Key'=>config('common_code.WechatApiV3KKeyNotExistsError')],'WechatJSAPINotifyDecryptError','WechatJSAPINotifyDecryptError');
            
			throw new CommonException('apiV3KKeyNotExistsError');
        }

		$wechatpayCertificate = storage_path('app/public/').trim(Cache::get('wechat.merchant.wechatpayCertificate'));

        if(!$wechatpayCertificate || !file_exists($wechatpayCertificate))
        {
            throw new CommonException('WechatMerchantWechatpayCertificateError');
        }

		$config = [
			'apiv3Key' => $apiv3Key,
			'wechatpayCertificatePath' => $wechatpayCertificate
		];

		//定义返回数据

		$decryptedData = [];

		try {
			$decryptedData = WechatPayDecryptFacade::decryptData($config, $notifyData);

			plog(['$decryptedData:解密数据1' => $decryptedData],'wechatJsPayNotify','wechatJsPayDecryptedData');

			return $decryptedData;

		} catch (\Exception $e) {
			// 返回失败响应给微信
			throw new CommonException('WechatJsPayNotifyError');
		}
       
   }
}
