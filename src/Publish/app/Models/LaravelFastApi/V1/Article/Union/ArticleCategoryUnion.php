<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-23 05:36:57
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\Article\Union\ArticleCategoryUnion.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\Article\Union;

use App\Models\Traits\WithShardRouting;
use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ArticleCategoryUnion extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;
    use WithShardRouting;

    // 实现抽象方法（替代原来的属性定义）
    public function getBaseTable(): string
    {
        return 'article_category_unions';
    }

    protected function getShardBusinessKey(): string
    {
        return 'article_uid';
    }
    protected $fillable = ['article_category_union_uid', 'article_uid', 'category_id', 'created_time', 'updated_time','created_at','updated_at','deleted_at'];
    protected $hidden = ['revision'];
    protected $table = 'article_category_unions';
    protected $primaryKey = 'article_category_union_uid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    // 时间戳格式
    protected $dateFormat = 'Y-m-d H:i:s';
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
