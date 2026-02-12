<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-03-11 10:53:49
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-10 19:57:30
 */

namespace App\Services\Facade\Traits\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

Trait QueryService
{

    //where 查询条件
    public $where = [];
    //orWhere查询条件
    public $orWhere = [];

    //范围查询条件
    public $whereBetween = [];

    //关联查询条件
    public $withWhere = [];
    //关联查询判断条件
    public $orWithWhere = [];

    //分页
    public $perPage=10;
    public $page=1;
    public $pageName = 'page';

    //查询内容
    public $columns = ['*'];

    /**
     * 处理分页选项
     *
     * @param [type] $validated
     * @return void
     */
    protected function setQueryOptions($validated)
    {
        $this->setPaginate($validated);
    }

    /**
     * 设置分页
     *
     * @param [type] $validated
     * @return void
     */
    protected function setPaginate($validated)
    {
       if(isset($validated['pageSize']) && !empty($validated['pageSize']))
       {
            $this->perPage = $validated['pageSize'];
       }

       if(isset($validated['currentPage']) && !empty($validated['currentPage']))
       {
            $this->page = $validated['currentPage'];
       }
    }

    /**
     * 处理 orWhere选项
     *
     * @param [type] $query
     * @param [type] $orWhere
     *  $orWhere[] = ['id',2];
        $orWhere[] = ['id',3];
     * @return void
     */
    protected function setOrWhere($query,$orWhere)
    {
        // 仅处理有效的非空数组
    if (is_array($orWhere) && !empty($orWhere)) {
        foreach ($orWhere as $condition) {
            // 跳过无效条件（非数组或元素数量不足）
            if (!is_array($condition) || count($condition) < 2) {
                continue;
            }

            // 解析字段、操作符、值
            $field = $condition[0]; // 字段名（必选）
            $value = $condition[count($condition) - 1]; // 值（必选，始终取最后一个元素）
            
            // 添加orWhere条件
            $query->orWhere($field,$value);
        }
    }

        return $query;
    }

	/**
	 * 检测是值是否是唯一的
	 */
	protected function checkIsUnique(Model $model,$property_name,$property_value,$id)
	{
		$checkResult = true;

		$checObject = $model::where($property_name,$property_value)->first();

		if($checObject)
		{
			if($checObject->id !== $id)
			{
				$checkResult = false;
			}
		}

		return $checkResult;

	}

}
