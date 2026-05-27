<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 16:57:21
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 11:39:33
 * @FilePath: \app\Rules\LaravelFastApi\V1\Admin\File\Action.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Rules\LaravelFastApi\V1\Admin\File;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class Action implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (isset($value) || !empty($value))
        {
            $actionPartten = '/^[a-zA-Z]+$/';

            $actionResult = preg_match($actionPartten, $value);

            if (!$actionResult)
            {
                throw new RuleException('RuleFileActionError', $attribute);
            }
        }
    }
}
