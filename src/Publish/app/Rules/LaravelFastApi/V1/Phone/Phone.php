<?php
/*
 * @Descripttion:
 * @version:
 * @Author: Lak
 * @Date: 2023-08-08 09:16:03
 * @LastEditors: Lak
 * @LastEditTime: 2023-08-11 10:13:19
 */

namespace App\Rules\LaravelFastApi\V1\Phone;

use App\Exceptions\Common\RuleException;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

class Phone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = true;

        if (isset($value) || !empty($value))
        {
            $phonePartten = '/^(13[0-9]|14[5|7]|15[0|1|2|3|4|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/';

            $phoneResult = preg_match($phonePartten, $value);

            if (!$phoneResult)
            {
                $result = false;
            }
        }

        if(!$result)
        {
            throw new RuleException('RulePhoneError', $attribute);
        }
    }
}
