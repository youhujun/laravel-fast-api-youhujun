<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-08 07:38:38
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-21 22:18:15
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Admin\System\Role\AddRoleDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\DTOs\LaravelFastApi\V1\Admin\System\Role;

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
use App\Rules\LaravelFastApi\V1\Admin\Common\TreeDeep;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\Role\RoleController
 */
class AddRoleDTO
{
    use BaseDTOTrait;
    // 字段定义（赋默认值）
    public int $parent_id = 0;
    public int $deep = 0;
    public int $type = 0;
    public int $is_system = 0;
    public int $sort = 0;
    public string $role_name = '';
    public string $logic_name = '';

    // ========== 核心约定：字段映射（必实现） ==========
    /**
     * 字段映射（供TS类型生成/微服务对接，支持单表/多表格式）
     * 多表格式：['表名' => ['字段1','字段2'], ...]
     * 单表格式：['字段1','字段2']（极简适配）
     * @return array
     */
    public function getFieldMap(): array
    {
        return ['parent_id', 'deep','type','is_system','sort', 'role_name', 'logic_name'];
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
            'parent_id' => ['bail', new Required(), new Numeric()],
            'deep' => ['bail', new Required(), new TreeDeep()],
            'type' => ['bail', new Required(),  new Numeric()],
            'is_system' => ['bail', new Required(),  new Numeric()],
            'sort' => ['bail', new Required(), new Numeric()],
            'role_name' => ['bail', new Required(), new CheckString(), new CheckBetween(2, 10)],
            'logic_name' => ['bail', new Required(), new CheckString(), new CheckBetween(3, 60)],
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
        $requiredFields = ['parent_id', 'deep','type','is_system', 'sort', 'role_name', 'logic_name'];
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
        // 字符串字段使用 f() 过滤
        $this->role_name = f($this->role_name);
        $this->logic_name = f($this->logic_name);

        // int 字段保持原类型

        return $this;
    }
}
