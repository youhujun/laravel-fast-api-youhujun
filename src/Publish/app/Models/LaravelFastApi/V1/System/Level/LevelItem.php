<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-05-17 13:51:36
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-24 19:50:56
 * @FilePath: \app\Models\LaravelFastApi\V1\System\Level\LevelItem.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\System\Level;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\LaravelFastApi\V1\System\Level\UserLevel;

class LevelItem extends Model
{
    use HasFactory,Notifiable,SoftDeletes;

    public $timestamps = false;
    protected $table = 'level_item';
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
     * 定义  多对多 级别配置项 对 用户级别
     *
     * @return void
     */
    public function userLevel()
    {
        return $this->belongsToMany(UserLevel::class, 'user_level_item_union', 'level_item_id', 'user_level_id');
    }
}
