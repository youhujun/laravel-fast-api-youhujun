<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-04-01 17:27:38
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-01 23:20:41
 * @FilePath: \youhu-laravel-api-12\app\Contracts\Template\V1\Replace\DoReplaceHandlerContract.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Contracts\Template\V1\Replace;

interface  DoReplaceHandlerContract 
{
	 /**
     * 
     * @param $param
     */

   public function handle($param);
}
