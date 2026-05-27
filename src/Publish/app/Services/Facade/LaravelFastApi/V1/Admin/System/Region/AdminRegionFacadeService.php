<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-31 23:22:53
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-06 14:32:30
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\System\Region\AdminRegionFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\System\Region;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Collection;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Services\Facade\Traits\V1\Es\EsAlwaysService;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\System\Region\AddRegionDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Region\UpdateRegionDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Region\MoveRegionDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Region\DeleteRegionDTO;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Region\Region;
use App\Models\LaravelFastApi\V1\User\Info\UserAddress;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Region\EsRegionResource;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\Region\RegionController
 * @see \App\Facades\LaravelFastApi\V1\Admin\System\Region\AdminRegionFacade
 */
class AdminRegionFacadeService
{
    use EsAlwaysService;
    public function test()
    {
        echo "AdminRegionFacadeService test";
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
     * 结合redis获取所有地区
     *
     * @return void
     */
    public function getAllRegion()
    {
        $result = code(config('admin_code.GetAllRegionError'));

        $allRegionCollection = $this->getAllData();

        EsRegionResource::showControl(1);

        $dataArray['data'] = EsRegionResource::collection($allRegionCollection);

        $result = code(['code' => 0,'msg' => '获取所有地区成功!'], $dataArray);

        return  $result;
    }

    /**
     * 结合redis获取所有树形地区
     *
     * @return void
     */
    public function getTreeRegion()
    {
        $result = code(config('admin_code.GetTreeRegionError'));

        $redisKey = config('common_redis.system_region.key');
        $redisField = config('common_redis.system_region.field');

        $redisString = Redis::hget($redisKey, $redisField);

        if($redisString){
            $treeRegionColelction = unserialize($redisString);
        }else{
            $treeRegionColelction = $this->getTreeData();

            Redis::hset($redisKey, $redisField, serialize($treeRegionColelction));
        }

        EsRegionResource::showControl(1);

        $data['data'] = EsRegionResource::collection($treeRegionColelction);

        $result = code(['code' => 0,'msg' => '获取树形地区成功!'], $data);

        return  $result;
    }

    /**
     *  添加地区
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function addRegion(AddRegionDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddRegionError'));

        $validated = $requestDTO->toArray();

        $regionObject = new Region();

        foreach ($validated as $key => $value) {
            if (\is_null($value)) {
                $value = "";
            }

            $regionObject->$key = $value;
        }

        $regionObject->created_time = time();
        $regionObject->created_at = time();

        $regionResult = $regionObject->save();

        if (!$regionResult) {
            throw new CommonException('AddRegionError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'AddRegion');

        $esIndexName = config('common_es.indices.system.regions');

        $insertDataArray = [
            '_docId' => $regionObject->id,
            'id' => $regionObject->id,
            'parent_id' => $regionObject->parent_id,
            'deep' => $regionObject->deep,
            'region_name' => $regionObject->region_name,
            'region_area' => $regionObject->region_area,
            'latitude' => $regionObject->latitude,
            'longitude' => $regionObject->longitude,
            'sort' => $regionObject->sort,
            'created_time' => $regionObject->created_time,
            'updated_time' => $regionObject->updated_time,
            'created_at' => $regionObject->created_at,
            'updated_at' => $regionObject->updated_at,
            'deleted_at' => $regionObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($esIndexName, $insertDataArray, $regionObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加区域失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$regionObject' => $regionObject,'$adminObject' => $adminObject], 'AdminRegionFacadeService', 'handleError');
            throw new CommonException('EsAddRegionError');
        }

      

        $redisKey = config('common_redis.system_region.key');
        $redisField = config('common_redis.system_region.field');
        Redis::hdel($redisKey, $redisField);

        $result = code(['code' => 0,'msg' => '添加地区成功!']);

        return $result;
    }

    /**
     * 更新地区
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function updateRegion(UpdateRegionDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateRegionError'));

        $validated = $requestDTO->toArray();

        $regionObject = Region::find($validated['id']);

        if (!$regionObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];
        $updateDataArray = [];

        $where[] = ['revision','=',$regionObject->revision];

        foreach ($validated as $key => $value) {
            if ($key === 'id') {
                \array_push($where, ['id','=',$value]);
                continue;
            }

            if (\is_null($value)) {
                $value = "";
            }

            $updateDataArray[$key] = $value;
        }

        $updateDataArray['updated_time'] = time();
        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());
        $updateDataArray['revision']  = $regionObject->revision + 1;


        $regionResult = Region::where($where)->update($updateDataArray);

        if (!$regionResult) {
            throw new CommonException('UpdateRegionError');
        }

        $regionObject = $regionObject->fresh();

        CommonEvent::dispatch($adminObject, $validated, 'UpdateRegion');

        $esIndexName = config('common_es.indices.system.regions');

        $updateDataArray = [
            '_docId' => $regionObject->id,
            'id' => $regionObject->id,
            'parent_id' => $regionObject->parent_id,
            'deep' => $regionObject->deep,
            'region_name' => $regionObject->region_name,
            'region_area' => $regionObject->region_area,
            'latitude' => $regionObject->latitude,
            'longitude' => $regionObject->longitude,
            'sort' => $regionObject->sort,
            'created_time' => $regionObject->created_time,
            'updated_time' => $regionObject->updated_time,
            'created_at' => $regionObject->created_at,
            'updated_at' => $regionObject->updated_at,
            'deleted_at' => $regionObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($esIndexName, $regionObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新区域失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$regionObject' => $regionObject,'$adminObject' => $adminObject], 'AdminRegionFacadeService', 'handleError');
            throw new CommonException('EsUpdateRegionError');
        }

        $redisKey = config('common_redis.system_region.key');
        $redisField = config('common_redis.system_region.field');
        Redis::hdel($redisKey, $redisField);


        $result = code(['code' => 0,'msg' => '更新地区成功!']);

        return $result;
    }

    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function moveRegion(MoveRegionDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MoveRegionError'));

        $validated = $requestDTO->toArray();

        $regionObject = Region::find($validated['id']);

        if (!$regionObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $regionRevision = $regionObject->revision;

        $oldDeep = $regionObject->deep;

        $parentDeep = 1;

        if ($validated['parent_id']) {
            $parentRegion = Region::find($validated['parent_id']);

            $parentDeep = $parentRegion->deep + 1;
        }


        if (self::$dropType[$validated['dropType']] == 10) {
            $regionUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'deep' => $parentDeep,
                'revision' => $regionRevision + 1
            ];
        }

        if (self::$dropType[$validated['dropType']] == 20) {
            $regionUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'sort' => $validated['sort'],
                'deep' => $parentDeep,
                'revision' => $regionRevision + 1
            ];
        }

        if (self::$dropType[$validated['dropType']] == 30) {
            $regionUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'sort' => $validated['sort'],
                'deep' => $parentDeep,
                'revision' => $regionRevision + 1
            ];
        }

        $regionWhere = [['id','=',$validated['id']],['revision','=',$regionRevision]];

        //更新配置项
        $regionResult = Region::where($regionWhere)->update($regionUpdate);

        if (!$regionResult) {
            throw new CommonException('MoveRegionError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'MoveRegion');

        //修改子级deep
        $deepNumber = $parentDeep - $oldDeep;

        //p($deepNumber);die;

        $updateDeepResult = $this->updateChildrenDeep($regionObject->id, $deepNumber);

        $esIndexName = config('common_es.indices.system.regions');

        $queryArray = [
            'match_all' => new \stdClass()
        ];

        $deleteEsResult = EsFacade::deleteByQuery($esIndexName, $queryArray);

        if (!isset($deleteEsResult['code']) || $deleteEsResult['code'] != 0) {
            plog(['error' => 'EsMoveRegionJobError','$deleteEsResult' => $deleteEsResult,'$adminObject' => $adminObject], 'AdminRegionFacadeService', 'handleError');
            throw new CommonException('EsMoveRegionError');
        }

        \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncRegion();

        $redisKey = config('common_redis.system_region.key');
        $redisField = config('common_redis.system_region.field');
        Redis::hdel($redisKey, $redisField);

        $result = code(['code' => 0,'msg' => '移动地区成功!']);

        return $result;
    }

    /**
     * 删除地区
     *
     * @param [type] $id
     * @param [type] $userObject
     * @return void
     */
    public function deleteRegion(DeleteRegionDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteRegionError'));

        $validated = $requestDTO->toArray();

        $id = $validated['id'];
        //查看是否有子类
        $regionObject = Region::where('parent_id', $id)->get();


        $count = $regionObject->count();

        if ($count) {
            throw new CommonException('DeleteNoRegionError');
        }

        //查看是否有用户具有该地区
        $countryRegion = UserAddress::where('country_id', $id)->get()->count();
        $provinceRegion = UserAddress::where('province_id', $id)->get()->count();
        $regionRegion = UserAddress::where('region_id', $id)->get()->count();
        $cityRegion = UserAddress::where('city_id', $id)->get()->count();
        $townsRegion = UserAddress::where('towns_id', $id)->get()->count();
        $villageRegion = UserAddress::where('village_id', $id)->get()->count();

        if ($countryRegion || $provinceRegion || $regionRegion || $cityRegion || $townsRegion || $villageRegion) {
            throw new CommonException('DeleteNoUserRegionError');
        }

        $delRegionObject = Region::find($id);

        if (!$delRegionObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $delRegionObject->deleted_at = date('Y-m-d H:i:s');

        $delRegionResult =  $delRegionObject->save();

        if (!$delRegionResult) {
            throw new CommonException('DeleteRegionError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'DeleteRegion');

        $esIndexName = config('common_es.indices.system.regions');

        $updateDataArray = [
            '_docId' => $delRegionObject->id,
            'id' => $delRegionObject->id,
            'parent_id' => $delRegionObject->parent_id,
            'deep' => $delRegionObject->deep,
            'region_name' => $delRegionObject->region_name,
            'region_area' => $delRegionObject->region_area,
            'latitude' => $delRegionObject->latitude,
            'longitude' => $delRegionObject->longitude,
            'sort' => $delRegionObject->sort,
            'created_time' => $delRegionObject->created_time,
            'updated_time' => $delRegionObject->updated_time,
            'created_at' => $delRegionObject->created_at,
            'updated_at' => $delRegionObject->updated_at,
            'deleted_at' => $delRegionObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($esIndexName, $delRegionObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es删除区域失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$delRegionObject' => $delRegionObject,'$adminObject' => $adminObject], 'AdminRegionFacadeService', 'handleError');
            throw new CommonException('EsDeleteRegionError');
        }

        $redisKey = config('common_redis.system_region.key');
        $redisField = config('common_redis.system_region.field');

        Redis::hdel($redisKey, $redisField);


        $result = code(['code' => 0,'msg' => '删除地区成功!']);

        return $result;
    }
}
