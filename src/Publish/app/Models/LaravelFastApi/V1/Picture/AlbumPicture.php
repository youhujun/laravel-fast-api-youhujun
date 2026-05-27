<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-10 04:14:17
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\Picture\AlbumPicture.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\Picture;

use App\Models\Traits\WithShardRouting;
use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\LaravelFastApi\V1\System\Module\Goods\GoodsClass;
use App\Models\LaravelFastApi\V1\System\Phone\PhoneBanner;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\User\Info\UserQrcode;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\User\User;

class AlbumPicture extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;
    use WithShardRouting;

    // 实现抽象方法（替代原来的属性定义）
    public function getBaseTable(): string
    {
        return 'album_pictures';
    }

    protected function getShardBusinessKey(): string
    {
        return 'album_uid';
    }

    protected $fillable = ['album_picture_uid','shard_key', 'admin_uid', 'user_uid', 'album_uid', 'revision', 'picture_name', 'picture_tag', 'picture_path', 'picture_file', 'picture_size', 'picture_spec', 'picture_type', 'picture_url', 'created_at', 'created_time', 'updated_at', 'updated_time','deleted_at'];
    protected $hidden = ['revision'];
    protected $table = 'album_pictures';
    protected $primaryKey = 'album_picture_uid';
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
     * 定义 图片和手机轮播图 一对一
     *
     * @return void
     */
    public function phoneBanner()
    {
        return $this->hasOne(PhoneBanner::class, 'album_picture_uid', 'album_picture_uid');
    }


    /**
     * 定义 用户相册图片相对用户头像 一对多
     *
     * @return void
     */
    public function userAvatars()
    {
        $table = UserAvatar::getShardTableName($this->user_uid);
        return $this->hasMany(UserAvatar::class, 'album_picture_uid', 'album_picture_uid')->from($table);
    }

    /**
     * 定义 用户相册图片相对用户二维码 一对多
     *
     * @return void
     */
    public function userQrcodes()
    {
        $table = UserQrcode::getShardTableName($this->user_uid);
        return $this->hasMany(UserQrcode::class, 'album_picture_uid', 'album_picture_uid')->from($table);
    }

    /**
     * 定义 用户相册图片相对相册封面 一对多
     *
     * @return void
     */
    public function coverAlbums()
    {
        $table = Album::getShardTableName($this->user_uid);
        return $this->hasMany(Album::class, 'cover_album_picture_uid', 'album_picture_uid')->from($table);
    }

    /**
         * 定义 一对多 产品分类
         */
    public function goodsClasses()
    {
        return $this->hasMany(GoodsClass::class, 'goods_class_picture_uid', 'album_picture_uid');
    }

    /**
     * 定义 一对多 标签
     */
    public function labels()
    {
        return $this->hasMany(GoodsClass::class, 'label_picture_uid', 'album_picture_uid');
    }

    //================================================分割线===========================================================

    /**
     * 对应 用户相册图片对用户 多对一
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'user_uid');
    }

    /**
    * 对应 用户相册图片对 用户相册 多对一
    *
    * @return void
    */
    public function albums()
    {
        $table = Album::getShardTableName($this->user_uid);
        return $this->hasMany(Album::class, 'album_uid', 'album_uid')->from($table);
    }
}
