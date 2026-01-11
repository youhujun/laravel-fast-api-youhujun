<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 20:26:46
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-27 09:55:52
 * @FilePath: \app\Models\LaravelFastApi\V1\System\Level\UserLevel.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Models\LaravelFastApi\V1\System\Level;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UserLevel extends Model
{
    use HasFactory,Notifiable,SoftDeletes;

	//链接
	protected $connection = 'mysql';
	//表名
    protected $table = 'user_level';
	//主键
	protected $primaryKey = 'id';
	//是否自动维护时间戳
	public $timestamps = false;
	//时间戳格式
	protected $dateFormat = 'Y-m-d H:i:s';

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

	protected function amount():Attribute
    {
        return new Attribute(

            get:fn($amount) => number_format($amount,'2','.',''),
        );
    }

}
