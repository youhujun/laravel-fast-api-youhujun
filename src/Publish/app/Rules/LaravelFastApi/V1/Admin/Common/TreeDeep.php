<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 16:57:21
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 17:20:10
 * @FilePath: \app\Rules\LaravelFastApi\V1\Admin\Common\TreeDeep.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Rules\LaravelFastApi\V1\Admin\Common;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\Log;

use App\Exceptions\Common\RuleException;

class TreeDeep implements Rule
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

        if($value == 0)
        {
            $result = false;
        }

        if(!$result)
        {
            $this->message = 'deep的值必须大于0';
            throw new RuleException('RuleTreeDeepError',$attribute);
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
