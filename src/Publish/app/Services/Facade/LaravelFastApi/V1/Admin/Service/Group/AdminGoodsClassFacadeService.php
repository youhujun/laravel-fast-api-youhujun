<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-26 10:42:07
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-09 12:43:23
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminGoodsClassFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Service\Group;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Services\Facade\Traits\V1\Es\EsAlwaysService;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\GetSingleGoodsClassDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\AddGoodsClassDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\UpdateGoodsClassDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\MoveGoodsClassDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\DeleteGoodsClassDTO;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Module\Goods\GoodsClass;
use App\Models\LaravelFastApi\V1\System\Module\Goods\GoodsClassUnion;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Goods\EsGoodsClassResource;
use App\Contracts\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\DeleteGoodsClassHandleContract;

/**
 * @see \app\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group\GoodsClassController
 * @see \App\Facades\Admin\Service\Group\AdminGoodsClassFacade
 */
class AdminGoodsClassFacadeService
{
    use EsAlwaysService;
    public function test()
    {
        echo "AdminGoodsClassFacadeService test";
    }

    /**
      * Class constructor.
      */
    public function __construct()
    {
        $esIndexName = config('common_es.indices.business.goods_classes');
        $this->init((new GoodsClass()), $esIndexName, 'deep');
    }

    /**
     * 结合redis获取所有树形地区
     *
     * @return void
     */
    public function getTreeGoodsClass()
    {
        $result = code(config('admin_code.GetGoodsClassError'));

        $treeGoodsClass = $this->getTreeData();

        $data['data'] = [];

        if (count($treeGoodsClass)) {
            $data['data'] = EsGoodsClassResource::collection($treeGoodsClass);
        }

        $result = code(['code' => 0,'msg' => '获取树形产品分类成功'], $data);

        return  $result;
    }

