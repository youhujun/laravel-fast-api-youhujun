<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-05-30 20:05:13
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-20 17:37:40
 */

 return [

     /*
    |--------------------------------------------------------------------------
    | Version
    |--------------------------------------------------------------------------
    |
    | This value is the verson of your application.
    |
    */

    //定义项目版本号
    'version' => env('APP_VERSION', 'V1'),
    //app_name 默认为YouHu
	//目前用来判断数据填充
	'app_name'=>env('APP_NAME','YOUHU'),
    //websocket 端口号
	'websocket_port'=>env('WEBSOCKET_PORT',9001),

 ];
