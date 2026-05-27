<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-26 10:46:25
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-09 14:05:16
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminLabelFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Service\Group;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Services\Facade\Traits\V1\Es\EsAlwaysService;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Label\AddLabelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Label\UpdateLabelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Label\MoveLabelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Label\DeleteLabelDTO;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Module\Label;
use App\Models\LaravelFastApi\V1\Article\Union\ArticleLabelUnion;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Help\EsLabelRecource;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group\LabelController
 * @see \App\Facades\Admin\Service\Group\AdminLabelFacade
 */
class AdminLabelFacadeService
{
    use EsAlwaysService;
    public function test()
    {
        echo "AdminLabelFacadeService test";
    }

    /**
    * Label constructor.
    */
    public function __construct()
    {
        $esIndexName = config('common_es.indices.business.labels');
        $this->init((new Label()), $esIndexName, 'deep');
    }

    /**
     * 结合redis获取所有树形地区
     *
     * @return void
     */
    public function getTreeLabel()
    {
        $result = code(config('admin_code.GetLabelError'));

        $treeLabel = $this->getTreeData();

        $data['data'] = EsLabelRecource::collection($treeLabel);

        $result = code(['code' => 0,'msg' => '获取树形标签成功!'], $data);

        return  $result;
    }

    /**
     *  添加地区
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function addLabel(AddLabelDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddLabelError'));

        $validated = $requestDTO->toArray();

        $labelObject = new Label();

        foreach ($validated as $key => $value) {
            if (\is_null($value)) {
                $value = "";
            }

            $labelObject->$key = $value;
        }

        $labelObject->created_time = time();
        $labelObject->created_at = time();
        $labelObject->switch = 1;

        $labelResult = $labelObject->save();

        if (!$labelResult) {
            throw new CommonException('AddLabelError');
        }

        $esIndexName = config('common_es.indices.business.labels');

        $insertDataArray = [
            '_docId' => $labelObject->id,
            'id' => $labelObject->id,
            'parent_id' => $labelObject->parent_id,
            'deep' => $labelObject->deep,
            'switch' => $labelObject->switch,
            'label_name' => $labelObject->label_name,
            'label_code' => $labelObject->label_code,
            'label_picture_uid' => $labelObject->label_picture_uid,
            'note' => $labelObject->note,
            'sort' => $labelObject->sort,
            'created_time' => $labelObject->created_time,
            'updated_time' => $labelObject->updated_time,
            'created_at' => $labelObject->created_at,
            'updated_at' => $labelObject->updated_at,
            'deleted_at' => $labelObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($esIndexName, $insertDataArray, $labelObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加标签分类失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$labelObject' => $labelObject,'$adminObject' => $adminObject], 'AdminLabelFacadeService', 'handleError');
            throw new CommonException('EsAddLabelError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'AddLabel');

        $result = code(['code' => 0,'msg' => '添加标签成功!']);

        return $result;
    }

    /**
     * 更新地区
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function updateLabel(UpdateLabelDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateLabelError'));

        $validated = $requestDTO->toArray();

        $labelObject = Label::find($validated['id']);

        if (!$labelObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$labelObject->revision];

        foreach ($validated as $key => $value) {
            if ($key === 'id') {
                $where[] = ['id','=',$value];
                continue;
            }

            if (\is_null($value)) {
                $value = "";
            }

            $updateDataArray[$key] = $value;
        }

        $updateDataArray['revision'] = $labelObject ->revision + 1;

        $updateDataArray['updated_time'] = time();

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        $labelResult = Label::where($where)->update($updateDataArray);

        if (!$labelResult) {
            throw new CommonException('UpdateLabelError');
        }

        $labelObject = $labelObject->fresh();

        $esIndexName = config('common_es.indices.business.labels');

        $updateDataArray = [
            '_docId' => $labelObject->id,
            'id' => $labelObject->id,
            'parent_id' => $labelObject->parent_id,
            'deep' => $labelObject->deep,
            'switch' => $labelObject->switch,
            'label_name' => $labelObject->label_name,
            'label_code' => $labelObject->label_code,
            'label_picture_uid' => $labelObject->label_picture_uid,
            'note' => $labelObject->note,
            'sort' => $labelObject->sort,
            'created_time' => $labelObject->created_time,
            'updated_time' => $labelObject->updated_time,
            'created_at' => $labelObject->created_at,
            'updated_at' => $labelObject->updated_at,
            'deleted_at' => $labelObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($esIndexName, $labelObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新标签分类失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$labelObject' => $labelObject,'$adminObject' => $adminObject], 'AdminLabelFacadeService', 'handleError');
            throw new CommonException('EsUpdateLabelError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'UpdateLabel');

        $result = code(['code' => 0,'msg' => '更新标签成功!']);

        return $result;
    }

    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function moveLabel(MoveLabelDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MoveLabelError'));

        $validated = $requestDTO->toArray();

        $labelObject = Label::find($validated['id']);

        if (!$labelObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $labelRevision = $labelObject->revision;

        $oldDeep = $labelObject->deep;

        $parentDeep = 1;

        if ($validated['parent_id']) {
            $parentLabel = Label::find($validated['parent_id']);

            $parentDeep = $parentLabel->deep + 1;
        }

        if (self::$dropType[$validated['dropType']] == 10) {
            $labelUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'deep' =>  $parentDeep,
                'revision' => $labelRevision + 1
            ];
        }

        if (self::$dropType[$validated['dropType']] == 20) {
            $labelUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'sort' => $validated['sort'],
                'deep' => $parentDeep,
                'revision' => $labelRevision + 1
            ];
        }

        if (self::$dropType[$validated['dropType']] == 30) {
            $labelUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'sort' => $validated['sort'],
                'deep' => $parentDeep,
                'revision' => $labelRevision + 1
            ];
        }

        $labelWhere = [['id', '=', $validated['id']], ['revision', '=', $labelRevision]];

        //更新配置项
        $labelResult = Label::where($labelWhere)->update($labelUpdate);

        if (!$labelResult) {
            throw new CommonException('MoveLabelError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'MoveLabel');

        //修改子级deep

        //分析 如果现在深度- 以前深度 =0 说敏级别没变  >0 级别变小  <0 级别变大
        $deepNumber = $parentDeep - $oldDeep;

        $updateDeepResult = $this->updateChildrenDeep($labelObject->id, $deepNumber);

        $esIndexName = config('common_es.indices.business.labels');

        $queryArray = [
            'match_all' => new \stdClass()
        ];

        $deleteEsResult = EsFacade::deleteByQuery($esIndexName, $queryArray);

        if (!isset($deleteEsResult['code']) || $deleteEsResult['code'] != 0) {
            plog(['error' => 'EsMoveLabelJobError','$deleteEsResult' => $deleteEsResult,'$labelObject' => $labelObject,'$adminObject' => $adminObject], 'AdminLabelFacadeService', 'handleError');
            throw new CommonException('EsMoveLabelError');
        }

        \App\Facades\LaravelFastApi\V1\Es\Sync\Business\EsSyncBusinessFacade::syncLabel();

        $result = code(['code' => 0,'msg' => '移动标签成功!']);

        return $result;
    }

    /**
     * 删除地区
     *
     * @param [type] $id
     * @param [type] $userObject
     * @return void
     */
    public function deleteLabel(DeleteLabelDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteLabelError'));

        $validated = $requestDTO->toArray();

        $id = $validated['id'];
        //查看是否有子类
        $labelObject = Label::where('parent_id', $id)->get();

        $count = $labelObject->count();

        if ($count) {
            throw new CommonException('DeleteNoLabelError');
        }

        $articleLabelUnionCount = $this->checkLabelHasArticle($id);

        if ($articleLabelUnionCount) {
            throw new CommonException('DeleteNoArticleLabelError');
        }

        $goodsLabelUnionCount = $this->checkLabelHasGoods($id);

        if ($goodsLabelUnionCount) {
            throw new CommonException('DeleteNoGoodsLabelError');
        }

        $delLabelObject = Label::find($id);

        $delLabelObject->deleted_at = date('Y-m-d H:i:s');

        $delLabelResult =  $delLabelObject->save();

        if (!$delLabelResult) {
            //DB::rollBack();
            throw new CommonException('DeleteLabelError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'DeleteLabel');

        $esIndexName = config('common_es.indices.business.labels');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s'),
        ];

        $esResult = EsFacade::updateDoc($esIndexName, $delLabelObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es删除标签分类失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$delLabelObject' => $delLabelObject,'$adminObject' => $adminObject], 'AdminLabelFacadeService', 'handleError');
            throw new CommonException('EsDeleteLabelError');
        }

        $result = code(['code' => 0,'msg' => '删除标签成功!']);

        return $result;
    }

    /**
     * 检测标签是否有文章
     */
    protected function checkLabelHasArticle(int $label_id): bool
    {
        $result = false;
        $indexName = config('common_es.indices.union.article_label_unions');

        $count = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('label_id',$label_id)->get()->count();

        if($count){
            $result = true;
        }

        return $result;
    }

    /**
     * 检测标签是否有商品
     */
    protected function checkLabelHasGoods(int $label_id): bool
    {
        $result = false;
        $indexName = config('common_es.indices.union.goods_label_unions');

        $count = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('label_id',$label_id)->get()->count();

        if($count){
            $result = true;
        }

        return $result;
    }
}
