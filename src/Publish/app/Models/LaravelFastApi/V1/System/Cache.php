<?php

/*
 * @Description: 系统缓存表
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-21 00:00:00
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-21 00:00:00
 * @FilePath: \app\Models\LaravelFastApi\V1\System\Cache.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\WithCustomConnection;

class Cache extends Model
{
    use HasFactory;

    use WithCustomConnection;

    /**
     * 批量赋值字段(明确可赋值字段,杜绝安全风险)
     */
    protected $fillable = ['key', 'value', 'expiration'];

    /**
     * 隐藏敏感/无用字段
     */
    protected $hidden = ['id'];

    /**
     * 表名
     */
    protected $table = 'cache';

    /**
     * 主键
     */
    protected $primaryKey = 'key';

    /**
     * 主键非自增
     */
    public $incrementing = false;

    /**
     * 关闭自动时间戳
     */
    public $timestamps = false;

    /**
     * 类型转换
     */
    protected function casts(): array
    {
        return [
            'value' => 'binary',
            'expiration' => 'integer',
        ];
    }
}
