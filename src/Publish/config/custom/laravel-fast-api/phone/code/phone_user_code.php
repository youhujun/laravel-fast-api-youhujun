<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-25 09:47:00
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-10-12 21:01:52
 * @FilePath: \config\custom\phone\code\phone_user_code.php
 */

$totalCodeArray =  [
    //实名认证
    'UserRealAuthApplyError'=>['code'=>10000,'msg'=>'用户申请实名认证失败','error'=>'UserRealAuthApplyError'],
    'UpdateUserRealAuthStatusError'=>['code'=>10000,'msg'=>'更新实名认证申请状态失败!','error'=>'UpdateUserRealAuthStatusError'],
    'UserHasRealAuthApplyError'=>['code'=>10000,'msg'=>'已经实名认证了!','error'=>'UserHasRealAuthApplyError'],
    'UserRealAuthApplyNoUserInfoError'=>['code'=>10000,'msg'=>'用户申请实名认证缺失用户信息','error'=>'UserRealAuthApplyNoUserInfoError'],
    'UserRealAuthApplySaveUserInfoError'=>['code'=>10000,'msg'=>'用户申请实名认证保存用户信息失败','error'=>'UserRealAuthApplySaveUserInfoError'],
    'UserRealAuthApplySaveUserIdCardError'=>['code'=>10000,'msg'=>'用户申请实名认证保存用户身份证信息失败','error'=>'UserRealAuthApplySaveUserIdCardError'],


    'UpdateUserNickNameError'=>['code'=>10000,'msg'=>'用户更新昵称失败','error'=>'UpdateUserNickNameError'],
    'UpdateUserPhoneError'=>['code'=>10000,'msg'=>'用户更新手机号失败','error'=>'UpdateUserPhoneError'],


    //清理缓存
    'ClearUserCacheError'=>['code'=>10000,'msg'=>'清理缓存失败!','error'=>'ClearUserCacheError'],

    'ClearUserCacheError'=>['code'=>10000,'msg'=>'清理缓存失败!','error'=>'ClearUserCacheError'],

];

//用户地址
$myCodeArray = [
	'GetUserAddressError'=>['code'=>10000,'msg'=>'获取用户地址失败','error'=>'GetUserAddressError'],
	'AddUserAddressError'=>['code'=>10000,'msg'=>'添加用户地址失败','error'=>'AddUserAddressError'],
	'UpdateUserAddressError'=>['code'=>10000,'msg'=>'更新用户地址失败','error'=>'UpdateUserAddressError'],
	'DeleteUserAddressError'=>['code'=>10000,'msg'=>'删除用户地址失败','error'=>'DeleteUserAddressError'],
	'SetDefaultUserAddressError'=>['code'=>10000,'msg'=>'设置默认用户地址失败','error'=>'SetDefaultUserAddressError'],
	'SetTopUserAddressError'=>['code'=>10000,'msg'=>'置顶用户地址失败','error'=>'SetTopUserAddressError'],
];

$errorCodeArray = array_merge(
    $myCodeArray,
    $totalCodeArray,
);

 return $errorCodeArray;
