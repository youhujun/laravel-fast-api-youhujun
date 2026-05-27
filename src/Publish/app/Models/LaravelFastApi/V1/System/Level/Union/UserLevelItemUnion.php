<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-09 13:31:55
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-09 13:53:16
 * @FilePath: \app\Models\System\Level\Union\UserLevelItemUnion.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\System\Level\Union;

use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UserLevelItemUnion extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithCustomConnection;

    protected $fillable = ['revision', 'level_item_id', 'user_level_id', 'created_time', 'updated_time'];
    protected $hidden = ['revision'];
    protected $table = 'user_level_item_unions';
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
