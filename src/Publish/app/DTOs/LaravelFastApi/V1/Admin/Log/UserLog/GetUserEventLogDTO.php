<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-04-08 03:16:51
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-08 03:19:24
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Admin\Log\UserLog\GetUserEventLogDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */
namespace App\DTOs\LaravelFastApi\V1\Admin\Log\UserLog;

use App\DTOs\Traits\BaseDTOTrait;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Common\RuleException;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckArray;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Log\UserLogController
 */
class GetUserEventLogDTO
{
    use BaseDTOTrait;

    // 字段定义 - 严格按控制器验证规则
    public ?int $user_uid = null;
    public ?array $timeRange = null;
    public ?int $eventType = null;
    public ?int $sortType = null;
    public ?int $currentPage = null;
    public ?int $pageSize = null;

    // ========== 核心约定：字段映射 ==========
    public function getFieldMap(): array
    {
        return ['user_uid', 'timeRange', 'eventType', 'sortType', 'currentPage', 'pageSize'];
    }

    // ========== 校验规则 ==========
    public function rules(): array
    {
        return [
            'user_uid' => ['bail', 'nullable',  new Numeric()],
            'timeRange' => ['bail', 'nullable', new CheckArray()],
            'eventType' => ['bail', 'nullable', new Numeric()],
            'sortType' => ['bail', 'nullable', new Numeric()],
            'currentPage' => ['bail', 'nullable', new Numeric()],
            'pageSize' => ['bail', 'nullable', new Numeric()],
        ];
    }

    /**
     * 执行校验并抛出异常
     * @param array $data 待校验的数据
     * @return self
     * @throws RuleException
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
