<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-04 01:36:04
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-04 01:38:35
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\Api\Auth\YouHuAuthService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\Api\Auth;

use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithShardRouting;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class YouHuAuthService extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;
    use WithShardRouting;

    protected static function boot()
    {
        parent::boot();
    }
    /**
     * 分片路由基础表名（如需分片请实现此方法）
     */
    public function getBaseTable(): string
    {
        return 'youhu_auth_services';
    }

    /**
     * 分片业务键（如需分片请实现此方法）
     */
    public function getShardBusinessKey(): string
    {
        return 'user_uid';
    }

    /**
     * 批量赋值字段（明确可赋值字段，杜绝安全风险）
     示例：'user_uid', 'name', 'mobile', 'status'
     */
    protected $fillable = [
        'user_uid','shard_key','secret_key','service_flag','auth_token','status','revision','created_at', 'created_time', 'updated_at', 'updated_time','deleted_at'
    ];

    /**
     * 隐藏敏感/无用字段
     */
    protected $hidden = ['id', 'revision'];

    /**
     * 表名
     */
    protected $table = 'youhu_auth_services';

    /**
    *主键
    */
    protected $primaryKey = 'user_uid';

    /**
     * 主键类型 建议默认值：'string'（适配雪花ID）
     */
    protected $keyType = 'string';

    /**
     * 是否自增 建议默认值：false（雪花ID非自增,规避PHP处理雪花ID大数值溢出/截断风险）
     */
    public $incrementing = false;

    /**
     * 开启自动时间戳（Laravel自动维护created_at/updated_at）
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
            'created_time' => 'integer',
            'updated_time' => 'integer',
            'revision' => 'integer',
        ];
    }
}
