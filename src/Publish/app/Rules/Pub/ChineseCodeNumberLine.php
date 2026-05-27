<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-10 14:16:39
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-02-15 11:09:13
 * @FilePath: \base.laravel.com\App\Rules\Pub\ChineseCodeNumberLine.php
 */

namespace App\Rules\Pub;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class ChineseCodeNumberLine implements ValidationRule
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
            $chineseCodeNumberLinePartten = '/^([\x{4e00}-\x{9fa5}]|[a-zA-Z0-9_-])+$/u';

            $chineseCodeNumberLineResult = preg_match($chineseCodeNumberLinePartten, $value);

            if(!$chineseCodeNumberLineResult)
            {
                throw new RuleException('RuleChineseCodeNumberLineError',$attribute);
            }
        }
    }
}
