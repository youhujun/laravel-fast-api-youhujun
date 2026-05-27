<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-04-08 04:22:23
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-08 04:27:07
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Admin\System\Permission\UpdateMenuDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */
namespace App\DTOs\LaravelFastApi\V1\Admin\System\Permission;

use App\DTOs\Traits\BaseDTOTrait;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Common\RuleException;
use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\CheckArray;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\Permission\PermissionController
 */
class UpdateMenuDTO
{
    use BaseDTOTrait;

    // 字段定义 - 严格按控制器验证规则
    public int|string $id = 0;
    public int $parent_id = 0;
    public int $deep = 0;
    public int $type = 0;
    public string $route_name = '';
    public string $route_path = '';
    public string $component = '';
    public int $hidden = 0;
    public int $always_show = 0;
    public ?string $perm = null;
    public ?int $switch = null;
    public ?int $sort = null;
    public ?string $icon = null;
    public string $title = '';
    public ?int $cache = null;
    public ?int $affix = null;
    public ?int $breadcrumb = null;
    public ?string $active_menu = null;
    public ?string $redirect = null;
    public ?array $params = null;

    // ========== 核心约定：字段映射 ==========
    public function getFieldMap(): array
    {
        return [
            'id', 'parent_id', 'deep', 'type', 'route_name', 'route_path', 'component',
            'hidden', 'always_show', 'perm', 'switch', 'sort', 'icon', 'title',
            'cache', 'affix', 'breadcrumb', 'active_menu', 'redirect', 'params'
        ];
    }

    // ========== 校验规则 ==========
    public function rules(): array
    {
        return [
            'id' => ['bail', new Required(), new Numeric()],
            'parent_id' => ['bail', new Required(), new Numeric()],
            'deep' => ['bail', new Required(), new Numeric()],
            'type' => ['bail', new Required(), new Numeric()],
            'route_name' => ['bail', new Required(), new CheckString()],
            'route_path' => ['bail', new Required(), new CheckString()],
            'component' => ['bail', new Required(), new CheckString()],
            'hidden' => ['bail', new Required(), new Numeric()],
            'always_show' => ['bail', new Required(), new Numeric()],
            'perm' => ['bail', 'nullable', new CheckString()],
            'switch' => ['bail', 'nullable', new Numeric()],
            'sort' => ['bail', 'nullable', new Numeric()],
            'icon' => ['bail', 'nullable', new CheckString()],
            'title' => ['bail', new Required(), new CheckString()],
            'cache' => ['bail', 'nullable', new Numeric()],
            'affix' => ['bail', 'nullable', new Numeric()],
            'breadcrumb' => ['bail', 'nullable', new Numeric()],
            'active_menu' => ['bail', 'nullable', new CheckString()],
            'redirect' => ['bail', 'nullable', new CheckString()],
            'params' => ['bail', 'nullable', new CheckArray()],
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

        $requiredFields = ['id', 'parent_id', 'deep', 'type', 'route_name', 'route_path', 'component', 'hidden', 'always_show', 'title'];
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
        return $this;
    }
}
