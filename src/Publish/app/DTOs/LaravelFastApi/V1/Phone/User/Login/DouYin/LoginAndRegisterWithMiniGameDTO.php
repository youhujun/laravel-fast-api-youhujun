<?php
/*
 * @Description: 抖音小游戏登录注册
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-26 19:05:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 19:05:00
 * @FilePath: \youhu-laravel-api-12\app\DTOs\LaravelFastApi\V1\Phone\User\Login\DouYin\LoginAndRegisterWithMiniGameDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\DTOs\LaravelFastApi\V1\Phone\User\Login\DouYin;

use App\DTOs\Traits\BaseDTOTrait;

class LoginAndRegisterWithMiniGameDTO
{
    use BaseDTOTrait;
    // 字段定义（赋默认值）
    public string $ip = '';

    /**
     * 字段映射
     * @return array
     */
    public function getFieldMap(): array
    {
        return ['ip'];
    }

    /**
     * 校验规则
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * 执行校验并抛出异常
     * @param array $data 待校验的数据
     * @return self 校验通过返回DTO实例
     */
    public function validate(array $data): self
    {
        $this->fill($data);
        $this->formatFields();

        return $this;
    }

    /**
     * 字段格式化
     * @return self
     */
    public function formatFields(): self
    {
        $this->ip = f($this->ip);
        return $this;
    }
}
