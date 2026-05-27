<?php

/*
 * @Description: 邀请码序列表
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-21 00:00:00
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-21 00:00:00
 * @FilePath: \app\Models\LaravelFastApi\V1\System\InviteCodeSequence.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithTimeStampFields;

class InviteCodeSequence extends Model
{
    use HasFactory;

    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;

    /**
     * 批量赋值字段(明确可赋值字段,杜绝安全风险)
     */
    protected $fillable = ['invite_code_sequence_uid', 'sequence_name', 'prefix', 'start_value', 'current_value', 'length', 'padding_char', 'is_increment', 'step', 'min_value', 'max_value', 'cycle_type', 'status', 'last_reset_at', 'last_reset_value', 'created_at', 'created_time', 'updated_at', 'updated_time'];

    /**
     * 隐藏敏感/无用字段
     */
    protected $hidden = ['id'];

    /**
     * 表名
     */
    protected $table = 'invite_code_sequences';

    /**
     * 主键
     */
    protected $primaryKey = 'invite_code_sequence_uid';

    /**
     * 雪花ID非自增
     */
    public $incrementing = false;

    /**
     * 雪花ID是字符串类型
     */
    protected $keyType = 'string';

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
            'last_reset_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
