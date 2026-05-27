<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-26 10:46:02
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-08 21:54:44
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminCategoryFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Service\Group;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Services\Facade\Traits\V1\Es\EsAlwaysService;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Category\AddCategoryDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Category\UpdateCategoryDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Category\MoveCategoryDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Category\DeleteCategoryDTO;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Module\Article\Category;
use App\Models\LaravelFastApi\V1\Article\Union\ArticleCategoryUnion;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Article\EsCategoryResource;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group\CategoryController
 * @see \App\Facades\Admin\Service\Group\AdminCategoryFacade
 */
class AdminCategoryFacadeService
{
    use EsAlwaysService;
    public function test()
    {
        echo "AdminCategoryFacadeService test";
    }

    /**
    * Category constructor.
    */
    public function __construct()
    {
        $esIndexName = config('common_es.indices.business.article_categories');
        $this->init((new Category()), $esIndexName, 'deep');
    }

    /**
     * 结合redis获取所有树形地区
     *
     * @return void
     */
    public function getTreeCategory()
    {
        $result = code(config('admin_code.GetCategoryError'));

        $treeCategory = $this->getTreeData();

        $data['data'] = EsCategoryResource::collection($treeCategory);

        $result = code(['code' => 0,'msg' => '获取文章分类成功!'], $data);

        return  $result;
    }

