<?php

/*
 * @Descripttion: 自定义验证规则错误码配置
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-09 10:00:05
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-28 14:46:01
 * @FilePath: \xue-hu-api-12d:\wwwroot\PHP\Components\Laravel\youhujun\xuehu-xueer-youhujun\src\Publish\config\custom\xuehu\public\rule\rule_code.php
 */



//|--后台公共错误码
$adminCodeArray = [

];

//|--手机公共错误码
$phoneCodeArray = [

];



$errorCodeArray = array_merge(
    $adminCodeArray,
    $phoneCodeArray
);

return $errorCodeArray;
