<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-06-04 14:26:20
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-06-04 14:30:03
 * @FilePath: \youhu-laravel-api-13\app\Attributes\Common\DocParams.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Attributes\Common;

use Attribute;

/**
 * TARGET_CLASS：仅能标注类、接口、Trait，不能贴方法、变量。
 * TARGET_METHOD：仅能标注类内部的成员方法。
 * TARGET_FUNCTION：专门标注全局独立函数（不在任何 class 里的普通 function）
 * TARGET_PROPERTY：类的成员属性
 * TARGET_CLASS_CONSTANT：类常量
 * TARGET_PARAMETER：函数 / 方法入参
 * TARGET_ALL：包含所有选项除了 IS_REPEATABLE
 * IS_REPEATABLE：标注属性可以重复使用
 */

#[Attribute(Attribute::TARGET_ALL | Attribute::IS_REPEATABLE)]

class DocParams
{
    public function __construct(public string $note,public ?array $params = null)
    {
		
    }
}