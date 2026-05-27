<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-04-08 01:24:18
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 20:56:57
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Admin\Article\Article\AddArticleDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\DTOs\LaravelFastApi\V1\Admin\Article\Article;

use App\DTOs\Traits\BaseDTOTrait;
use Illuminate\Support\Facades\Validator; 
use App\Exceptions\Common\RuleException; 
use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckArray;
use App\Rules\Pub\FormatTime;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Article\ArticleController
 */
class AddArticleDTO
{
    use BaseDTOTrait;
    
    // 字段定义（赋默认值）
    public string $type = '';
    public string $title = '';
    public array $category_cascader_id_array = [];
    public array $label_cascader_id_array = [];
    public int|string $is_top = 0;
    public string $content = '';
    public string $published_time = '';
    public int|string $sort = 0;

    // ========== 核心约定：字段映射（必实现） ==========
    /**
     * 字段映射
     * @return array
     */
    public function getFieldMap(): array
    {
        return ['type', 'title','category_cascader_id_array', 'label_cascader_id_array', 'is_top', 'content', 'published_time', 'sort'];
    }

    // ========== 按需扩展：校验规则 ==========
    /**
     * 校验规则
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => [new Required()],
            'title' => ['bail', new Required(), new CheckBetween(4, 35)],
            'category_cascader_id_array' => ['bail', new Required(), new CheckArray()],
            'label_cascader_id_array' => ['bail', 'nullable', new CheckArray()],
            'is_top' => ['bail', new Numeric()],
            'content' => ['bail', new Required(), new CheckString(), new CheckBetween(1, 6000)],
            'published_time' => ['bail', 'nullable', new FormatTime(20)],
            'sort' => ['bail', new Numeric()],
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

        // 校验必填字段
        $requiredFields = ['title', 'category_cascader_id_array', 'content', 'type'];
        foreach ($requiredFields as $field) {
            if (!isset($validated[$field])) {
                throw new RuleException('RuleRequiredError', $field);
            }
        }

        // 处理content的htmlspecialchars
        if (isset($validated['content'])) {
            $validated['content'] = htmlspecialchars($validated['content']);
        }

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
