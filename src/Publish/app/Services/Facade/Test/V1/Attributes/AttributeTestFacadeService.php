<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-06-04 13:48:27
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-06-04 14:52:27
 * @FilePath: \youhu-laravel-api-13\app\Services\Facade\Test\V1\Attributes\AttributeTestFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\Test\V1\Attributes;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Attributes\Common\DocNote;
use App\Attributes\Common\DocParams;

/**
 * @see \App\Facades\Test\V1\Attributes\AttributeTestFacade
 */
#[DocNote('属性测试门面代理服务')]
class AttributeTestFacadeService
{
	#[DocNote('属性测试方法入口')]
   public function test()
   {
      // echo "AtrributeTestFacadeService test";
	  $this->testReflection();
	  $this->testParams(10);
   }

   #[DocNote('属性反射测试方法')] 
   public function testReflection()
   {
		// 读取类注解
		$refClass = new \ReflectionClass(\App\Services\Facade\Test\V1\Attributes\AttributeTestFacadeService::class);
		
		foreach ($refClass->getAttributes(DocNote::class) as $item) {
			$note = $item->newInstance()->note;
			p($note);
		}

		// 读取方法注解
		$refMethod = $refClass->getMethod('test');
		$methodAttrs = $refMethod->getAttributes(DocNote::class);

		foreach($methodAttrs as $attr) {
			$note = $attr->newInstance()->note;
			p($note);
		}
		
   }

   #[DocParams(note:'测试参数注解',params:['money'=>'金额'])]
   public function testParams(int $money)
   {
	    $ref = new \ReflectionMethod(\App\Services\Facade\Test\V1\Attributes\AttributeTestFacadeService::class,'testParams');
		$methodAttrs = $ref->getAttributes(DocParams::class);
		
		foreach($methodAttrs as $attr) {
			$note = $attr->newInstance()->note;
			p($note);
			$params = $attr->newInstance()->params;
			p($params);
		}
   }
}
