<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-01 12:42:01
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\System\SystemConfig.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\System;

use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SystemConfig extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;

    protected $fillable = ['revision', 'parent_id', 'deep', 'item_type', 'item_label', 'item_value', 'item_price', 'item_path', 'item_introduction', 'sort', 'created_time', 'updated_time','created_at','updated_at','deleted_at'];
    protected $hidden = ['revision'];
    protected $table = 'system_configs';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    /**
     * 时间戳格式
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }



    protected function itemPrice(): Attribute
    {
        return new Attribute(
            set:fn ($price) => bcmul($price, 100, 2),
            get:fn ($price) => bcdiv($price, 100, 2),
        );
    }
}
