<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-27 14:38:50
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-01 16:33:03
 * @FilePath: \youhu-laravel-api-12\app\DTOs\Template\V1\Replace\AddReplaceDTO.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\DTOs\Template\V1\Replace;

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
use App\Rules\Common\Phone;
use App\Rules\LaravelFastApi\V1\Admin\User\User\CheckSex;
use App\Rules\LaravelFastApi\V1\Admin\Login\Password;

/**
 * @see \App\Http\Controllers\Template\V1\Replace\ReplceController
 */
class AddReplaceDTO
{
    use BaseDTOTrait;

    // 添加用户DTO字段
    public ?string $source_replace_uid = null;
    public string $phone = '';
    public string $password = '';
    public ?string $nick_name = null;
    public ?int $sex = null;


    // ========== 核心约定：字段映射（必实现） ==========
    /**
     * 字段映射（供TS类型生成/微服务对接，支持单表/多表格式）
     * 多表格式：['表名' => ['字段1','字段2'], ...]
     * 单表格式：['字段1','字段2']（极简适配）
     * @return array
     */
    public function getFieldMap(): array
    {
        return ['source_replace_uid', 'phone', 'password', 'nick_name', 'sex'];
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
            'source_replace_uid' => ['bail', 'nullable', new Numeric()],
            'phone' => ['bail', new Required(), new CheckString(), new Phone(), new CheckUnique('replaces', 'phone')],
            'password' => ['bail', new Required(), new CheckString(), new CheckBetween(6, 15), new Password()],
            'nick_name' => ['bail', 'nullable', new CheckString(), new CheckBetween(1, 60)],
            'sex' => ['bail', 'nullable', new Numeric(), new CheckSex()],
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

        // 3. 校验字段是否存在（和你控制器里的逻辑一致）
        $requiredFields = ['phone', 'password'];
        foreach ($requiredFields as $field) {
            if (!isset($validated[$field])) {
                throw new RuleException('RuleRequiredError', $field);
            }
        }

        // 4. 校验通过，给DTO赋值并格式化
        $this->fill($validated); // 复用BaseDTOTrait的fill方法
        $this->formatFields(); // 执行字段格式化

        return $this;
    }

    /**
     * 字段格式化（按需实现，非必填）
     * 说明：Laravel Request已默认trim字符串，仅以下场景需实现：
     * 1. 时间戳格式转换（如string转int、时间字符串转Carbon）
     * 2. 类型强转（如string转int/float）
     * 3. 特殊字符过滤（如防XSS、替换非法字符）
     * 4. 业务规则格式化（如手机号去空格/横杠）
     * @return self
     */
    public function formatFields(): self
    {
        // 按需添加格式化逻辑，默认留空即可
        // 示例：
        // $this->timestamp = (int)$this->timestamp; // 时间戳转整型
        // $this->mobile = preg_replace('/\D/', '', $this->mobile); // 手机号去非数字
        foreach ($this->getFieldMap() as $field) {
            $this->$field = f($this->$field);
        }
        return $this;
    }
}
