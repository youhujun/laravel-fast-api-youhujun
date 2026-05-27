<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-18 15:32:30
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-01 15:10:02
 * @FilePath: \youhu-laravel-api-12\app\Facades\Common\V1\Es\EsQueryFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\Common\V1\Es;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Common\V1\Es\EsQueryFacadeService
 */
class EsQueryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsQueryFacade";
    }

    public static function index(string $index)
    {
        return static::getFacadeRoot()->index($index);
    }

    public static function where(string $field, ...$params)
    {
        return static::getFacadeRoot()->where($field, ...$params);
    }

    public static function whereLike(string $field, string $value)
    {
        return static::getFacadeRoot()->whereLike($field, $value);
    }

    public static function whereBetween(string $field, array $range)
    {
        return static::getFacadeRoot()->whereBetween($field, $range);
    }

    public static function whereIn(string $field, array $values)
    {
        return static::getFacadeRoot()->whereIn($field, $values);
    }

    public static function whereNull(string $field)
    {
        return static::getFacadeRoot()->whereNull($field);
    }

    public static function whereNotNull(string $field)
    {
        return static::getFacadeRoot()->whereNotNull($field);
    }

    public static function orWhere(string $field, ...$params)
    {
        return static::getFacadeRoot()->orWhere($field, ...$params);
    }

    public static function orderBy(string $field, string $direction = 'asc')
    {
        return static::getFacadeRoot()->orderBy($field, $direction);
    }

    public static function page(int $page, int $pageSize)
    {
        return static::getFacadeRoot()->page($page, $pageSize);
    }

    public static function get()
    {
        return static::getFacadeRoot()->get();
    }

    public static function paginate()
    {
        return static::getFacadeRoot()->paginate();
    }

    public static function count()
    {
        return static::getFacadeRoot()->count();
    }
}
