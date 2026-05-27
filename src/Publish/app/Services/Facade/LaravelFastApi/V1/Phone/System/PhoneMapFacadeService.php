<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-14 16:16:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-01 04:51:04
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Phone\System\PhoneMapFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\System;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Phone\CommonException;
use App\Events\Phone\CommonEvent;
use App\DTOs\LaravelFastApi\V1\Phone\System\Map\GetLocationRegionByH5DTO;
use App\Events\LaravelFastApi\V1\Phone\User\Location\UserLocationLogEvent;
use App\Models\LaravelFastApi\V1\User\User;
use App\Facades\Pub\V1\Map\TencentMapFacade;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\System\MapController
 * @see \App\Facades\LaravelFastApi\V1\Phone\System\PhoneMapFacade
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
     * @param  [type] $userObject
     */
    public function getLocationRegionByH5(GetLocationRegionByH5DTO $requestDTO, User $userObject)
    {
        $result = code(config('phone_code.GetLocationRegionByH5TencentMapError'));

        /**
         * @see \App\Services\Facade\Pub\V1\Map\TencentMapFacadeService
         */
        $dataResultArray = TencentMapFacade::getLocationRegionByH5($requestDTO);

        // p($dataResultArray);
        // die;
        /**
         * Array
            (
                [location] => stdClass Object
                    (
                        [lat] => 37.54061
                        [lng] => 121.40011
                    )

                [address] => 山东省烟台市芝罘区市府街63号
                [address_component] => stdClass Object
                    (
                        [nation] => 中国
                        [province] => 山东省
                        [city] => 烟台市
                        [district] => 芝罘区
                        [street] => 市府街
                        [street_number] => 市府街63号
                    )

                [ad_info] => stdClass Object
                    (
                        [nation_code] => 156
                        [adcode] => 370602
                        [phone_area_code] => 0535
                        [city_code] => 156370600
                        [name] => 中国,山东省,烟台市,芝罘区
                        [location] => stdClass Object
                            (
                                [lat] => 37.541312
                                [lng] => 121.400303
                            )

                        [nation] => 中国
                        [province] => 山东省
                        [city] => 烟台市
                        [district] => 芝罘区
                        [_distance] => 0
                    )

                [address_reference] => stdClass Object
                    (
                        [famous_area] => stdClass Object
                            (
                                [id] => 8558298051031
                                [title] => 向阳
                                [location] => stdClass Object
                                    (
                                        [lat] => 37.535562
                                        [lng] => 121.39736
                                    )

                                [_distance] => 0
                                [_dir_desc] => 内
                            )

                        [business_area] => stdClass Object
                            (
                                [id] => 8558298051031
                                [title] => 向阳
                                [location] => stdClass Object
                                    (
                                        [lat] => 37.535562
                                        [lng] => 121.39736
                                    )

                                [_distance] => 0
                                [_dir_desc] => 内
                            )

                        [town] => stdClass Object
                            (
                                [id] => 370602001
                                [title] => 向阳街道
                                [location] => stdClass Object
                                    (
                                        [lat] => 37.534313
                                        [lng] => 121.399845
                                    )

                                [_distance] => 0
                                [_dir_desc] => 内
                            )

                        [landmark_l2] => stdClass Object
                            (
                                [id] => 16123012369974312590
                                [title] => 烟台市芝罘区人民政府
                                [location] => stdClass Object
                                    (
                                        [lat] => 37.541038
                                        [lng] => 121.400292
                                    )

                                [_distance] => 0
                                [_dir_desc] => 内
                            )

                        [street] => stdClass Object
                            (
                                [id] => 14677662124692694598
                                [title] => 市府街
                                [location] => stdClass Object
                                    (
                                        [lat] => 37.54024
                                        [lng] => 121.401878
                                    )

                                [_distance] => 16.4
                                [_dir_desc] => 北
                            )

                        [street_number] => stdClass Object
                            (
                                [id] => 56612323734693544304590
                                [title] => 市府街63号
                                [location] => stdClass Object
                                    (
                                        [lat] => 37.5405
                                        [lng] => 121.40026
                                    )

                                [_distance] => 18
                                [_dir_desc] =>
                            )

                        [crossroad] => stdClass Object
                            (
                                [id] => 1169559664178945959
                                [title] => 丹桂街与市府街交叉口
                                [location] => stdClass Object
                                    (
                                        [lat] => 37.540465
                                        [lng] => 121.399571
                                    )

                                [_distance] => 44.7
                                [_dir_desc] => 东
                            )

                    )

                [formatted_addresses] => stdClass Object
                    (
                        [recommend] => 向阳烟台市芝罘区人民政府(市府街北)
                        [rough] => 向阳烟台市芝罘区人民政府(市府街北)
                        [standard_address] => 山东省烟台市芝罘区市府街76号
                    )

            )
         */

        $validated = $requestDTO->toArray();
        //事件处理
        UserLocationLogEvent::dispatch($userObject, $validated, $dataResultArray['address']);

        $result = code(['code' => 0,'msg' => '腾讯地图获取成功!'], ['data' => $dataResultArray]);

        return  $result;
    }
}
