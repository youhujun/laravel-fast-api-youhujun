<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-02-06 09:23:25
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-17 18:01:57
 * @FilePath: \app\Rules\LaravelFastApi\V1\Phone\LoginPhone.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Rules\LaravelFastApi\V1\Phone;

use Illuminate\Contracts\Validation\Rule;

use App\Exceptions\Common\RuleException;

use App\Models\LaravelFastApi\V1\User\User;

class LoginPhone implements Rule
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
                $this->message = '手机号不正确';
				throw new RuleException('RulePhoneError', $attribute);
            }
            else
            {
                //手机号审核通过后,要确保数据库中有该用户,并且该用户是可用的
                $userQuery = User::where([['phone', '=', $value], ['switch', '=', 1]]);

				//p($userQuery->count());die;

                if (!$userQuery->count())
                {
                    $result = false;
                    $this->message = '账号异常,请联系客服';
					throw new RuleException('NoRulePhoneError', $attribute);
                }
            }
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
