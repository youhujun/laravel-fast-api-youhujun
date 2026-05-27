<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: lak 15931400746@163.com
 * @Date: 2023-08-11 17:22:17
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-02-15 11:09:36
 * @FilePath: \base.laravel.com\App\Rules\Pub\FileType.php
 */

namespace App\Rules\Pub;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class FileType implements ValidationRule
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
            $fileTypePartten = '/^[a-z]+$/';

            $fileTypeResult = preg_match($fileTypePartten, $value);

            if(!$fileTypeResult)
            {
                throw new RuleException('RuleFileTypeError',$attribute);
            }
        }
    }
}
