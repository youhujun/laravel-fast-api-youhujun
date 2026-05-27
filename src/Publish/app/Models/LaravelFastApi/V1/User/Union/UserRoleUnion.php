<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-05-17 13:51:36
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-25 01:35:35
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\User\Union\UserRoleUnion.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\User\Union;

use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithShardRouting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\LaravelFastApi\V1\User\User;

class UserRoleUnion extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;
    use WithShardRouting;

    protected $fillable = ['user_role_union_uid','revision','user_uid', 'role_id','type','created_time', 'updated_time','created_at', 'created_time', 'updated_at', 'updated_time','deleted_at'];
    protected $hidden = ['id'];
    protected $table = 'user_role_unions';
    protected $primaryKey = 'user_role_union_uid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    // 时间戳格式
    protected $dateFormat = 'Y-m-d H:i:s';

    protected static function boot()
    {
        parent::boot();
    }

    public function getBaseTable(): string
    {
        return 'user_role_unions';
    }

    public function getShardBusinessKey(): string
    {
        return 'user_uid';
    }

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
        $table = User::getShardTableName($this->user_uid);
        return $this->belongsTo(User::class, 'user_uid', 'user_uid')->form($table);
    }
}
