<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 08:53:05
 * @FilePath: \app\Models\LaravelFastApi\V1\Picture\Album.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\Picture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

class Album extends Model
{
    use HasFactory,SoftDeletes;

    public $timestamps = false;
    protected $table = 'album';
    protected $guarded = [''];
    protected $attributes =
    [

    ];

    protected function createdAt():Attribute
    {
        return new Attribute(

            set:fn($time) => date('Y-m-d H:i:s',$time),
        );
    }

    protected function updatedAt():Attribute
    {
        return new Attribute(

            set:fn($time) => date('Y-m-d H:i:s',$time),
        );
    }


    /**
     * 定义  相册对用户图片 一对多
     *
     * @return void
     */
    public function albumPicture()
    {
        return $this->hasMany(AlbumPicture::class,'album_id','id');
    }

    //=====相对分割线=====

     /**
     * 相册 相对 用户 多对一
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    /**
     * 相册 相对 管理员 多对一
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class,'admin_id','id');
    }

     /**
     * 对应 相册封面相对相册图片 多对一
     *
     * @return void
     */
    public function coverAlbumPicture()
    {
        return $this->belongsTo(AlbumPicture::class,'cover_album_picture_id','id');
    }

}