    /**
     *  添加地区
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function addGoodsClass(AddGoodsClassDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddGoodsClassError'));

        $validated = $requestDTO->toArray();

        $goodsClassObject = new GoodsClass();

        foreach ($validated as $key => $value) {
            if (isset($value)) {
                $goodsClassObject->$key = $value;
            }
        }
        //默认可用
        $goodsClassObject->switch = 1;
        $goodsClassObject->created_time = time();
        $goodsClassObject->created_at = time();

        $goodsClassResult =  $goodsClassObject->save();

        if (!$goodsClassResult) {
            throw new CommonException('AddGoodsClassError');
        }

        $esIndexName = config('common_es.indices.business.goods_classes');

        $insertDataArray = [
            '_docId' => $goodsClassObject->id,
            'id' => $goodsClassObject->id,
            'parent_id' => $goodsClassObject->parent_id,
            'deep' => $goodsClassObject->deep,
            'switch' => $goodsClassObject->switch,
            'rate' => $goodsClassObject->rate,
            'goods_class_name' => $goodsClassObject->goods_class_name,
            'goods_class_code' => $goodsClassObject->goods_class_code,
            'goods_class_picture_uid' => $goodsClassObject->goods_class_picture_uid,
            'is_certificate' => $goodsClassObject->is_certificate,
            'certificate_number' => $goodsClassObject->certificate_number,
            'note' => $goodsClassObject->note,
            'sort' => $goodsClassObject->sort,
            'created_time' => $goodsClassObject->created_time,
            'updated_time' => $goodsClassObject->updated_time,
            'created_at' => $goodsClassObject->created_at,
            'updated_at' => $goodsClassObject->updated_at,
            'deleted_at' => $goodsClassObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($esIndexName, $insertDataArray, $goodsClassObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加商品分类失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$goodsClassObject' => $goodsClassObject,'$adminObject' => $adminObject], 'AdminGoodsClassFacadeService', 'handleError');
            throw new CommonException('EsAddGoodsClassError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'AddGoodsClass');

        Redis::hdel('system:config', 'treeGoodsClass');

        $result = code(['code' => 0,'msg' => '添加产品分类成功']);

        return $result;
    }

    /**
     * 更新地区
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function updateGoodsClass(UpdateGoodsClassDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateGoodsClassError'));

        $validated = $requestDTO->toArray();

        $goodsClassObject = GoodsClass::find($validated['id']);

        if (!$goodsClassObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        //查看级别是否变化
        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$goodsClassObject ->revision];

        foreach ($validated as $key => $value) {
            if ($key === 'id') {
                $where[] = ['id','=',$value];
                continue;
            }

            if (isset($value)) {
                $updateDataArray[$key] = $value;

                if ($key === 'rate' && $value > 0) {
                    $updateDataArray[$key] = \bcdiv($value, 100, 2);
                }
            }
        }

        $updateDataArray['revision'] = $goodsClassObject ->revision + 1;

        $updateDataArray['updated_time'] = time();

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        $goodsClassResult = GoodsClass::where($where)->update($updateDataArray);

        if (!$goodsClassResult) {
            throw new CommonException('UpdateGoodsClassError');
        }

        $goodsClassObject = $goodsClassObject->fresh();

        $esIndexName = config('common_es.indices.business.goods_classes');

        $updateDataArray = [
            '_docId' => $goodsClassObject->id,
            'id' => $goodsClassObject->id,
            'parent_id' => $goodsClassObject->parent_id,
            'deep' => $goodsClassObject->deep,
            'switch' => $goodsClassObject->switch,
            'rate' => $goodsClassObject->rate,
            'goods_class_name' => $goodsClassObject->goods_class_name,
            'goods_class_code' => $goodsClassObject->goods_class_code,
            'goods_class_picture_uid' => $goodsClassObject->goods_class_picture_uid,
            'is_certificate' => $goodsClassObject->is_certificate,
            'certificate_number' => $goodsClassObject->certificate_number,
            'note' => $goodsClassObject->note,
            'sort' => $goodsClassObject->sort,
            'created_time' => $goodsClassObject->created_time,
            'updated_time' => $goodsClassObject->updated_time,
            'created_at' => $goodsClassObject->created_at,
            'updated_at' => $goodsClassObject->updated_at,
            'deleted_at' => $goodsClassObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($esIndexName, $goodsClassObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新商品分类失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$goodsClassObject' => $goodsClassObject,'$adminObject' => $adminObject], 'AdminGoodsClassFacadeService', 'handleError');
            throw new CommonException('EsUpdateGoodsClassError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'UpdateGoodsClass');


        $result = code(['code' => 0,'msg' => '更新产品分类成功!']);

        return $result;
    }

    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function moveGoodsClass(MoveGoodsClassDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MoveClassError'));

        $validated = $requestDTO->toArray();

        $goodsClassObject = GoodsClass::find($validated['id']);

        if (!$goodsClassObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $goodsClassRevision = $goodsClassObject->revision;

        $oldDeep = $goodsClassObject ->deep;

        //元素自己本身的深度
        $parentDeep = 1;

        //如果有父级 就用父级元素
        if ($validated['parent_id']) {
            $parentClass = GoodsClass::find($validated['parent_id']);

            $parentDeep = $parentClass->deep + 1;
        }

        if (self::$dropType[$validated['dropType']] == 10) {
            $goodsClassUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'deep' =>  $parentDeep,
                'revision' => $goodsClassRevision + 1
            ];
        }

        if (self::$dropType[$validated['dropType']] == 20) {
            $goodsClassUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'sort' => $validated['sort'],
                'deep' => $parentDeep,
                'revision' => $goodsClassRevision + 1
            ];
        }

        if (self::$dropType[$validated['dropType']] == 30) {
            $goodsClassUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'sort' => $validated['sort'],
                'deep' => $parentDeep,
                'revision' => $goodsClassRevision + 1
            ];
        }

        $goodsClassWhere = [['id', '=', $validated['id']], ['revision', '=', $goodsClassRevision]];

        //更新配置项
        $goodsClassResult = GoodsClass::where($goodsClassWhere)->update($goodsClassUpdate);

        if (!$goodsClassResult) {
            throw new CommonException('MoveGoodsClassError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'MoveGoodsClass');

        //修改子级deep
        $deepNumber = $parentDeep - $oldDeep;

        $updateDeepResult = $this->updateChildrenDeep($goodsClassObject->id, $deepNumber);

        $esIndexName = config('common_es.indices.business.goods_classes');

        $queryArray = [
            'match_all' => new \stdClass()
        ];

        $deleteEsResult = EsFacade::deleteByQuery($esIndexName, $queryArray);

        if (!isset($deleteEsResult['code']) || $deleteEsResult['code'] != 0) {
            plog(['error' => 'EsMoveRoleJobError','$deleteEsResult' => $deleteEsResult,'$goodsClassObject' => $goodsClassObject,'$adminObject' => $adminObject], 'AdminGoodsClassFacadeService', 'handleError');
            throw new CommonException('EsMoveGoodsClassError');
        }

        \App\Facades\LaravelFastApi\V1\Es\Sync\Business\EsSyncBusinessFacade::syncGoodsClass();

        $result = code(['code' => 0,'msg' => '移动产品分类成功!']);

        return $result;
    }

    /**
     * 删除地区
     *
     * @param [type] $id
     * @param [type] $userObject
     * @return void
     */
    public function deleteGoodsClass(DeleteGoodsClassDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteGoodsClassError'));

        $validated = $requestDTO->toArray();

        $id = $validated['id'];
        //查看是否有子类
        $goos_class = GoodsClass::where('parent_id', $id)->get();

        $count = $goos_class->count();

        if ($count) {
            throw new CommonException('DeleteHasChildrenError');
        }

        $goodsClassObject = GoodsClass::find($id);

        if (!$goodsClassObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $contcatParamArray = ['goods_class_id' => $id];

        app(DeleteGoodsClassHandleContract::class)->handle($contcatParamArray);

        $goodsClassObject->deleted_at = date('Y-m-d H:i:s', time());

        $delClassResult =  $goodsClassObject->save();

        if (!$delClassResult) {
            throw new CommonException('DeleteGoodsClassError');
        }

        $esIndexName = config('common_es.indices.business.goods_classes');

        $updateDataArray = [

            'deleted_at' => date('Y-m-d H:i:s', time()),
        ];

        $esResult = EsFacade::updateDoc($esIndexName, $goodsClassObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es删除商品分类失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$goodsClassObject' => $goodsClassObject,'$adminObject' => $adminObject], 'AdminGoodsClassFacadeService', 'handleError');
            throw new CommonException('EsDeleteGoodsClassError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'DeleteGoodsClass');

        $result = code(['code' => 0,'msg' => '删除分类成功!']);

        return $result;
    }

    /**
     * 获取单个商品分类信息
     *
     * @param array $validated 验证后的请求参数
     * @param object $adminObject 管理员信息
     * @return array 返回处理结果
     *              成功返回 ['code'=>0, 'msg'=>'获取分类成功', 'data'=>商品分类对象]
     *              失败返回 配置中的错误码
     * @throws CommonException 当商品分类不存在时抛出异常
     */
    public function getSingleGoodsClass(GetSingleGoodsClassDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetSingleGoodsClassError'));

        $validated = $requestDTO->toArray();

        $id = $validated['goods_class_id'];

        $esIndexName = config('common_es.indices.business.goods_classes');

        $esGooodsClassObject = EsQueryFacade::index($esIndexName)->whereNull('deleted_at')->where('id', $id)->get()->first();

        if (!$esGooodsClassObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $result = code(['code' => 0,'msg' => '获取单条分类信息成功'],['data' => new EsGoodsClassResource($esGooodsClassObject)]);

        return $result;
    }
}
