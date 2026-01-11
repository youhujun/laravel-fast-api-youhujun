<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-03 14:27:54
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-03 15:24:01
 * @FilePath: src\config\container_helper.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

use Illuminate\Contracts\Foundation\Application;

if (!function_exists('resolve_with_validation')) {
    /**
     * 从容器中解析一个类，并验证它是否实现了指定的接口。
     *
     * @param  Application  $app         The Laravel application instance.
     * @param  string|null  $concrete    The concrete class to resolve. If null, the default will be used.
     * @param  string       $interface   The interface that the resolved class must implement.
     * @param  string|null  $default     The default concrete class to use if $concrete is null or invalid.
     * @return mixed                     The resolved and validated instance.
     *
     * @throws \InvalidArgumentException If the resolved class does not implement the interface.
     */
    function resolve_with_validation(Application $app, ?string $concrete, string $interface, ?string $default = null)
    {
        // 1. 如果没有提供具体实现，或者提供的实现不存在，则使用默认实现
        if (empty($concrete) || !class_exists($concrete)) {
            // 如果连默认实现都没有，则无法继续，抛出异常
            if (empty($default) || !class_exists($default)) {
                throw new \InvalidArgumentException("No valid concrete class provided for interface [{$interface}].");
            }
            return $app->make($default);
        }

        // 2. 从容器中解析实例
        $instance = $app->make($concrete);

        // 3. 验证实例是否实现了指定的接口
        if (!$instance instanceof $interface) {
            throw new \InvalidArgumentException(
                "Class [{$concrete}] does not implement the required interface [{$interface}]."
            );
        }

        // 4. 验证通过，返回实例
        return $instance;
    }
}