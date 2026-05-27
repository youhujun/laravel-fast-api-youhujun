<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-30 10:55:37
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\User\Platform\UserWechatUnionid.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\User\Platform;

use App\Models\Traits\WithShardRouting;
use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithCustomConnection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class UserWechatUnionid extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;
    use WithShardRouting;

    // 实现抽象方法（替代原来的属性定义）
    public function getBaseTable(): string
    {
        return 'user_wechat_unionids';
    }

    protected function getShardBusinessKey(): string
    {
        return 'user_uid';
    }

    protected $fillable = ['user_wechat_unionid_uid', 'user_uid', 'unionid', 'revision', 'sort', 'created_time', 'updated_time','created_at', 'created_time', 'updated_at', 'updated_time','deleted_at'];
    protected $hidden = ['id', 'revision'];
    protected $table = 'user_wechat_unionids';
    protected $primaryKey = 'user_wechat_unionid_uid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    // 时间戳格式
    protected $dateFormat = 'Y-m-d H:i:s';
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\LaravelFastApi\V1\User\User::class, 'user_uid', 'user_uid');
    }
}
