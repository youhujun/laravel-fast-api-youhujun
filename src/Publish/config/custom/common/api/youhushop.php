<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-02-25 06:03:56
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-30 09:19:14
 * @FilePath: \youhu-laravel-api-12\config\custom\common\api\youhushop.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */


$youhushop_host = env('APP_URL');

return [
    //获取临时凭证
    'GetAccessToken' => "{$youhushop_host}/api/v1/youhushop/auth/getAccessToken",
    //测试api通信
    'test' => "{$youhushop_host}/api/v1/youhushop/test/test",
];
