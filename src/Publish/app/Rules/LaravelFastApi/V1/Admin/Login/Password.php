<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 16:57:21
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-15 15:45:41
 * @FilePath: \app\Rules\LaravelFastApi\V1\Admin\Login\Password.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Rules\LaravelFastApi\V1\Admin\Login;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class Password implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = true;

        if (isset($value) || !empty($value))
        {
            $passwordPartten = "/^(?=.*\d)(?=.*[a-zA-Z]).{6,10}$/";

            $passwordResult = preg_match($passwordPartten, $value);

            if (!$passwordResult)
            {
                $result = false;
            }
        }

        if(!$result)
        {
            throw new RuleException('RuleAdminPasswordError', $attribute);
        }
    }
}
