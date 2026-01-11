<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-07 19:20:24
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-24 14:56:10
 * @FilePath: \config\custom\laravel-fast-api\public\code\common_wechat_code.php
 */

return [
     //微信公众号
	'WechatOfficialGetCodeError'=>['code'=>10000,'msg'=>'微信公众号发起授权失败','error'=>'WechatOfficialGetCodeError'],
    'WechatOfficialConfigError'=>['code'=>10000,'msg'=>'微信公众号配置有误','error'=>'WechatOfficialConfigError'],
    'WechatOfficialAuthError'=>['code'=>10000,'msg'=>'微信公众号授权失败','error'=>'WechatOfficialAuthError'],
    'WechatOfficialUserInfoError'=>['code'=>10000,'msg'=>'微信公众号获取用户信息失败','error'=>'WechatOfficialUserInfoError'],
	//微信查找用户不存在
    'WechatOfficialFindUserError'=>['code'=>10000,'msg'=>'微信查找用户不存在!','error'=>'WechatOfficialFindUserError'],

    
    //微信支付
	'WechatJsPayNotifyError'=>['code'=>'FAIL','message'=>'WechatJsPay回调失败!','error'=>'WechatJsPayNotifyError'],
    'WechatJsPayNotifyUpdateOrderError'=>['code'=>'FAIL','message'=>'订单状态修改!','error'=>'WechatJsPayNotifyUpdateOrderError'],
    'WechatMerchantMerchantIdError'=>['code'=>10000,'msg'=>'微信商户Id不存在!','error'=>'WechatMerchantMerchantIdError'],
    'WechatMerchantMerchantSerialNumberError'=>['code'=>10000,'msg'=>'微信商户API证书序列号不存在!','error'=>'WechatMerchantMerchantSerialNumberError'],
    'WechatMerchantMerchantPrivateKeyError'=>['code'=>10000,'msg'=>'微信商户私钥不存在!','error'=>'WechatMerchantMerchantPrivateKeyError'],
    'WechatMerchantWechatpayCertificateError'=>['code'=>10000,'msg'=>'微信商户平台证书不存在!','error'=>'WechatMerchantWechatpayCertificateError'],
    'WecahtMerchantNotifyUrlJsPayNotifyUrlError'=>['code'=>10000,'msg'=>'微信Js支付回调链接不存在!','error'=>'WecahtMerchantNotifyUrlJsPayNotifyUrlError'],
    'WechatOfficialAppIdError'=>['code'=>10000,'msg'=>'微信公众号appid不存在!','error'=>'WechatOfficialAppIdError'],

    'PrePayOrderByWechatJsError'=>['code'=>10000,'msg'=>'微信支付订单不存在!','error'=>'PrePayOrderByWechatJsError'],

    //微信支付解密
    'WechatApiV3KKeyNotExistsError'=>['code'=>10000,'msg'=>'微信支付的ApiV3Key不存在!','error'=>'WechatApiV3KKeyNotExistsError'],

	//微信小程序
	'SystemWechatConfigIsNullError'=>['code'=>10000,'msg'=>'微信商户Id不存在!','error'=>'SystemWechatConfigIsNullError'],

];
