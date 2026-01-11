<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-13 20:50:30
 * @FilePath: \app\Models\LaravelFastApi\V1\Article\ArticleInfo.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Models\LaravelFastApi\V1\Article;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\LaravelFastApi\V1\Article\Article;

class ArticleInfo extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'article_info';
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



    protected function articleInfo():Attribute
    {
        return new Attribute(

            get:fn($value) => htmlspecialchars_decode($value),
        );
    }


    /**
     * 对应  文章详情和文章 一对一
     *
     * @return void
     */
    public function article()
    {
        return $this->belongsTo(Article::class,'article','id');
    }

}
