<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-04-07 23:57:18
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-08 00:05:00
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Admin\Picture\Album\GetAlbumDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\DTOs\LaravelFastApi\V1\Admin\Picture\Album;

use App\DTOs\Traits\BaseDTOTrait;
use Illuminate\Support\Facades\Validator; 
use App\Exceptions\Common\RuleException; 
use App\Rules\Pub\Numeric;
use App\Rules\LaravelFastApi\V1\Admin\Common\sortType;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Picture\AlbumController
 */
class GetAlbumDTO
{
    use BaseDTOTrait;
    
    // 字段定义（赋默认值）
    public int|string $album_type = 0;
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
        return ['album_type', 'sortType', 'currentPage', 'pageSize'];
    }

    // ========== 按需扩展：校验规则 ==========
    /**
     * 校验规则
     * @return array
     */
    public function rules(): array
    {
        return [
            'album_type' => ['bail', 'nullable', new Numeric()],
            'sortType' => ['bail', 'nullable', new Numeric(), new sortType()],
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
