<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-09 14:14:31
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 08:50:43
 * @FilePath: \app\Models\LaravelFastApi\V1\Article\Info\ArticleInfo.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\Article\Info;

use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\LaravelFastApi\V1\Article\Article;

class ArticleInfo extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;

    protected $fillable = ['article_info_uid', 'article_uid', 'revision', 'article_info', 'created_time', 'updated_time'];
    protected $hidden = ['revision'];
    protected $table = 'article_infos';
    protected $primaryKey = 'article_info_uid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

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
