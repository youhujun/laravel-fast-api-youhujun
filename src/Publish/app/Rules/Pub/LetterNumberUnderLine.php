<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: lak 15931400746@163.com
 * @Date: 2023-08-14 17:57:17
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-17 11:30:10
 * @FilePath: \App\Rules\Pub\LetterNumberUnderLine.php
 */

namespace App\Rules\Pub;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class LetterNumberUnderLine implements ValidationRule
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
            $CheckLetterNumberUnderLine = '/^|[a-zA-Z0-9_]+$/u';

            $CheckLetterNumberUnderLineVerify = preg_match($CheckLetterNumberUnderLine, $value);

            if (!$CheckLetterNumberUnderLineVerify)
            {
                throw new RuleException('RuleCheckLetterNumberUnderLineError', $attribute);
            }
        }
    }
}
