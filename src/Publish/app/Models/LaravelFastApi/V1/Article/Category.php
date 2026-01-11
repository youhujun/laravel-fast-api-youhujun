<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 08:50:15
 * @FilePath: \app\Models\LaravelFastApi\V1\Article\Category.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\Article;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\LaravelFastApi\V1\Article\Article;

class Category extends Model
{
    use HasFactory,SoftDeletes;

    public $timestamps = false;
    protected $table = 'category';
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
     * 对应 文章分类和文章 多对多
     *
     * @return void
     */
    public function article()
    {
        return $this->newBelongsToMany(Article::class,'article_category_union','category_id','article_id');
    }

}
