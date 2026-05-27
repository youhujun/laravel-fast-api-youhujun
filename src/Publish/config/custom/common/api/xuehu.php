<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-02-25 06:03:43
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-30 23:33:09
 * @FilePath: \youhu-laravel-api-12\config\custom\common\api\xuehu.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

$xuehu_host = env('APP_URL');

return [
    //获取临时凭证
    'GetAccessToken' => "{$xuehu_host}/api/v1/xuehu/auth/getAccessToken",
    //测试api通信
    'test' => "{$xuehu_host}/api/v1/xuehu/test/test"
];
