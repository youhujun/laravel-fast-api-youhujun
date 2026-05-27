<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-02 18:13:30
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-25 02:03:31
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Phone\System\Region\PhoneRegionFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\System\Region;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\Phone\CommonException;
use App\Events\Phone\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Services\Facade\Traits\V1\Es\EsAlwaysService;
use App\DTOs\LaravelFastApi\V1\Phone\System\Region\GetRegionByIdDTO;
use App\Models\LaravelFastApi\V1\System\Region\Region;
use App\Models\LaravelFastApi\V1\User\User;
use App\Http\Resources\LaravelFastApi\V1\Es\Phone\System\Region\EsRegionResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Phone\System\Region\EsRegionCollection;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\System\RegionController
 * @see \App\Facades\Phone\System\Region\PhoneRegionFacade
 */
class PhoneRegionFacadeService
{
    use EsAlwaysService;
    public function test()
    {
        echo "PhoneRegionFacadeService test";
    }

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $esIndexName = config('common_es.indices.system.regions');
        $this->init((new Region()), $esIndexName, 'deep');
    }


    /**
     * 获取地区id
     *
     * @param  [type] $validated
     * @param  [type] $userObject
     */
    public function getRegionById(GetRegionByIdDTO $requestDTO, $userObject)
    {
        $result = code(config('phone_code.GetRegionByIdError'));

        $esIndexName = config('common_es.indices.system.regions');

        $parent_id = $requestDTO->parent_id;

        $max_size = config('common_es.max_result_window');

        $regionColection = EsQueryFacade::index($esIndexName)->whereNull('deleted_at')->where('parent_id', $parent_id)->limit($max_size)->get();

        EsRegionResource::showControl(1);

        $data['data'] = EsRegionResource::collection($regionColection);

        $result = code(['code' => 0,'msg' => '获取地区成功!'], $data);

        return $result;
    }

    /**
     * 获取树形地区
     *
     * @param  [type] $validated
     * @param  [type] $userObject
     */
    public function getTreeRegions(User $userObject)
    {
        $result = code(config('phone_code.GetTreeRegionError'));

        $redisKey = config('common_redis.system_region.key');
        $redisField = config('common_redis.system_region.field');

        $treeReggionRedisString = Redis::hget($redisKey, $redisField);

        if ($treeReggionRedisString) {
            $treeRegionCollection = unserialize($treeReggionRedisString);
        }

        if (!$treeRegionCollection) {
            $treeRegionCollection = $this->getTreeData();
            Redis::hset($redisKey, $redisField, serialize($treeRegionCollection));
        }

        EsRegionResource::showControl(1);

        $data['data'] = EsRegionResource::collection($treeRegionCollection);

        $result = code(['code' => 0,'msg' => '获取树形地区成功!'], $data);

        return  $result;
    }
}
