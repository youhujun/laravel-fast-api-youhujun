<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-09-06 20:16:53
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-16 05:09:14
 */

namespace App\Services\Facade\Traits\V1\Es;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Exceptions\Common\CommonException;

trait EsAlwaysService
{
    // 移动类型
    protected static $dropType = ['inner' => 10, 'before' => 20, 'after' => 30];

    /**
     * 定义的静态模型
     *
     * @var Model
     */
    protected $model;

    //es索引名称
    protected $esIndexName;

    /**
     * 定义的静态字段
     */
    protected $field;

    // 排序字段
    protected $sortField;

    // 排序方式
    protected $sortOrder;

    // 所有不同等级数据容器（存储集合）
    protected $treeDataArray = [];

    // 提前查询所有数据（可在 init 时缓存）
    protected $allNodes;


    /**
     * 初始化 用来在继承的子类构造函数中执行
     *
     * @param Model $Model
     * @param string $field
     * @return void
     */
    public function init(Model $Model, $esIndexName, $field = 'deep', $sortField = 'id', $sortOrder = 'asc')
    {
        $this->model = $Model;

        if (!$esIndexName) {
            throw new CommonException('EsIndexNameEmptyError');
        }
        $this->esIndexName = $esIndexName;
        $this->field = $field;
        $this->sortField = $sortField;
        $this->sortOrder = $sortOrder;
    }

    /**
     * 获取所有的等级
     *
     * @param array $with 关联关系
     * @return array
     */
    private function getAllTree(): array
    {
        $esQuery = EsQueryFacade::index($this->esIndexName);

        $esQuery->whereNull('deleted_at');

        $esQuery->orderBy($this->sortField, $this->sortOrder);

        $max_size = config('common_es.max_result_window');

        $allTreeCollection = $esQuery->limit($max_size)->get();

        // 提取层级字段值并去重排序
        $allTreeArray = $allTreeCollection->pluck($this->field)->unique()->sort()->values()->all();

        /**
         * Array(    [0] => 1    [1] => 2    [2] => 3)
         */

        return $allTreeArray;
    }


    /**
     * 递归查找子级（返回模型集合）
     *
     * @param array $idArray 父级ID数组
     * @param array $idData 子级ID容器
     * @param Collection $data 子级数据容器（集合）
     * @return array ['idData' => 子级ID数组, 'data' => 子级模型集合]
     */
    public function getRecursionChildren($idArray = [], &$idDataArray = [], &$data = null): array
    {
        //先提前查出所有数据
        $this->getAllNodesData();

        foreach ($this->allNodes as $node) {
            if (in_array($node->parent_id, $idArray)) {  // 这里将数组访问改为对象访问
                $idDataArray[] = $node->id;  // 这里将数组访问改为对象访问
                $data->push($node);  // 使用集合的push方法添加元素
                $this->getRecursionChildren([$node->id], $idDataArray, $data);  // 这里将数组访问改为对象访问
            }
        }
        return ['idDataArray' => $idDataArray, 'data' => $data];
    }

    //提前查询所有数据,降低对数据库查询压力
    final protected function getAllNodesData()
    {
        $esQuery = EsQueryFacade::index($this->esIndexName);

        $esQuery->whereNull('deleted_at');

        $esQuery->orderBy($this->sortField, $this->sortOrder);

        $max_size = config('common_es.max_result_window');

        $this->allNodes = $esQuery->limit($max_size)->get()->keyBy($this->sortField);
    }

    /**
     * 获取等级树形数据（返回模型集合）
     *
     * @param array $with 关联关系
     * @return Collection
     */
    public function getTreeData()
    {
        $treeArray = $this->getAllTree();
        $data = new Collection();

        $max_size = config('common_es.max_result_window');

        if (!empty($treeArray)) {
            // 将不同等级数据分别装入容器（存储集合）
            for ($i = 0; $i < count($treeArray); $i++) {
                $esQuery = EsQueryFacade::index($this->esIndexName);

                $esQuery->whereNull('deleted_at');
                $esQuery->where($this->field, $treeArray[$i]);
                $esQuery->orderBy($this->sortField, $this->sortOrder);

                $collection = $esQuery->limit($max_size)->get();

                $this->treeData[$i] = $collection->sortBy($this->sortField)->values();
            }

            // 从倒数第二级开始绑定子节点
            for ($i = count($treeArray); $i > 1; $i--) {
                // 遍历上一级节点（集合）
                $this->treeData[$i - 2]->each(function ($parent) use ($i) {
                    // 初始化children属性为集合
                    $parent->children = new Collection();
                    // 遍历下一级节点，匹配父ID
                    $this->treeData[$i - 1]->each(function ($child) use ($parent) {
                        if ($child->parent_id == $parent->id) {
                            $parent->children->push($child);
                        }
                    });

                    $parent->children = $parent->children->sortBy($this->sortField)->values();
                });
            }

            $data = $this->treeData[0]; // 根节点集合
        }

        return $data;
    }

    /**
     * 获取所有数据（返回模型集合）
     *
     * @return Collection
     */
    public function getAllData()
    {
        $esQuery = EsQueryFacade::index($this->esIndexName);

        $esQuery->whereNull('deleted_at');

        $esQuery->orderBy($this->sortField, $this->sortOrder);

        $max_size = config('common_es.max_result_window');

        return $esQuery->limit($max_size)->get();
    }

    /**
     * 根据ID查找所有子级数据（非树形，用于批量修改）
     *
     * @param int $id 父级ID
     * @return array ['idData' => 子级ID数组, 'data' => 子级模型集合]
     */
    public function getAllChildren($id): array
    {
        $idArray = [$id];
        $idDataArray = [];
        $data = new Collection(); // 确保初始化为空集合

        return $this->getRecursionChildren($idArray, $idDataArray, $data);
    }

    /**
     * 更新子节点的层级深度
     *
     * @param int $parent_id 父级ID
     * @param int $deepNumber 深度调整值
     * @return int 1=成功，0=失败
     */
    public function updateChildrenDeep($parent_id, $deepNumber = 0)
    {
        $result = 1;
        $allChildren = $this->getAllChildren($parent_id);

        if ($allChildren['data']->isNotEmpty()) {
            foreach ($allChildren['data'] as $child) {
                // 实例化模型
                $model = new $this->model();

                // 填充数据
                $model->fill($child->toArray());

                // 修改层级深度
                $model->{$this->field} += $deepNumber;

                // 按ID执行更新
                $updateResult = $model->where('id', $child->id)->update([
                    $this->field => $model->{$this->field}
                ]);

                if (!$updateResult) {
                    $result = 0;
                }
            }
        }

        return $result;
    }
}
