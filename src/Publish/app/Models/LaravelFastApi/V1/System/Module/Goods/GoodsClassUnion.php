<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-15 11:57:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-15 12:03:37
 * @FilePath: \app\Models\LaravelFastApi\V1\System\Module\Goods\GoodsClassUnion.php
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

class GoodsClassUnion extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithCustomConnection;

    protected $fillable = ['goods_uid', 'revision', 'goods_class_id', 'created_time', 'updated_time'];
    protected $hidden = ['revision'];
    protected $table = 'goods_class_unions';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
