<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-10 02:41:10
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-10 02:41:48
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Index\Union\Group;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacadeService
 */
class EsCreateGroupUnionIndexFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsCreateGroupUnionIndexFacade";
    }

    public static function createGoodsClassUnionsIndex()
    {
        return static::getFacadeRoot()->createGoodsClassUnionsIndex();
    }

    public static function createGoodsLabelUnionsIndex()
    {
        return static::getFacadeRoot()->createGoodsLabelUnionsIndex();
    }

    public static function createArticleCategoryUnionsIndex()
    {
        return static::getFacadeRoot()->createArticleCategoryUnionsIndex();
    }

    public static function createArticleLabelUnionsIndex()
    {
        return static::getFacadeRoot()->createArticleLabelUnionsIndex();
    }
}
