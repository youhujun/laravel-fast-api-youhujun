<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-14 16:16:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-29 15:00:07
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Phone\System\PhoneMapFacadeService.php
 */

namespace App\Service\Facade\LaravelFastApi\V1\Phone\System;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Phone\CommonException;
use App\Events\Phone\CommonEvent;
use App\Events\LaravelFastApi\V1\Phone\User\Location\UserLocationLogEvent;
use App\Facade\Pub\V1\Map\TencentMapFacade;

/**
 * @see \App\Facade\LaravelFastApi\V1\Phone\System\PhoneMapFacade
 */
class PhoneMapFacadeService
{
    public function test()
    {
        echo "PhoneMapFacadeService test";
    }

    /**
     * 通过H5获取腾讯地图
     *
     * @param  [type] $validated
     * @param  [type] $user
     */
    public function getLocationRegionByH5($validated, $user)
    {
        $result = code(config('phone_code.GetLocationRegionByH5TencentMapError'));

        $dataResult = TencentMapFacade::getLocationRegionByH5($validated);

        p($dataResult);
        die;



        //事件处理
        UserLocationLogEvent::dispatch($user, $validated, $dataResult->address);

        $result = code(['code' => 0,'msg' => '腾讯地图获取成功!'], ['data' => $dataResult]);

        return  $result;
    }
}
