<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-09-06 20:16:53
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-08-31 18:08:06
 */

namespace App\Service\Facade\Traits\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

Trait AlwaysService
{
    // 移动类型
    protected static $dropType = ['inner' => 10, 'before' => 20, 'after' => 30];

    /**
     * 定义的静态模型
     *
     * @var Model
     */
    protected $model; 

    /**
     * 定义的静态字段
     */
    protected $field;

    /**
     * 返回结果要显示的字段
     *
     * @var array|string
     */
    protected $selectField;

    // 排序字段
    protected $sortField;

    // 排序方式
    protected $sortOrder;

    // 所有不同等级数据容器（存储集合）
    protected $treeData = [];

	// 提前查询所有数据（可在 init 时缓存）
	protected $allNodes;


    /**
     * 初始化 用来在继承的子类构造函数中执行
     *
     * @param Model $Model
     * @param string $field
     * @return void
     */
    public function init(Model $Model, $field = 'deep', $selectField = '*', $sortField = 'id', $sortOrder = 'asc')
    {
        $this->model = $Model;
        $this->field = $field;
        $this->selectField = $selectField;
        $this->sortField = $sortField;
        $this->sortOrder = $sortOrder;
    }

    /**
     * 获取所有的等级
     *
     * @param array $with 关联关系
     * @return array
     */
    private function getAllTree($with = [])
    {
        // 查询所有的等级（保留模型对象，但只提取层级字段）
        $query = $this->model::select($this->field)->orderBy($this->sortField, $this->sortOrder);
        if (!empty($with)) {
            $query->with($with);
        }
        $allTreeCollection = $query->get();

        // 提取层级字段值并去重排序
        $allTree = $allTreeCollection->pluck($this->field)->unique()->sort()->values()->all();

        return $allTree;
    }


    /**
     * 递归查找子级（返回模型集合）
     *
     * @param array $idArray 父级ID数组
     * @param array $idData 子级ID容器
     * @param Collection $data 子级数据容器（集合）
     * @return array ['idData' => 子级ID数组, 'data' => 子级模型集合]
     */
    public function getRecursionChildren($idArray = [], &$idData = [], &$data = null)
    {
		//先提前查出所有数据
		$this->getAllNodesData();

        foreach ($this->allNodes as $node) {
			if (in_array($node->parent_id, $idArray)) {  // 这里将数组访问改为对象访问
				$idData[] = $node->id;  // 这里将数组访问改为对象访问
				$data->push($node);  // 使用集合的push方法添加元素
				$this->getRecursionChildren([$node->id], $idData, $data);  // 这里将数组访问改为对象访问
			}
		}
		return ['idData' => $idData, 'data' => $data];
    }

	//提前查询所有数据,降低对数据库查询压力
	protected final function getAllNodesData()
	{
		// 关键修复：去掉toArray()，保持模型对象集合而不是转换为数组
		$this->allNodes = $this->model::select($this->selectField)->get()->keyBy('id');
	}

    /**
     * 获取等级树形数据（返回模型集合）
     *
     * @param array $with 关联关系
     * @return Collection
     */
    public function getTreeData($with = [])
    {
        $tree = $this->getAllTree();
        $data = new Collection();

        if (!empty($tree)) 
		{
            // 将不同等级数据分别装入容器（存储集合）
            for ($i = 0; $i < count($tree); $i++) {
                $query = $this->model::select($this->selectField)
                    ->where($this->field, $tree[$i])
                    ->orderBy($this->sortField, $this->sortOrder)
                    ->orderBy('sort', 'desc');

                if (!empty($with)) {
                    $query->with($with);
                }

                $this->treeData[$i] = $query->get(); // 存储集合而非数组
            }

            // 从倒数第二级开始绑定子节点
            for ($i = count($tree); $i > 1; $i--) {
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
        return $this->model::select($this->selectField)->get();
    }

    /**
     * 根据ID查找所有子级数据（非树形，用于批量修改）
     *
     * @param int $id 父级ID
     * @return array ['idData' => 子级ID数组, 'data' => 子级模型集合]
     */
    public function getAllChildren($id)
    {
        $idArray = [$id];
        $idData = [];
        $data = new Collection(); // 确保初始化为空集合

        return $this->getRecursionChildren($idArray, $idData, $data);
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
                // 现在$child是模型对象，可以安全地使用对象属性访问
                $child->{$this->field} += $deepNumber;
                $updateResult = $child->save();

                if (!$updateResult) {
                    $result = 0;
                }
            }
        }

        return $result;
    }
}
