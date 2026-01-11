<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 16:57:21
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-15 15:47:27
 * @FilePath: \app\Rules\LaravelFastApi\V1\Admin\User\User\CheckSex.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Rules\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\Log;

use App\Exceptions\Common\RuleException;

class CheckSex implements Rule
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
     * 验证选择性别是否错误
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
            $CheckSex = '/^[012]+$/';

            $CheckSexVerify = preg_match($CheckSex, $value);

            if (!$CheckSexVerify)
            {
                $result = false;
            }
        }

        if(!$result)
        {
            $this->message = '性别选择错误';
            throw new RuleException('RuleCheckSexError', $attribute);
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
        return  $this->message;
    }
}
