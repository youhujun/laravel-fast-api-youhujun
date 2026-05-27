<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-10 13:57:22
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-02-15 11:08:31
 * @FilePath: \base.laravel.com\App\Rules\Pub\CheckChinese.php
 */

namespace App\Rules\Pub;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class CheckChinese implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if( isset($value) || !empty($value))
        {
            $chinesePartten = '/^[\x{4e00}-\x{9fa5}]+$/u';

            $chineseResult = preg_match($chinesePartten, $value);

            if(!$chineseResult)
            {
                throw new RuleException('RuleChineseError',$attribute);
            }
        }
    }
}
