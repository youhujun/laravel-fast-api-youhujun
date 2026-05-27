<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 09:24:51
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-10-14 18:47:49
 * @FilePath: \config\custom\phone\code\phone_pay_code.php
 */

return [

    'PayOrderError'=>['code'=>10000,'msg'=>'订单支付失败','error'=>'PayOrderError'],
    'PayOrderParamsError'=>['code'=>10000,'msg'=>'订单支付参数缺失','error'=>'PayOrderParamsError'],
    'WechatPayOrderByJsNoUserOpenidError'=>['code'=>10000,'msg'=>'微信H5支付的用户openid缺失!','error'=>'WechatPayOrderByJsNoUserOpenidError'],

    //支付回调
    'WechatJsPayNotifyError'=>['code'=>'FAIL','message'=>'WechatJsPay回调失败!','error'=>'WechatJsPayNotifyError'],

    'WechatJsPayNotifyUpdateOrderError'=>['code'=>'FAIL','message'=>'订单状态修改!','error'=>'WechatJsPayNotifyUpdateOrderError'],

    'UserBonusLogError'=>['code'=>'FAIL','message'=>'用户奖金日志记录失败','error'=>'UserBonusLogError'],

    'UserPrepareBonusError'=>['code'=>'FAIL','message'=>'用户预计奖金日志记录失败','error'=>'UserPrepareBonusError'],

    'ParentUserBonusLogError'=>['code'=>'FAIL','message'=>'用户系统预计奖金日志记录失败','error'=>'ParentUserBonusLogError'],

    'ParentUserPrepareBonusError'=>['code'=>'FAIL','message'=>'用户系统预计分配奖金失败','error'=>'ParentUserPrepareBonusError'],

    'SourceUserBonusLogError'=>['code'=>'FAIL','message'=>'用户系统预计奖金日志记录失败','error'=>'SourceUserBonusLogError'],

    'SourceUserBonusError'=>['code'=>'FAIL','message'=>'用户系统预计分配奖金失败','error'=>'SourceUserBonusError'],

];
