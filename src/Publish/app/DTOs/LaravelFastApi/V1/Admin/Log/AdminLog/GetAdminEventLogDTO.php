<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-08 02:49:09
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-08 02:51:45
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\GetAdminEventLogDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog;

use App\DTOs\Traits\BaseDTOTrait;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Common\RuleException;
use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckArray;

/**
 *  @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Log\AdminLogController
 */
class GetAdminEventLogDTO
{
    use BaseDTOTrait;

    // 字段定义（赋默认值）- 严格按控制器 $request->all() 验证规则
    public ?int $admin_uid = null;
    public ?array $timeRange = null;
    public ?int $eventType = null;
    public ?int $sortType = null;
    public ?int $currentPage = null;
    public ?int $pageSize = null;

    // ========== 核心约定：字段映射（必实现） ==========
    /**
     * 字段映射
     * @return array
     */
    public function getFieldMap(): array
    {
        return ['admin_uid', 'timeRange', 'eventType', 'sortType', 'currentPage', 'pageSize'];
    }

    // ========== 按需扩展：校验规则 ==========
    /**
     * 校验规则
     * @return array
     */
    public function rules(): array
    {
        return [
            'admin_uid' => ['bail', 'nullable', new Numeric()],
            'timeRange' => ['bail', 'nullable', new CheckArray()],
            'eventType' => ['bail', 'nullable', new Numeric()],
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
        return $this;
    }
}
