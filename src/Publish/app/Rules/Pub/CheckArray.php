<?php

/*
 * @Descripttion:
 * @version:
 * @Author: Lak
 * @Date: 2023-08-09 16:31:57
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-05-20 17:42:17
 */

namespace App\Rules\Pub;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Exceptions\Common\RuleException;

class CheckArray implements ValidationRule
{
    /**
     * Run the validation rule.
     * 验证是否为数组
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (isset($value) || !empty($value)) {
            $checkArrayResult = is_array($value);

            if (!$checkArrayResult) {
                throw new RuleException('RuleCheckArrayError', $attribute);
            }
        }
    }
}
