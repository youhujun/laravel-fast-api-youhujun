<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-29 12:06:09
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\User\User.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\User;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithInviteCode;
use App\Models\Traits\WithShardRouting;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Role\Role;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;
use App\Models\LaravelFastApi\V1\System\Level\UserLevel;
use App\Models\LaravelFastApi\V1\User\Info\UserIdCard;
use App\Models\LaravelFastApi\V1\User\Info\UserBank;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\User\Info\UserQrcode;
use App\Models\LaravelFastApi\V1\User\Info\UserAddress;
use App\Models\LaravelFastApi\V1\User\Log\UserEventLog;
use App\Models\LaravelFastApi\V1\User\Log\UserLoginLog;
use App\Models\LaravelFastApi\V1\User\Log\UserAmountLog;
use App\Models\LaravelFastApi\V1\User\Log\UserCoinLog;
use App\Models\LaravelFastApi\V1\User\Log\UserScoreLog;
use App\Models\LaravelFastApi\V1\User\Log\UserLocationLog;
use App\Models\LaravelFastApi\V1\User\Log\UserRealAuthLog;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\Article\Article;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    use WithTimeStampFields;  // 时间戳填充
    use WithSnowflakeId;      // 雪花ID生成
    use WithCustomConnection; // 自定义数据库连接
    use WithInviteCode; //邀请码

    use WithShardRouting;

    protected static function boot()
    {
        parent::boot();
    }

    // 实现抽象方法（替代原来的属性定义）
    public function getBaseTable(): string
    {
        return 'users';
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
        ];
    }
    /**
         * 批量赋值字段（明确可赋值字段，杜绝安全风险）
         *
         */
    protected $fillable = ['user_uid','shard_key','remember_token','source_user_uid', 'parent_user_uid', 'revision', 'account_status', 'real_auth_status', 'level_id', 'source','auth_token', 'account_name', 'invite_code', 'phone_area_code', 'phone', 'password', 'email', 'created_at', 'created_time', 'updated_at', 'updated_time','deleted_at'];
    /**
     * 隐藏敏感/无用字段
     */

    protected $hidden = ['id'];

    // 表名
    protected $table = 'users';
    // 主键
    protected $primaryKey = 'user_uid';
    // 雪花ID非自增
    public $incrementing = false;
    // 雪花ID是字符串类型
    protected $keyType = 'string';
    //开启自动时间戳（Laravel自动维护created_at/updated_at）
    public $timestamps = true;

    // 时间戳格式
    protected $dateFormat = 'Y-m-d H:i:s';

    // 关联推荐人（基于user_uid）
    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_user_uid', 'user_uid');
    }

    // 关联父级用户（基于user_uid）
    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_user_uid', 'user_uid');
    }


    /**
     * 用户和管理员 一对一
     */
    public function admin()
    {
        $table = Admin::getShardTableName($this->user_uid);

        return $this->hasOne(Admin::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和身份证 一对一
     */
    public function idCard()
    {
        //特意保留的写法
        UserIdCard::bindShardBusinessId($this->user_uid);
        $table = (new UserIdCard())->getTable();
        UserIdCard::clearBoundShardBusinessId();

        return $this->hasOne(UserIdCard::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和用户信息 一对一
     */
    public function userInfo()
    {
        $table = UserInfo::getShardTableName($this->user_uid);

        return $this->hasOne(UserInfo::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和用户头像 一对多
     */
    public function userAvatars()
    {
        $table = UserAvatar::getShardTableName($this->user_uid);

        return $this->hasMany(UserAvatar::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和用户二维码 一对多
     */
    public function userQrcodes()
    {
        $table = UserQrcode::getShardTableName($this->user_uid);

        return $this->hasMany(UserQrcode::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和用户地址 一对多
     */
    public function userAddresses()
    {
        $table = UserAddress::getShardTableName($this->user_uid);

        return $this->hasMany(UserAddress::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和用户事件日志 一对多
     */
    public function userEventLogs()
    {
        $table = UserEventLog::getShardTableName($this->user_uid);

        return $this->hasMany(UserEventLog::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和用户登录日志 一对多
     */
    public function userLoginLogs()
    {
        $table = UserLoginLog::getShardTableName($this->user_uid);

        return $this->hasMany(UserLoginLog::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和角色 多对多
     */
    public function roles()
    {
        $table = UserRoleUnion::getShardTableName($this->user_uid);

        return $this->belongsToMany(Role::class, $table, 'user_uid', 'role_id');
    }

    /**
     * 用户和用户相册 一对多
     */
    public function albums()
    {
        $table = Album::getShardTableName($this->user_uid);

        return $this->hasMany(Album::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和相册图片 一对多
     */
    public function albumPictures()
    {
        $table = AlbumPicture::getShardTableName($this->user_uid);

        return $this->hasMany(AlbumPicture::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和文章 一对多
     */
    public function articles()
    {
        $table = Article::getShardTableName($this->user_uid);

        return $this->hasMany(Article::class, 'publisher_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和银行卡 一对多
     */
    public function userBanks()
    {
        $table = UserBank::getShardTableName($this->user_uid);

        return $this->hasMany(UserBank::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和余额日志 一对多
     */
    public function amountLogs()
    {
        $table = UserAmountLog::getShardTableName($this->user_uid);

        return $this->hasMany(UserAmountLog::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和金币日志 一对多
     */
    public function coinLogs()
    {
        $table = UserCoinLog::getShardTableName($this->user_uid);

        return $this->hasMany(UserCoinLog::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和积分日志 一对多
     */
    public function scoreLogs()
    {
        $table = UserScoreLog::getShardTableName($this->user_uid);

        return $this->hasMany(UserScoreLog::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和位置日志 一对多
     */
    public function locationLogs()
    {
        $table = UserLocationLog::getShardTableName($this->user_uid);

        return $this->hasMany(UserLocationLog::class, 'user_uid', 'user_uid')->from($table);
    }

    /**
     * 用户和用户级别 多对一
     */
    public function userLevel()
    {
        $table = UserLevel::getShardTableName($this->user_uid);

        return $this->belongsTo(UserLevel::class, 'level_id', 'level_uid')->from($table);
    }

    /**
     * 用户和实名认证日志 一对多
     */
    public function userRealAuthLogs()
    {
        $table = UserRealAuthLog::getShardTableName($this->user_uid);

        return $this->hasMany(UserRealAuthLog::class, 'user_uid', 'user_uid')->from($table);
    }
}
