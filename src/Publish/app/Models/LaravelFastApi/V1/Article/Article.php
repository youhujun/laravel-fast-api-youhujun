<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 20:53:07
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\Article\Article.php
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
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\System\Module\Label;
use App\Models\LaravelFastApi\V1\System\Module\Article\Category;
use App\Models\LaravelFastApi\V1\Article\ArticleInfo;
use Illuminate\Database\Eloquent\Builder;

class Article extends Model
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
        return 'articles';
    }

    protected function getShardBusinessKey(): string
    {
        return 'user_uid';
    }

    protected $fillable = ['article_uid','shard_key', 'admin_uid', 'user_uid', 'revision', 'title', 'status', 'type', 'is_top', 'check_status', 'category_cascader_json', 'label_cascader_json', 'published_at', 'published_time', 'checked_at', 'checked_time', 'sort','created_time', 'updated_time','created_at','updated_at','deleted_at'];

    protected $hidden = ['revision'];
    protected $table = 'articles';
    protected $primaryKey = 'article_uid';
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
            'published_at' => 'datetime:Y-m-d H:i:s',
            'checked_at' => 'datetime:Y-m-d H:i:s',
        ];
    }


    /**
     * 定义文章和文章详情  一对一
     *
     * @return void
     */
    public function articleInfo()
    {
        return $this->hasOne(ArticleInfo::class, 'article_uid', 'article_uid');
    }



    /**
     * 定义 文章和文章分类 多对多
     *
     * @return void
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'article_category_unions', 'article_uid', 'category_id')->wherePivot('deleted_at', null);
    }

    /**
     * 定义 文章和文章分类 多对多
     *
     * @return void
     */
    public function labels()
    {
        return $this->belongsToMany(Label::class, 'article_label_unions', 'article_uid', 'label_id')->wherePivot('deleted_at', null);
    }

    /**
     * 对应 文章和用户 多对一
     *
     * @return void
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_uid', 'admin_uid');
    }

    /**
     * 对应 文章和用户 多对一
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'user_uid');
    }
}
