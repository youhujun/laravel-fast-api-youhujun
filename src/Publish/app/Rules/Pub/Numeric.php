<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-09 13:44:50
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 01:41:59
 * @FilePath: \youhu-laravel-api-12\app\Rules\Pub\Numeric.php
 */

namespace App\Rules\Pub;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class Numeric implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = false;

        if(isset($value) || !empty($value))
        {
            $checkIsArray = is_array($value);

            if(!$checkIsArray)
            {
                $numericPartten = '/^(-1|[\d.]+)$/';

                $numericResult = preg_match($numericPartten, $value);

                if($numericResult)
                {
                    $result = true;
                }
            }
        }

        if(!$result)
        {
            throw new RuleException('RuleNumericError',$attribute);
        }
    }
}
