<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-05-17 13:51:36
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-20 23:33:57
 * @FilePath: \app\Models\LaravelFastApi\V1\User\Union\UserRoleUnion.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Models\LaravelFastApi\V1\User\Union;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UserRoleUnion extends Model
{
    use HasFactory,SoftDeletes;
   //链接
	protected $connection = 'mysql';
	//表名
    protected $table = 'user_role_union';
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


}
