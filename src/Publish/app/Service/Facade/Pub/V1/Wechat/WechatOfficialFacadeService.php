<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-07 10:25:15
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-06 19:19:28
 * @FilePath: \app\Service\Facade\Pub\V1\Wechat\WechatOfficialFacadeService.php
 */

namespace App\Service\Facade\Pub\V1\Wechat;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use App\Exceptions\Common\CommonException;


/**
 * @see App\Models\LaravelFastApi\V1\System\Platform\SystemWechatConfig
 */
use App\Models\LaravelFastApi\V1\System\Platform\SystemWechatConfig;

use YouHuJun\Tool\App\Facade\V1\Wechat\Official\WechatOfficialWebAuthFacade;

/**
 * @see \App\Facade\Pub\V1\Wechat\WechatOfficialFacade
 */
class WechatOfficialFacadeService
{
   public function test()
   {
       echo "WechatOfficialFacadeService test";
   }

   //系统平台微信配置对象
   protected $wechatOfficial;

   // 10 静默授权 20主动授权
   protected $scope_array  = [10=>'snsapi_base',20=>'snsapi_userinfo'];


    /**
    * 初始化
    *
    * @param [type] $scopeIndex
    * @return void
    */
   private function init()
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


		if ((!$wechatOfficial || !isset($wechatOfficial->appid)) || (!$wechatOfficial || !isset($wechatOfficial->appsecret))) 
		{
			throw new CommonException('WechatOfficialConfigError');
		}

		$this->wechatOfficial = $wechatOfficial;

   }

   /**
	* 开发环境初始化
	*
	* @param  [int] $scope_type 默认是用户授权
	*/
   private function devWechatInit()
   {
	  $wechatOfficial = null;

	  $cacheWechatOfficial = unserialize(Cache::get('wechat.official.object.dev'));

	  $wechatOfficial = $cacheWechatOfficial;

	  if(!$wechatOfficial)
	  {
		$newWechatOfficial =  SystemWechatConfig::where('type',40)->first();

		if($newWechatOfficial)
		{
			$wechatOfficial = $newWechatOfficial;

			Cache::put('wechat.official.object.dev',serialize($newWechatOfficial));
		}
	  }

	  if(!isset($wechatOfficial->appid) || !isset($wechatOfficial->appsecret))
	  {
		throw new CommonException('WechatOfficialConfigError');
	  }

	  $this->wechatOfficial = $wechatOfficial;

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
   public function toGetCodeUrl($validated)
   {
		['scope_type'=> $scope_type,'auth_redirect_url'=>$auth_redirect_url,'state'=>$state ] = $validated;
		
        $result =  code(config('common_code.WechatOfficialGetCodeError'));

		if(!config('youhujun.wechat_official_mode'))
		{
			//生产环境初始化
			$this->init();
		}
		else
		{
			//开发环境初始化
        	$this->devWechatInit();
		}

		$wechatOfficial = $this->wechatOfficial;
		
        $appid =   trim($wechatOfficial->appid);
		$appsecret = trim($wechatOfficial->appsecret);

		$config = [
			'appid' =>$appid,
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

        $result =  code(['code'=> 0,'msg'=>'微信公众号发起授权成功!'],['data'=>$data]);

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
   public function  authToLoginByCode($validated)
   {
        $result =  code(config('common_code.WechatOfficialAuthError'));

        if(!config('youhujun.wechat_official_mode'))
		{
			//生产环境初始化
			$this->init();
		}
		else
		{
			//开发环境初始化
        	$this->devWechatInit();
		}

		$wechatOfficial = $this->wechatOfficial;

        $appid =  trim($wechatOfficial->appid);

        $appsercet = trim($wechatOfficial->appsecret);

		$config = [
			'appid' => $appid,
			'appsecret' => $appsercet
		];
    
		// 一键式授权
		$result = WechatOfficialWebAuthFacade::authorize(
			$config,
			$validated['code'],
			true
		);

   		return collect(['authResultArray'=>$result,'wechatOfficialObject'=>$wechatOfficial]);

   }

   
   

}
