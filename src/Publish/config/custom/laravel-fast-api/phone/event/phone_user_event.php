<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-15 11:03:09
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-09-05 16:40:37
 * @FilePath: \config\custom\phone\event\phone_user_event.php
 */

 //总的验证码地址
$totalCodeArray =  [
	//清理缓存
    'ClearUserCache'=>['code'=>10000,'info'=>'用户清理缓存','event'=>'ClearUserCache'],

	//绑定手机号
    'UserBindPhone'=>['code'=>10000,'info'=>'用户绑定手机号','event'=>'UserBindPhone'],

	//重置手机密码
    'RestPasswordByPhone'=>['code'=>10000,'info'=>'重置手机密码','event'=>'RestPasswordByPhone'],

	// 更新昵称
    'UpdateUserNickName'=>['code'=>10000,'info'=>'更新昵称','event'=>'UpdateUserNickName'],
	// 更新手机号
    'UpdateUserPhone'=>['code'=>10000,'info'=>'更新手机号','event'=>'UpdateUserPhone'],
];

//我的验证码地址
$myCodeArray = [
	'AddUserAddress'=>['code'=>10000,'info'=>'添加用户地址','event'=>'AddUserAddress'],
	'UpdateUserAddress'=>['code'=>10000,'info'=>'更新用户地址','event'=>'UpdateUserAddress'],
	'DeleteUserAddress'=>['code'=>10000,'info'=>'删除用户地址','event'=>'DeleteUserAddress'],
	'SetDefaultUserAddress'=>['code'=>10000,'info'=>'设置默认用户地址','event'=>'SetDefaultUserAddress'],
	'UnSetDefaultUserAddress'=>['code'=>10000,'info'=>'取消默认用户地址','event'=>'UnSetDefaultUserAddress'],
	'SetTopUserAddress'=>['code'=>10000,'info'=>'置顶用户地址','event'=>'SetTopUserAddress'],
	'UnSetTopUserAddress'=>['code'=>10000,'info'=>'取消置顶用户地址','event'=>'UnSetTopUserAddress'],
];

//所有事件码集合
$eventCodeArray = array_merge(
    $myCodeArray,
    $totalCodeArray
);

 return $eventCodeArray;