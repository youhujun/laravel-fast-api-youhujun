<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-09-08 17:22:16
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-05-20 17:41:18
 * @FilePath: \app\Rules\Common\IdNumber.php
 */

namespace App\Rules\Common;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class IdNumber implements ValidationRule
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
            $idNumberPartten = "/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/";

            $idNumberResult = preg_match($idNumberPartten, $value);

            if(!$idNumberResult)
            {
                throw new RuleException('RuleIdNumberError');
            }
        }
    }
}
