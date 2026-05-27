<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-26 16:19:32
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 16:20:51
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Phone\User\Register\UserRegisterDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\DTOs\LaravelFastApi\V1\Phone\User\Register;

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
use App\Rules\LaravelFastApi\V1\Phone\RegisterPhone;

class UserRegisterDTO
{
    use BaseDTOTrait;

    public string $phone = '';
    public string $register_code = '';
    public string $password = '';
    public string $invite_id = '';
    public string $invite_code = '';
    //注册来源 10 H5 20抖音 30微信
    public int $source = 0;

    // ========== 核心约定：字段映射（必实现） ==========
    /**
     * 字段映射（供TS类型生成/微服务对接，支持单表/多表格式）
     * 多表格式：['表名' => ['字段1','字段2'], ...]
     * 单表格式：['字段1','字段2']（极简适配）
     * @return array
     */
    public function getFieldMap(): array
    {
        return ['phone', 'register_code', 'password', 'invite_id', 'invite_code','source'];
    }

    // ========== 按需扩展：校验规则（非必实现，复用场景才加） ==========
    /**
     * 校验规则（仅需要复用校验时实现，否则保留空数组）
     * 已写好的接口：无需修改，默认空数组即可
     * 复用场景：复制控制器校验规则到此处，实现跨模块复用
     * @return array
     */
    public function rules(): array
    {
        return [
            'phone' => ['bail', new Required(), new CheckString(), new RegisterPhone()],
            'register_code' => ['bail', new Required(), new CheckString()],
            'password' => ['bail', new Required(), new CheckString()],
            'invite_id' => ['bail', 'nullable', new CheckString()],
            'invite_code' => ['bail', 'nullable', new CheckString()],
            'source' => ['bail', 'nullable', new Numeric()],
        ];
    }

    /**
     * 核心：执行校验并抛出异常（把控制器的校验逻辑移到这里）
     * @param array $data 待校验的数据（如request->all()）
     * @return self 校验通过返回DTO实例（已赋值）
     * @throws RuleException 校验不通过直接抛异常
     */
    public function validate(array $data): self
    {
        // 1. 初始化Validator（复用DTO的rules）
        $validator = Validator::make($data, $this->rules(), []);
        // 2. 执行校验，获取校验后的数据
        $validated = $validator->validated();

        // 3. 校验字段是否存在
        $requiredFields = ['phone', 'register_code', 'password'];
        foreach ($requiredFields as $field) {
            if (!isset($validated[$field])) {
                throw new RuleException('RuleRequiredError', $field);
            }
        }

        // 4. 校验通过，给DTO赋值并格式化
        $this->fill(f($validated)); // 复用BaseDTOTrait的fill方法
        $this->formatFields(); // 执行字段格式化

        return $this;
    }

    /**
     * 字段格式化（按需实现，非必填）
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
