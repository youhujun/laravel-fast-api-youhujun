<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-09 13:33:43
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-09 16:43:02
 * @FilePath: \app\Models\System\Module\Goods\GoodsClass.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\System\Module\Goods;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

class GoodsClass extends Model
{
    use HasFactory,Notifiable,SoftDeletes;

	//链接
	protected $connection = 'mysql';
	//表名
    protected $table = 'goods_class';
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

	protected function rate():Attribute
    {
        return new Attribute(
            set:fn($rate) => \bcdiv($rate,100,2),
        );
    }

    /**
     * 想对 多对一 相册图片表
     *
     * @return void
     */
    public function picture()
    {
        return $this->belongsTo(AlbumPicture::class,'goods_class_picture_id', 'id');
    }

}
