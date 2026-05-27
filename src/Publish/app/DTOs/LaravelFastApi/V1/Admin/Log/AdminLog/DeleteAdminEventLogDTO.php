<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-04-08 02:49:09
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-08 02:51:45
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\DeleteAdminEventLogDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */
namespace App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog;

use App\DTOs\Traits\BaseDTOTrait;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Common\RuleException;
use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;

/**
 *  @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Log\AdminLogController
 */
class DeleteAdminEventLogDTO
{
    use BaseDTOTrait;

    // 字段定义 - 严格按控制器验证规则
    public int|string $admin_event_log_uid = '';

    // ========== 核心约定：字段映射 ==========
    public function getFieldMap(): array
    {
        return ['admin_event_log_uid'];
    }

    // ========== 校验规则 ==========
    public function rules(): array
    {
        return [
            'admin_event_log_uid' => ['bail', new Required(), new Numeric()],
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

        if (!isset($validated['admin_event_log_uid'])) {
            throw new RuleException('RuleRequiredError', 'admin_event_log_uid');
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
        return $this;
    }
}
