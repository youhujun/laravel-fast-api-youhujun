<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-09 13:32:40
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-09 13:52:47
 * @FilePath: \app\Models\System\Module\Bank.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\System\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Bank extends Model
{
    use HasFactory,Notifiable,SoftDeletes;

	//链接
	protected $connection = 'mysql';
	//表名
    protected $table = 'bank';
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

}
