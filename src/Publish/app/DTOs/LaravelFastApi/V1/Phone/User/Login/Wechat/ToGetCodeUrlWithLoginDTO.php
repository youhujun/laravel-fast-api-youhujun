<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-04-26 17:05:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 17:07:42
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Phone\User\Login\Wechat\ToGetCodeUrlWithLoginDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */
namespace App\DTOs\LaravelFastApi\V1\Phone\User\Login\Wechat;

use App\DTOs\Traits\BaseDTOTrait;
use Illuminate\Support\Facades\Validator; 
use App\Exceptions\Common\RuleException; 
use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckUnique;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckArray;
use App\Rules\Pub\FormatTime;
use App\Rules\Pub\ChineseCodeNumberLine;

class ToGetCodeUrlWithLoginDTO
{
    use BaseDTOTrait;
    // 字段定义（赋默认值）
    public int $scope_type = 0;
    public int $state = 0;
    public string $auth_redirect_url = '';

    /**
     * 字段映射
     * @return array
     */
    public function getFieldMap(): array
    {
        return ['scope_type', 'state', 'auth_redirect_url'];
    }

    /**
     * 校验规则
     * @return array
     */
    public function rules(): array
    {
        return [
            'scope_type' => ['bail', new Required(), new Numeric()],
            'state' => ['bail', new Required(), new Numeric()],
            'auth_redirect_url' => ['bail', new Required(), new CheckString()],
        ];
    }

    /**
     * 执行校验并抛出异常
     * @param array $data 待校验的数据
     * @return self 校验通过返回DTO实例
     * @throws RuleException 校验不通过直接抛异常
     */
    public function validate(array $data): self
    {
        $validator = Validator::make($data, $this->rules(), []);
        $validated = $validator->validated();

        $requiredFields = ['scope_type', 'state', 'auth_redirect_url'];
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
        $this->auth_redirect_url = f($this->auth_redirect_url);
        return $this;
    }
}