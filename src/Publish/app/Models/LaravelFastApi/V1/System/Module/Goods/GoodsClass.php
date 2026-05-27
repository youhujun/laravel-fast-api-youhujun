<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-09 13:33:43
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-09 12:51:38
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\System\Module\Goods\GoodsClass.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\System\Module\Goods;

use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

class GoodsClass extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithCustomConnection;

    protected $fillable = ['revision','parent_id','deep','switch', 'goods_class_picture_uid', 'goods_class_name', 'goods_class_code', 'rate', 'is_certificate','certificate_number','note','sort', 'created_time', 'updated_time','created_at','updated_at','deleted_at'];
    protected $hidden = ['revision'];
    protected $table = 'goods_classes';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    /**
     * 时间戳格式
     */
    protected $dateFormat = 'Y-m-d H:i:s';
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    protected function rate(): Attribute
    {
        return new Attribute(
            get:fn ($rate) => \bcdiv($rate, 100, 2),
            set:fn ($rate) => bcmul($rate, 100, 2),
        );
    }

    /**
     * 想对 多对一 相册图片表
     *
     * @return void
     */
    public function picture()
    {
        return $this->belongsTo(AlbumPicture::class, 'goods_class_picture_uid', 'album_picture_uid');
    }
}
