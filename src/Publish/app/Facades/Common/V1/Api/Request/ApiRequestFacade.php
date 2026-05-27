<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-04 03:11:02
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-04 16:28:03
 * @FilePath: \youhu-laravel-api-12\app\Facades\Common\V1\Api\Request\ApiRequestFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 * 使用示例:
 *
$url = config('shardmap_api_url.SyncMapData');

$params = ['shardInfo' => $shardInfo,'modelData' => ['account_name' => $userObject->account_name]];

ApiRequestFacade::decoder($userUid, $url, $params, $originServiceFlag = 'youhu-base');

 */

namespace App\Facades\Common\V1\Api\Request;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Common\V1\Api\Request\ApiRequestFacadeService
 *
 */
class ApiRequestFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "ApiRequestFacade";
    }

    public static function decoder(string $userUid, string $url, array $params, string $originServiceFlag): string
    {
        return static::getFacadeRoot()->decoder($userUid, $url, $params, $originServiceFlag);
    }
}
