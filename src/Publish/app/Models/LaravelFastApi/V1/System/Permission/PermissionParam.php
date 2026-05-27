<?php

/*
 * @Description: 权限参数表
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-21 00:00:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-26 02:02:59
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\System\Permission\PermissionParam.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\System\Permission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithTimeStampFields;
use App\Models\LaravelFastApi\V1\System\Permission\Permission;

class PermissionParam extends Model
{
    use HasFactory;
    use SoftDeletes;

    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;

    /**
     * 批量赋值字段(明确可赋值字段,杜绝安全风险)
     */
    protected $fillable = ['permission_param_uid', 'permission_uid', 'param_key', 'param_value', 'param_type', 'is_required', 'default_value', 'description', 'sort', 'created_at', 'created_time', 'updated_at', 'updated_time', 'deleted_at'];

    /**
     * 隐藏敏感/无用字段
     */
    protected $hidden = ['id', 'revision'];

    /**
     * 表名
     */
    protected $table = 'permission_params';

    /**
     * 主键
     */
    protected $primaryKey = 'permission_param_uid';

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
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * 关联权限
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_uid', 'permission_uid');
    }
}
