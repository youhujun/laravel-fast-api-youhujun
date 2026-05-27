<?php

/*
 * @Description: 用户认证信息表
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-21 00:00:00
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-21 00:00:00
 * @FilePath: \app\Models\LaravelFastApi\V1\User\UserCertification.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\User;

use App\Models\Traits\WithShardRouting;
use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class UserCertification extends Model
{
    use HasFactory;
    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;
    use WithShardRouting;

    // 实现抽象方法（替代原来的属性定义）
    public function getBaseTable(): string
    {
        return 'user_certifications';
    }

    protected function getShardBusinessKey(): string
    {
        return 'user_uid';
    }

    /**
     * 批量赋值字段(明确可赋值字段,杜绝安全风险)
     */
    protected $fillable = ['user_certification_uid','revision','user_uid', 'cert_type', 'cert_status', 'certified_at', 'certified_time', 'auditor_uid', 'cert_remark', 'expired_at', 'created_at', 'created_time', 'updated_at', 'updated_time'];

    /**
     * 隐藏敏感/无用字段
     */
    protected $hidden = ['id'];

    /**
     * 表名
     */
    protected $table = 'user_certifications';

    /**
     * 主键
     */
    protected $primaryKey = 'user_certification_uid';

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
            'certified_at' => 'datetime:Y-m-d H:i:s',
            'expired_at' => 'datetime:Y-m-d H:i:s',
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
