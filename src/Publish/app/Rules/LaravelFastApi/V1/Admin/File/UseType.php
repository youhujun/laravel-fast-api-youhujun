<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 16:57:21
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 11:39:49
 * @FilePath: \app\Rules\LaravelFastApi\V1\Admin\File\UseType.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Rules\LaravelFastApi\V1\Admin\File;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\Log;

use App\Exceptions\Common\RuleException;

class UseType implements Rule
{
    protected $message;


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

        if( isset($value) || !empty($value))
        {
            $useTypeArray = [10,20,30];

            $useTypeRResult = in_array($value,$useTypeArray);

            if(!$useTypeRResult)
            {
                $result = false;
            }

        }

        if(!$result)
        {
            $this->message = 'use_type的值不正确';
            throw new RuleException('RuleUseTypeError',$attribute);
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
        return  $this->message;
    }
}
