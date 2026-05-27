<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-07 23:57:18
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-08 00:05:00
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Admin\Picture\Album\DeleteAlbumDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\DTOs\LaravelFastApi\V1\Admin\Picture\Album;

use App\DTOs\Traits\BaseDTOTrait;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Common\RuleException;
use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Picture\AlbumController
 */
class DeleteAlbumDTO
{
    use BaseDTOTrait;

    // 字段定义（赋默认值）
    public string $album_uid = '';

    // ========== 核心约定：字段映射（必实现） ==========
    /**
     * 字段映射
     * @return array
     */
    public function getFieldMap(): array
    {
        return ['album_uid'];
    }

    // ========== 按需扩展：校验规则 ==========
    /**
     * 校验规则
     * @return array
     */
    public function rules(): array
    {
        return [
            'album_uid' => ['bail', new Required(), new Numeric()],
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
        $requiredFields = ['album_uid'];
        foreach ($requiredFields as $field) {
            if (!isset($validated[$field])) {
                throw new RuleException('RuleRequiredError', $field);
            }
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
