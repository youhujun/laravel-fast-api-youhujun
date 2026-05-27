<?php
/*
 * @Descripttion:
 * @version:
 * @Author: Lak
 * @Date: 2023-08-10 10:13:57
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-02-15 11:08:14
 */

namespace App\Rules\Pub;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class CheckBetween implements ValidationRule
{
    //最小值
    protected $minLength = 0;

    //最大值
    protected $maxLength;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($minLength,$maxLength)
    {
        $this->minLength = $minLength;

        $this->maxLength = $maxLength;
    }

    /**
     * Run the validation rule.
     * 验证是否符合长度
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if( isset($value) || !empty($value))
        {
            $checkBetweenResult = mb_strlen($value);

            if ($checkBetweenResult < $this->minLength || $checkBetweenResult > $this->maxLength)
            {
                throw new RuleException('RuleCheckBetweenLengthError',$attribute);
            }
        }
    }
}
