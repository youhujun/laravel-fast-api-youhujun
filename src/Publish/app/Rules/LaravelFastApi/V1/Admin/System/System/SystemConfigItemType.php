<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-10 15:51:57
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2023-08-28 15:19:16
 * @FilePath: \api.laravel.com_LV9\app\Rules\Admin\System\System\SystemConfigItemType.php
 */

namespace App\Rules\LaravelFastApi\V1\Admin\System\System;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class SystemConfigItemType implements ValidationRule
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
            $systemConfigItemTypeArray = [10,20,30,40];

            $systemConfigItemTypeRResult = in_array($value,$systemConfigItemTypeArray);

            if(!$systemConfigItemTypeRResult)
            {
                throw new RuleException('RuleSystemConfigItemTypeError',$attribute);
            }
        }
    }
}
