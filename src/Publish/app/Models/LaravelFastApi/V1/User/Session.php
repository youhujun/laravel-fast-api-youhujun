<?php

/*
 * @Description: 用户会话表
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-21 00:00:00
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-21 00:00:00
 * @FilePath: \app\Models\LaravelFastApi\V1\User\Session.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;

class Session extends Model
{
    use HasFactory;
    use SoftDeletes;

    use WithTimeStampFields;
    use WithCustomConnection;

    /**
     * 批量赋值字段(明确可赋值字段,杜绝安全风险)
     */
    protected $fillable = ['id', 'user_uid', 'ip_address', 'user_agent', 'payload', 'last_activity', 'note', 'sort', 'created_at', 'created_time', 'updated_at', 'updated_time', 'deleted_at'];

    /**
     * 隐藏敏感/无用字段
     */
    protected $hidden = ['payload', 'id'];

    /**
     * 表名
     */
    protected $table = 'sessions';

    /**
     * 主键
     */
    protected $primaryKey = 'id';

    /**
     * 自增ID
     */
    public $incrementing = true;

    /**
     * 开启自动时间戳
     */
    public $timestamps = true;

    /**
     * 时间戳格式
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * 类型转换
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'user_uid');
    }
}
