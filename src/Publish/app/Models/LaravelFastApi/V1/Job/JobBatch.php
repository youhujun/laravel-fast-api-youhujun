<?php

/*
 * @Description: 队列任务批次表
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-21 00:00:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-26 02:00:20
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\Job\JobBatch.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;

class JobBatch extends Model
{
    use HasFactory;

    use WithTimeStampFields;
    use WithCustomConnection;

    /**
     * 批量赋值字段(明确可赋值字段,杜绝安全风险)
     */
    protected $fillable = ['id', 'name', 'total_jobs', 'pending_jobs', 'failed_jobs', 'failed_job_ids', 'options', 'cancelled_at', 'created_at', 'finished_at'];

    /**
     * 隐藏敏感/无用字段
     */
    protected $hidden = ['id'];

    /**
     * 表名
     */
    protected $table = 'job_batches';

    /**
     * 主键
     */
    protected $primaryKey = 'id';

    /**
     * 雪花ID是字符串类型
     */
    protected $keyType = 'string';

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
            'cancelled_at' => 'datetime:Y-m-d H:i:s',
            'created_at' => 'datetime:Y-m-d H:i:s',
            'finished_at' => 'datetime:Y-m-d H:i:s',
            'failed_job_ids' => 'array',
            'options' => 'array',
        ];
    }
}