    /**
     *  添加地区
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function addCategory(AddCategoryDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddCategoryError'));

        $validated = $requestDTO->toArray();

        $categoryObject = new Category();

        foreach ($validated as $key => $value) {
            if (isset($value)) {
                $categoryObject->$key = $value;
            }
        }

        $categoryObject->switch = 1;
        $categoryObject->created_time = time();
        $categoryObject->created_at = time();

        $categoryResult = $categoryObject->save();

        if (!$categoryResult) {
            throw new CommonException('AddCategoryError');
        }

        $esIndexName = config('common_es.indices.business.article_categories');

        $insertDataArray = [
            '_docId' => $categoryObject->id,
            'id' => $categoryObject->id,
            'parent_id' => $categoryObject->parent_id,
            'deep' => $categoryObject->deep,
            'switch' => $categoryObject->switch,
            'rate' => $categoryObject->rate,
            'category_name' => $categoryObject->category_name,
            'category_code' => $categoryObject->category_code,
            'category_picture_uid' => $categoryObject->category_picture_uid,
            'note' => $categoryObject->note,
            'sort' => $categoryObject->sort,
            'created_time' => $categoryObject->created_time,
            'updated_time' => $categoryObject->updated_time,
            'created_at' => $categoryObject->created_at,
            'updated_at' => $categoryObject->updated_at,
            'deleted_at' => $categoryObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($esIndexName, $insertDataArray, $categoryObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加文章分类失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$categoryObject' => $categoryObject,'$adminObject' => $adminObject], 'AdminCategoryFacadeService', 'handleError');
            throw new CommonException('EsAddCategoryError');
        }


        CommonEvent::dispatch($adminObject, $validated, 'AddCategory');

        $result = code(['code' => 0,'msg' => '添加文章分类成功!']);

        return $result;
    }

    /**
     * 更新地区
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function updateCategory(UpdateCategoryDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateCategoryError'));

        $validated = $requestDTO->toArray();

        $categoryObject = Category::find($validated['id']);

        if (!$categoryObject) {
            throw new CommonException('ThisDataHasChildrenError');
        }

        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$categoryObject ->revision];

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

        $updateDataArray['revision'] = $categoryObject ->revision + 1;

        $updateDataArray['updated_time'] = time();

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        $categoryResult = Category::where($where)->update($updateDataArray);

        if (!$categoryResult) {
            throw new CommonException('UpdateCategoryError');
        }

        $categoryObject = $categoryObject->fresh();

        $esIndexName = config('common_es.indices.business.article_categories');

        $updateDataArray = [
            '_docId' => $categoryObject->id,
            'id' => $categoryObject->id,
            'parent_id' => $categoryObject->parent_id,
            'deep' => $categoryObject->deep,
            'switch' => $categoryObject->switch,
            'rate' => $categoryObject->rate,
            'category_name' => $categoryObject->category_name,
            'category_code' => $categoryObject->category_code,
            'category_picture_uid' => $categoryObject->category_picture_uid,
            'note' => $categoryObject->note,
            'sort' => $categoryObject->sort,
            'created_time' => $categoryObject->created_time,
            'updated_time' => $categoryObject->updated_time,
            'created_at' => $categoryObject->created_at,
            'updated_at' => $categoryObject->updated_at,
            'deleted_at' => $categoryObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($esIndexName, $categoryObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新文章分类失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$categoryObject' => $categoryObject,'$adminObject' => $adminObject], 'AdminCategoryFacadeService', 'handleError');

            throw new CommonException('EsUpdateCategoryError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'UpdateCategory');

        $result = code(['code' => 0,'msg' => '修改文章分类成功!']);

        return $result;
    }

    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function moveCategory(MoveCategoryDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MoveCategoryError'));

        $validated = $requestDTO->toArray();

        $categoryObject = Category::find($validated['id']);

        $categoryRevision = $categoryObject->revision;

        $oldDeep = $categoryObject->deep;

        $parentDeep = 1;

        if ($validated['parent_id']) {
            $parentCategoryObject = Category::find($validated['parent_id']);

            $parentDeep = $parentCategoryObject->deep + 1;
        }

        if (self::$dropType[$validated['dropType']] == 10) {
            $categoryUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'deep' =>  $parentDeep,
                'revision' => $categoryRevision + 1
            ];
        }

        if (self::$dropType[$validated['dropType']] == 20) {
            $categoryUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'sort' => $validated['sort'],
                'deep' => $parentDeep,
                'revision' => $categoryRevision + 1
            ];
        }

        if (self::$dropType[$validated['dropType']] == 30) {
            $categoryUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'sort' => $validated['sort'],
                'deep' => $parentDeep,
                'revision' => $categoryRevision + 1
            ];
        }

        $categoryWhere = [['id', '=', $validated['id']], ['revision', '=', $categoryRevision]];

        //更新配置项
        $categoryResult = Category::where($categoryWhere)->update($categoryUpdate);

        if (!$categoryResult) {
            throw new CommonException('MoveCategoryError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'MoveCategory');

        //修改子级deep
        $deepNumber = $parentDeep - $oldDeep;

        $updateDeepResult = $this->updateChildrenDeep($categoryObject->id, $deepNumber);

        $esIndexName = config('common_es.indices.business.article_categories');

        $queryArray = [
            'match_all' => new \stdClass()
        ];

        $deleteEsResult = EsFacade::deleteByQuery($esIndexName, $queryArray);

        if (!isset($deleteEsResult['code']) || $deleteEsResult['code'] != 0) {
            plog(['error' => 'EsMoveCategoryJobError','$deleteEsResult' => $deleteEsResult,'$categoryObject' => $categoryObject,'$adminObject' => $adminObject], 'AdminCategoryFacadeService', 'handleError');

            throw new CommonException('EsMoveCategoryError');
        }

        \App\Facades\LaravelFastApi\V1\Es\Sync\Business\EsSyncBusinessFacade::syncCategory();

        $result = code(['code' => 0,'msg' => '移动文章分类成功!']);

        return $result;
    }

    /**
     * 删除地区
     *
     * @param [type] $id
     * @param [type] $userObject
     * @return void
     */
    public function deleteCategory(DeleteCategoryDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteCategoryError'));

        $validated = $requestDTO->toArray();
        //查看是否有子类
        $id = $validated['id'];

        $categoryObject = Category::where('parent_id', $id)->get();

        $count = $categoryObject->count();

        //有子类不能删除
        if ($count) {
            throw new CommonException('DeleteNoCategoryError');
        }

        $deleteCategoryObject = Category::find($id);

        if (!$deleteCategoryObject) {
            throw new CommonException('ThisDataHasChildrenError');
        }


        $articleCategoryUnionCount = $this->checkCategoryHasArticle($id);

        if ($articleCategoryUnionCount) {
            throw new CommonException('DeleteNoArticleCategoryError');
        }

        $deleteCategoryObject->deleted_at = date('Y-m-d H:i:s');

        $delCategoryResult =  $deleteCategoryObject->save();

        if (!$delCategoryResult) {
            throw new CommonException('DeleteCategoryError');
        }

        $esIndexName = config('common_es.indices.business.article_categories');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $esResult = EsFacade::updateDoc($esIndexName, $deleteCategoryObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es删除文章分类失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$categoryObject' => $categoryObject,'$adminObject' => $adminObject], 'AdminCategoryFacadeService', 'handleError');
            throw new CommonException('EsDeleteCategoryError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'DeleteCategory');

        $result = code(['code' => 0,'msg' => '删除文章分类成功!']);

        return $result;
    }
    /**
     * 检测分类是否有文章
     */
    protected function checkCategoryHasArticle(int $category_id): bool
    {
        $result = false;
        $indexName = config('common_es.indices.union.article_category_unions');

        $esCount = EsQueryFacade::index($indexName)->where('category_id', $category_id)->get()->count();

        if($esCount > 0){
            $result = true;
        }

        return $result;
    }
}
