<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-04-08 07:28:26
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-08 13:29:30
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\DouyinConfig\DeleteSystemDouyinConfigDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */
namespace App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\DouyinConfig;

use App\DTOs\Traits\BaseDTOTrait;
use Illuminate\Support\Facades\Validator; 
use App\Exceptions\Common\RuleException; 
use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemDouyinConfigController
 */
class DeleteSystemDouyinConfigDTO
{
    use BaseDTOTrait;

    public int $id = 0;
    public int $is_delete = 0;

    public function getFieldMap(): array
    {
        return ['id', 'is_delete'];
    }

    public function rules(): array
    {
        return [
            'id' => ['bail', new Required(), new Numeric()],
            'is_delete' => ['bail', new Required(), new Numeric()],
        ];
    }

    public function validate(array $data): self
    {
        $validator = Validator::make($data, $this->rules(), []);
        $validated = $validator->validated();

        $requiredFields = ['id', 'is_delete'];
        foreach ($requiredFields as $field) {
            if (!isset($validated[$field])) {
                throw new RuleException('RuleRequiredError', $field);
            }
        }

        $this->fill($validated);
        $this->formatFields();

        return $this;
    }

    public function formatFields(): self
    {
        return $this;
    }
}
