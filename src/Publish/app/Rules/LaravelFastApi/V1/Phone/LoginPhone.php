<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-02-06 09:23:25
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-29 20:16:43
 * @FilePath: \youhu-laravel-api-12\app\Rules\LaravelFastApi\V1\Phone\LoginPhone.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Rules\LaravelFastApi\V1\Phone;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Exceptions\Common\RuleException;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Models\LaravelFastApi\V1\User\User;

class LoginPhone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (isset($value) || !empty($value)) {
            $phonePartten = '/^(13[0-9]|14[5|7]|15[0|1|2|3|4|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/';

            $phoneResult = preg_match($phonePartten, $value);

            if (!$phoneResult) {
                throw new RuleException('RulePhoneError', $attribute);
            } else {
                //手机号审核通过后,要确保数据库中有该用户,并且该用户是可用的
                $indexName = config('common_es.indices.user.users');

                $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('phone', $value)->where('account_status', 1)->get()->first();

                if (!isset($esUserObject->user_uid)) {
                    throw new RuleException('NoRulePhoneError', $attribute);
                }
            }
        }
    }
}
