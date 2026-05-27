<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-27 23:23:31
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\User\Union\UserSystemWechatConfigUnion.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\User\Union;
use App\Models\Traits\WithShardRouting;
use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithCustomConnection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSystemWechatConfigUnion extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;
	use WithShardRouting;

    protected $fillable = ['user_system_wechat_config_union_uid', 'user_uid', 'revision', 'openid', 'session_key', 'type', 'system_wechat_config_id', 'access_token', 'expires_in', 'refresh_token', 'scope', 'is_snapshotuser', 'verified_at', 'verified_time', 'created_at', 'created_time', 'updated_at', 'updated_time','deleted_at'];
    protected $hidden = ['id', 'revision'];
    protected $table = 'user_system_wechat_config_unions';
    protected $primaryKey = 'user_system_wechat_config_union_uid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

	protected static function boot()
    {
        parent::boot();
    }

    public function getBaseTable(): string
    {
        return 'user_system_wechat_config_unions';
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
            'verified_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

	// 时间戳格式
    protected $dateFormat = 'Y-m-d H:i:s';

    public function user()
    {
        return $this->belongsTo(\App\Models\LaravelFastApi\V1\User\User::class, 'user_uid', 'user_uid');
    }
}
