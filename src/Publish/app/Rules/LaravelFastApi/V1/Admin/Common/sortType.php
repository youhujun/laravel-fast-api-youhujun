<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-22 14:53:49
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 17:19:46
 * @FilePath: \app\Rules\LaravelFastApi\V1\Admin\Common\sortType.php
 */

namespace App\Rules\LaravelFastApi\V1\Admin\Common;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class sortType implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(isset($value) && !empty($value))
        {
            $sortTypePartten = "/^[1234]$/";

            $sortTypeResult = preg_match($sortTypePartten, $value);

            if(!$sortTypeResult)
            {
                throw new RuleException('RuleSortTypeError');
            }
        }
    }
}
