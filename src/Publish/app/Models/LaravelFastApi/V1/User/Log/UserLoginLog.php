<?php

/*
 * @Description:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-09-09 13:42:58
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 05:05:03
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\User\Log\UserLoginLog.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\User\Log;

use App\Models\Traits\WithShardRouting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithCustomConnection;
use App\Models\LaravelFastApi\V1\User\User;
use Illuminate\Database\Eloquent\Builder;

class UserLoginLog extends Model
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
        return 'user_login_logs';
    }

    protected function getShardBusinessKey(): string
    {
        return 'user_uid';
    }

    /**
     * 批量赋值字段（明确可赋值字段，杜绝安全风险）
     */
    protected $fillable = [
        'user_login_log_uid','shard_key', 'user_uid', 'revision', 'status', 'instruction', 'ip', 'data_type','login_type', 'created_at', 'created_time', 'updated_at', 'updated_time', 'deleted_at'
    ];

    /**
     * 隐藏敏感/无用字段
     */
    protected $hidden = ['id', 'revision'];

    // 表名
    protected $table = 'user_login_logs';
    // 主键
    protected $primaryKey = 'user_login_log_uid';
    // 雪花ID非自增
    public $incrementing = false;
    // 雪花ID是字符串类型
    protected $keyType = 'string';
    // 开启自动时间戳（Laravel自动维护created_at/updated_at）
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

    //================================================相对分割线===========================================================

    /**
     * 对应 用户登录日志对用户 多对一
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'user_uid');
    }
}
