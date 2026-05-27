<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-12 18:01:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-02-15 11:10:00
 * @FilePath: \base.laravel.com\App\Rules\Pub\FormatTime.php
 */

namespace App\Rules\Pub;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

use App\Exceptions\Common\RuleException;

class FormatTime implements ValidationRule
{
    /**
     *
     * @var [type] 10 Y-m-d 20 Y-m-d H:i:s
     */
    protected $timeType;

    public function __construct($timeType = 10)
    {
        $this->timeType = $timeType;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(isset($value))
        {
            if($this->timeType == 10)
            {
                $timePartten = '/^\d{4}-\d{2}-\d{2}$/';
            }

            if($this->timeType == 20)
            {
                $timePartten =  '/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}$/';
            }

            $timeResult = preg_match($timePartten, $value);

            if(!$timeResult)
            {
                throw new RuleException('RuleTimeFormatError',$attribute);
            }
        }
    }
}
