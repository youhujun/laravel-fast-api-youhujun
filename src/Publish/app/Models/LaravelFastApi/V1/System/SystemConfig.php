<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-28 12:44:59
 * @FilePath: \app\Models\LaravelFastApi\V1\System\SystemConfig.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SystemConfig extends Model
{
    use HasFactory,Notifiable,SoftDeletes;

    public $timestamps = false;
    protected $table = 'system_config';
    protected $guarded = [''];
    protected $attributes =
    [
    ];

    protected function createdAt():Attribute
    {
        return new Attribute(

            set:fn($time) => date('Y-m-d H:i:s',$time),
        );
    }

    protected function updatedAt():Attribute
    {
        return new Attribute(

            set:fn($time) => date('Y-m-d H:i:s',$time),
        );
    }


    protected function itemPrice():Attribute
    {
        return new Attribute(
            get:fn($value) => number_format($value,2)
        );
    }


}
