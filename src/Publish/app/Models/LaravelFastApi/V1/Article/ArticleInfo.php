<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-22 22:21:16
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\Article\ArticleInfo.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\Article;

use App\Models\Traits\WithShardRouting;
use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\LaravelFastApi\V1\Article\Article;
use Illuminate\Database\Eloquent\Builder;

class ArticleInfo extends Model
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
        return 'article_infos';
    }

    protected function getShardBusinessKey(): string
    {
        return 'article_uid';
    }

    protected $fillable = ['article_info_uid', 'article_uid', 'revision', 'article_info', 'created_time', 'updated_time','created_at','updated_at','deleted_at'];
    protected $hidden = ['revision'];
    protected $table = 'article_infos';
    protected $primaryKey = 'article_info_uid';
    public $incrementing = false;
    protected $keyType = 'string';
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

    protected function articleInfo(): Attribute
    {
        return new Attribute(
            get:fn ($value) => htmlspecialchars_decode($value),
        );
    }


    /**
     * 对应  文章详情和文章 一对一
     *
     * @return void
     */
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_uid', 'article_uid');
    }
}
