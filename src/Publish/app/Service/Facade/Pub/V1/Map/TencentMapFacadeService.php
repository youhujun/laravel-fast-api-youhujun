<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-14 14:53:46
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-10 04:09:16
 * @FilePath: \App\Services\Facade\Pub\V1\Map\TencentMapFacadeService.php
 */

namespace App\Services\Facade\Pub\V1\Map;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\Common\CommonException;
use YouHuJun\Tool\App\Facades\V1\Map\Tencent\TencentMapFacade;

/**
 * @see \App\Facades\Pub\V1\Map\TencentMapFacade
 */
class TencentMapFacadeService
{
    public function test()
    {
        echo "TencentMapFacadeService test";
    }

    /**
     * 通过H5获取位置信息
     */
    public function getLocationRegionByH5($validated)
    {
        $result = code(config('common_code.GetLocationRegionByH5TencentMapError'));

        if (!(isset($validated['latitude']) && isset($validated['longitude']))) {
            throw new CommonException('GetLocationRegionByH5TencentMapParamError');
        }

        //维度
        $latitude = $validated['latitude'];
        //经度
        $longitude = $validated['longitude'];


        $key = trim(Cache::get('tencent.map.key'));

        if (!$key) {
            throw new CommonException('TencentMapNoKeyError');
        }

        $regionUrl = trim(Cache::get('tencent.map.api.regionUrl'));

        if (!$regionUrl) {
            throw new CommonException('TencentMapApiRegionUrlError');
        }



        $config = [
            'key' => $key,
            'regionUrl' => $regionUrl
        ];

        $locationData = [
            'latitude' => $latitude,     // 纬度
            'longitude' => $longitude    // 经度
        ];

        $result = TencentMapFacade::getLocationRegionByH5($config, $locationData);

        return $result;
    }
}
