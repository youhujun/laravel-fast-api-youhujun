<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-02-25 06:04:06
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-30 09:19:05
 * @FilePath: \youhu-laravel-api-12\config\custom\common\api\youhubase.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

$youhubase_host = env('APP_URL');

return [
    //获取临时凭证
    'GetAccessToken' => "{$youhubase_host}/api/v1/base/auth/getAccessToken",
    //测试api通信
    'test' => "{$youhubase_host}/api/v1/base/test/test",
];
