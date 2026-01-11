<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 16:57:21
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 17:19:30
 * @FilePath: \app\Rules\LaravelFastApi\V1\Admin\Common\Phone.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Rules\LaravelFastApi\V1\Admin\Common;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\Log;

use App\Exceptions\Common\RuleException;

class Phone implements Rule
{
    protected $message;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
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
            $this->message = '手机号格式错误';
            throw new RuleException('RulePhoneError', $attribute);
        }


        return $result;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        //return 'The validation error message.';
        return $this->message;
    }
}
