<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: lak 15931400746@163.com
 * @Date: 2023-08-14 18:02:09
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-02-15 11:06:38
 * @FilePath: \base.laravel.com\App\Rules\Pub\CheckString.php
 */

namespace App\Rules\Pub;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class CheckString implements ValidationRule
{
    /**
     * Run the validation rule.
     * 验证是否为字符串
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if( isset($value) || !empty($value))
        {
            $checkStringResult = is_string($value);

            if(!$checkStringResult)
            {
                throw new RuleException('RuleCheckStringError',$attribute);
            }
        }
    }
}
