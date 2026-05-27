<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 16:57:21
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-15 15:47:27
 * @FilePath: \app\Rules\LaravelFastApi\V1\Admin\User\User\CheckSex.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Rules\LaravelFastApi\V1\Admin\User\User;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class CheckSex implements ValidationRule
{
    /**
     * Run the validation rule.
     * 验证选择性别是否错误
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (isset($value) || !empty($value))
        {
            $CheckSex = '/^[012]+$/';

            $CheckSexVerify = preg_match($CheckSex, $value);

            if (!$CheckSexVerify)
            {
                throw new RuleException('RuleCheckSexError', $attribute);
            }
        }
    }
}
