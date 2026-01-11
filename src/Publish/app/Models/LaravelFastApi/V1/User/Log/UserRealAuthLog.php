<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-05-17 13:51:36
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-21 02:44:11
 * @FilePath: \app\Models\LaravelFastApi\V1\User\Log\UserRealAuthLog.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\User\Log;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

use App\Models\LaravelFastApi\V1\User\User;

class UserRealAuthLog extends Model
{
    use HasFactory,Notifiable,SoftDeletes;

    public $timestamps = false;
    protected $table = 'user_real_auth_log';
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



    protected function authApplyAt():Attribute
    {
          return new Attribute(

            set:fn($time) => date('Y-m-d H:i:s',$time),
        );
    }

    protected function authAt():Attribute
    {
          return new Attribute(

            set:fn($time) => date('Y-m-d H:i:s',$time),
        );
    }


    /**
     * 定义 相对 多对一 用户实名热认证申请对用户
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }



}
