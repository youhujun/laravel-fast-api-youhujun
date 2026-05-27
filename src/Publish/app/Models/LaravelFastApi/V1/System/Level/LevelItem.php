<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-05-17 13:51:36
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-19 12:52:35
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\System\Level\LevelItem.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\System\Level;

use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\LaravelFastApi\V1\System\Level\UserLevel;

class LevelItem extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use WithTimeStampFields;

    protected $fillable = ['revision','note','level_name', 'level_code', 'amount', 'background_picture_uid','sort', 'created_time', 'updated_time','created_at','updated_at','deleted_at'];
    protected $hidden = ['revision'];
    protected $table = 'level_items';
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

    /**
     * 定义  多对多 级别配置项 对 用户级别
     *
     * @return void
     */
    public function userLevels()
    {
        return $this->belongsToMany(UserLevel::class, 'user_level_item_unions', 'level_item_id', 'user_level_id');
    }
}
