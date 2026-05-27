<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-02-23 17:36:07
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-02-23 17:39:02
 * @FilePath: \youhu-laravel-api-12\config\custom\common\event\common_event.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

$totalEventArray =  [
    'AddApiEventLog' => ['code' => 10000, 'info' => '添加api事件日志','event' => 'AddApiEventLog'],
];

$apiEventArray = [];

$allEventArray = array_merge(
    $totalEventArray,
    $apiEventArray
);

return $allEventArray;
