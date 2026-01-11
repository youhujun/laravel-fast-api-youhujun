<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-09 13:18:04
 * @FilePath: d:\wwwroot\PHP\Work\YouHu\youhu-laravel-api\app\Models\LaravelFastApi\V1\Admin\Admin.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\Admin;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;
use App\Models\LaravelFastApi\V1\System\Role\Role;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Article\Article;
use App\Models\LaravelFastApi\V1\Admin\Log\AdminLoginLog;
use App\Models\LaravelFastApi\V1\Admin\Log\AdminEventLog;

class Admin extends Authenticatable
{
    use HasFactory,Notifiable,SoftDeletes;

   //链接
	protected $connection = 'mysql';
	//表名
    protected $table = 'admin';
	//主键
	protected $primaryKey = 'id';
	//是否自动维护时间戳
	public $timestamps = false;
	//时间戳格式
	protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = [''];

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

    //模型关系
    public function album()
    {
        return $this->hasMany(Album::class,'admin_id','id');
    }

    //文章
    public function article()
    {
        return $this->hasMany(Article::class,'admin_id','id');
    }

    //管理员登录日志
    public function loginLog()
    {
        return $this->hasMany(AdminLoginLog::class,'admin_id','id');
    }

     //管理员事件日志
    public function eventLog()
    {
        return $this->hasMany(AdminEventLog::class,'admin_id','id');
    }


    //=====相对分割线=====

    /**
     * 对应  管理员 对用户 一对一
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }









}
