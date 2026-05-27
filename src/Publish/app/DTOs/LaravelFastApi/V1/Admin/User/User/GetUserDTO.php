<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-14 00:42:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-21 11:30:07
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Admin\User\User\GetUserDTO.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\DTOs\LaravelFastApi\V1\Admin\User\User;

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
use App\Rules\Common\IdNumber;
use App\Rules\LaravelFastApi\V1\Admin\User\User\CheckSex;
use App\Rules\LaravelFastApi\V1\Admin\Login\Password;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserController
 */
class GetUserDTO
{
    use BaseDTOTrait;
    //分页
    public int $currentPage = 1;
    //每页条数
    public int $pageSize = 10;
    //只能是1和2
    public int $sortType = 2;
    //是否导出
    public ?int $isExport = null;
    //导出类型 10本页
    public ?int $exportType = null;
    //搜索内容
    public ?string $find = null;
    //配合搜索内容指定下标,用来映射搜索字段
    public int $findSelectIndex = 0;
    //时间范围
    public array $timeRange = [];
    //用户状态
    public int $account_status = 1;
    //实名认证状态  10未认证 20认证中 30未通过 40通过
    public ?int $real_auth_status = null;

    // ========== 核心约定：字段映射（必实现） ==========
    /**
     * 字段映射（供TS类型生成/微服务对接，支持单表/多表格式）
     * 多表格式：['表名' => ['字段1','字段2'], ...]
     * 单表格式：['字段1','字段2']（极简适配）
     * @return array
     */
    public function getFieldMap(): array
    {
        return ['currentPage','pageSize','sortType','isExport','exportType','find','findSelectIndex','timeRange','account_status','real_auth_status'];
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
            // 当前页
            'currentPage' => ['bail', 'nullable', new Numeric()],
            // 每页条数
            'pageSize' => ['bail', 'nullable', new Numeric()],
            // 排序类型
            'sortType' => ['bail', new Required(), new Numeric()],
            // 是否导出
            'isExport' => ['bail', 'nullable', new Numeric()],
            // 导出类型
            'exportType' => ['bail', 'nullable', new Numeric()],
            // 查找内容
            'find' => ['bail', 'nullable', new CheckString()],
            // 查找内容项下标
            'findSelectIndex' => ['bail', 'nullable', new Numeric()],
            // 时间范围 添加时间
            'timeRange' => ['bail', 'nullable', new CheckArray()],
            // 是否禁用
            'account_status' => ['bail', 'nullable', new Numeric()],
            // 状态 0未申请 10未认证 20申请中  30通过 40拒绝
            'real_auth_status' => ['bail', 'nullable', new Numeric()],
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
        $requiredFields = ['sortType'];

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
