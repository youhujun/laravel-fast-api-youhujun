<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-09 13:46:30
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-20 08:44:35
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\System\Module\Label.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\System\Module;

use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Label extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithCustomConnection;

    protected $fillable = ['revision','parent_id','deep','switch', 'label_picture_uid', 'label_name', 'label_code','note', 'sort', 'created_time', 'updated_time','created_at','updated_at','deleted_at'];
    protected $hidden = ['revision'];
    protected $table = 'labels';
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
