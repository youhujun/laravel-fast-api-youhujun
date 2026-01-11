<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-07-24 14:49:06
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-12-25 15:24:58
 */


namespace App\Models\LaravelFastApi\V1\User\Info;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

class UserAvatar extends Model
{
    use HasFactory,SoftDeletes;

    public $timestamps = false;
    protected $table = 'user_avatar';
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



     //================================================分割线===========================================================

     /**
     * 对应 用头像对用户 多对一
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    /**
     * 对应  用户头像和 用户相册图片 一对一
     *
     * @return void
     */
    public function albumPicture()
    {
        return $this->belongsTo(AlbumPicture::class,'album_picture_id','id');
    }
}
