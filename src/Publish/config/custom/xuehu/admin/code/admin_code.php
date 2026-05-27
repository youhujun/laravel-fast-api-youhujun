<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-21 21:30:22
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-29 14:22:38
 * @FilePath: \config\custom\laravel-fast-api\admin\code\admin_code.php
 */

//后台错误码
$systemCodeArray = [

    //发送异常邮件通知定义
    'EmailArray' => [
        //'serverError',
    ],

];



$errorCodeArray = array_merge(
    $systemCodeArray
);

return $errorCodeArray;
