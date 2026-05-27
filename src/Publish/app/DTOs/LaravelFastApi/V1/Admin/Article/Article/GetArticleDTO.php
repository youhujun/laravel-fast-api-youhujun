<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-04-08 01:24:18
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 22:34:15
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Admin\Article\Article\GetArticleDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\DTOs\LaravelFastApi\V1\Admin\Article\Article;

use App\DTOs\Traits\BaseDTOTrait;
use Illuminate\Support\Facades\Validator; 
use App\Exceptions\Common\RuleException; 
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\CheckArray;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Article\ArticleController
 */
class GetArticleDTO
{
    use BaseDTOTrait;
    
    // 字段定义（赋默认值）
    public string $find = '';
    public array $timeRangePublish = [];
    public array $timeRangeCreate = [];
    public array $category_cascader_id_array = [];
    public array $label_cascader_id_array = [];
    public int|string $is_top = 0;
    public int|string $status = 0;
    public int|string $sortType = 0;
    public int|string $currentPage = 1;
    public int|string $pageSize = 10;

    // ========== 核心约定：字段映射（必实现） ==========
    /**
     * 字段映射
     * @return array
     */
    public function getFieldMap(): array
    {
        return ['find', 'timeRangePublish', 'timeRangeCreate', 'category_cascader_id_array', 'label_cascader_id_array', 'is_top', 'status', 'sortType', 'currentPage', 'pageSize'];
    }

    // ========== 按需扩展：校验规则 ==========
    /**
     * 校验规则
     * @return array
     */
    public function rules(): array
    {
        return [
            'find' => ['bail', 'nullable', new CheckString()],
            'timeRangePublish' => ['bail', 'nullable', new CheckArray()],
            'timeRangeCreate' => ['bail', 'nullable', new CheckArray()],
            'category_cascader_id_array' => ['bail', 'nullable', new CheckArray()],
            'label_cascader_id_array' => ['bail', 'nullable', new CheckArray()],
            'is_top' => ['bail', 'nullable', new Numeric()],
            'status' => ['bail', 'nullable', new Numeric()],
            'sortType' => ['bail', 'nullable', new Numeric()],
            'currentPage' => ['bail', 'nullable', new Numeric()],
            'pageSize' => ['bail', 'nullable', new Numeric()],
        ];
    }

    /**
     * 核心：执行校验并抛出异常
     * @param array $data 待校验的数据
     * @return self 校验通过返回DTO实例
     * @throws RuleException 校验不通过直接抛异常
     */
    public function validate(array $data): self
    {
        $validator = Validator::make($data, $this->rules(), []);
        $validated = $validator->validated();

        $this->fill($validated);
        $this->formatFields();

        return $this;
    }

    /**
     * 字段格式化
     * @return self
     */
    public function formatFields(): self
    {
        foreach ($this->getFieldMap() as $field) {
            $this->$field = f($this->$field);
        }
        return $this;
    }
}
