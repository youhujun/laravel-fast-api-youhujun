<?php

/*
 * @Description: 队列任务表
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-21 00:00:00
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-21 00:00:00
 * @FilePath: \app\Models\LaravelFastApi\V1\Job\Job.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;

class Job extends Model
{
    use HasFactory;

    use WithTimeStampFields;
    use WithCustomConnection;

    /**
     * 批量赋值字段(明确可赋值字段,杜绝安全风险)
     */
    protected $fillable = ['id', 'queue', 'payload', 'attempts', 'reserved_at', 'available_at', 'created_at'];

    /**
     * 隐藏敏感/无用字段
     */
    protected $hidden = ['id'];

    /**
     * 表名
     */
    protected $table = 'jobs';

    /**
     * 主键
     */
    protected $primaryKey = 'id';

    /**
     * 自增ID
     */
    public $incrementing = true;

    /**
     * 关闭自动时间戳
     */
    public $timestamps = false;
    // 时间戳格式
    protected $dateFormat = 'Y-m-d H:i:s';
    /**
     * 类型转换
     */
    protected function casts(): array
    {
        return [
            'reserved_at' => 'datetime:Y-m-d H:i:s',
            'available_at' => 'datetime:Y-m-d H:i:s',
            'created_at' => 'datetime:Y-m-d H:i:s',
            'payload' => 'array',
        ];
    }
}
