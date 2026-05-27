<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-28 15:08:15
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-12-28 15:11:00
 * @FilePath: \App\Facades\LaravelFastApi\V1\Phone\Login\Wechat\PhoneLoginWechatFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Phone\Login\Wechat;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\Login\Wechat\PhoneLoginWechatFacadeService
 */
class PhoneLoginWechatFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneLoginWechatFacade";
    }
}
