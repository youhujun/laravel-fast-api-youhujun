<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-08-02 14:58:42
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-28 20:21:43
 */

namespace App\Rules\LaravelFastApi\V1\Phone;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Exceptions\Common\RuleException;

class RegisterPhone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = true;

        if (isset($value) || !empty($value)) {
            $phonePartten = '/^(13[0-9]|14[5|7]|15[0|1|2|3|4|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/';

            $phoneResult = preg_match($phonePartten, $value);

            if (!$phoneResult) {
                throw new RuleException('RulePhoneError', $attribute);
            } else {
                $userIndexName = config('common_es.indices.user.users');

                $esUserObject = EsQueryFacade::index($userIndexName)->whereNull('deleted_at')->where('phone', $value)->get()->first();

                if (isset($esUserObject) && $esUserObject?->phone) {
                    throw new RuleException('RulePhoneIsRegister', $attribute);
                }
            }
        }
    }
}
