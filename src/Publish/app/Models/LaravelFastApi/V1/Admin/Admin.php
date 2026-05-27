<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-29 13:15:00
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\Admin\Admin.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\Admin;

use App\Models\Traits\WithShardRouting;
use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithCustomConnection;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Redis;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;
use App\Models\LaravelFastApi\V1\System\Role\Role;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Article\Article;
use App\Models\LaravelFastApi\V1\Admin\Log\AdminLoginLog;
use App\Models\LaravelFastApi\V1\Admin\Log\AdminEventLog;
use Illuminate\Database\Eloquent\Builder;

class Admin extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;
    use WithShardRouting;

    // 实现抽象方法（替代原来的属性定义）
    public function getBaseTable(): string
    {
        return 'admins';
    }

    protected function getShardBusinessKey(): string
    {
        return 'user_uid';
    }

    protected $fillable = ['admin_uid','shard_key', 'user_uid', 'remember_token', 'account_name', 'phone_area_code', 'phone', 'password', 'email', 'revision', 'created_time', 'updated_time'];
    protected $hidden = ['id'];
    protected $table = 'admins';
    protected $primaryKey = 'admin_uid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    //模型关系
    public function albums()
    {
        $table = Album::getShardTableName($this->admin_uid);

        return $this->hasMany(Album::class, 'admin_uid', 'admin_uid')->from($table);
    }

    //文章
    public function articles()
    {
        $table = Article::getShardTableName($this->admin_uid);

        return $this->hasMany(Article::class, 'admin_uid', 'admin_uid')->from($table);
    }

    //管理员登录日志
    public function loginLogs()
    {
        $table = AdminLoginLog::getShardTableName($this->admin_uid);

        return $this->hasMany(AdminLoginLog::class, 'admin_uid', 'admin_uid')->from($table);
    }

    //管理员事件日志
    public function eventLogs()
    {
        $table = AdminEventLog::getShardTableName($this->admin_uid);

        return $this->hasMany(AdminEventLog::class, 'admin_uid', 'admin_uid')->from($table);
    }


    //=====相对分割线=====

    /**
     * 对应  管理员 对用户 一对一
     *
     * @return void
     */
    public function user()
    {
        $table = User::getShardTableName($this->user_uid);

        return $this->belongsTo(User::class, 'user_uid', 'user_uid')->from($table);
    }
}
