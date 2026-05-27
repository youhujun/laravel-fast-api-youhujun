<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-02-06 09:23:25
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-18 13:36:30
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\System\Platform\SystemWechatConfig.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\System\Platform;

use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SystemWechatConfig extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use WithTimeStampFields;

    protected $fillable = ['revision', 'name','type', 'appid', 'appsecret', 'note','sort', 'created_time', 'updated_time','created_at','updated_at','deleted_at'];
    protected $hidden = ['revision'];
    protected $table = 'system_wechat_configs';
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
}
