<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-24 16:36:30
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\Picture\Album.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\Picture;

use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithShardRouting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

class Album extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;
    use WithShardRouting;

    // 实现抽象方法
    public function getBaseTable(): string
    {
        return 'albums';
    }

    public function getShardBusinessKey(): string
    {
        return 'user_uid';
    }

    protected $fillable = ['album_uid','shard_key', 'admin_uid', 'user_uid', 'cover_album_picture_uid', 'revision', 'is_default', 'is_system', 'album_type', 'album_name', 'album_description', 'sort', 'created_at', 'created_time', 'updated_at', 'updated_time','deleted_at'];
    protected $hidden = ['id','revision'];
    protected $table = 'albums';
    protected $primaryKey = 'album_uid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    // 时间戳格式
    protected $dateFormat = 'Y-m-d H:i:s';
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * 定义  相册对用户图片 一对多
     *
     * @return void
     */
    public function albumPictures()
    {
        $table = AlbumPicture::getShardTableName($this->album_uid);
        return $this->hasMany(AlbumPicture::class, 'album_uid', 'album_uid')->form($table);
    }

    //=====相对分割线=====

    /**
    * 相册 相对 用户 多对一
    *
    * @return void
    */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'user_uid');
    }

    /**
     * 相册 相对 管理员 多对一
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_uid', 'admin_uid');
    }

    /**
    * 对应 相册封面相对相册图片 多对一
    *
    * @return void
    */
    public function coverAlbumPicture()
    {
        return $this->belongsTo(AlbumPicture::class, 'cover_album_picture_uid', 'album_picture_uid');
    }
}
