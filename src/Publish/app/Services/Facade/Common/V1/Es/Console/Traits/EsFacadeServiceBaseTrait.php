<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-19 17:55:05
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-20 13:56:56
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Common\V1\Es\Console\Traits\EsFacadeServiceBaseTrait.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Services\Facade\Common\V1\Es\Console\Traits;

trait EsFacadeServiceBaseTrait
{
    /**
     * 自定义控制台彩色输出（和Service保持一致）
     */
    public function consoleOutput(string $message, string $type = 'info'): void
    {
        $colors = [
            'info' => "\033[32m",
            'error' => "\033[31m",
            'warn' => "\033[33m",
            'reset' => "\033[0m"
        ];
        echo $colors[$type] . $message . $colors['reset'] . PHP_EOL;
    }
}
