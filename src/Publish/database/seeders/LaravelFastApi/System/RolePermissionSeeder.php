<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-01-06 12:36:10
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-06-12 00:53:15
 * @FilePath: \youhu-laravel-api-13\database\seeders\LaravelFastApi\System\RolePermissionSeeder.php
 */

namespace Database\Seeders\LaravelFastApi\System;

use App\Models\LaravelFastApi\V1\System\Permission\Permission;
use App\Models\LaravelFastApi\V1\System\Role\Role;
use App\Models\LaravelFastApi\V1\System\Union\RolePermissionUnion;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RolePermissionUnion::truncate();

        $this->command->info('开始绑定开发者和超管的权限');

        // 查询所有的菜单路由
        $permissionCollection = Permission::select(['id'])->orderBy('id', 'asc')->get();

        // 开发者和超级管理员的role_id
		$developerRoleObject = Role::where('logic_name', 'develop')->first();
		$superAdminRoleObject = Role::where('logic_name', 'super')->first();

        $developerRoleId = $developerRoleObject->id;
        $superAdminRoleId = $superAdminRoleObject->id;
		
        // 批量创建角色权限关联
        foreach ($permissionCollection as $permission) {
            // 开发者权限
            RolePermissionUnion::create([
                'permission_id' => $permission->id,
                'role_id' => $developerRoleId,
                'created_time' => time(),
            ]);

            // 超级管理员权限
            RolePermissionUnion::create([
                'permission_id' => $permission->id,
                'role_id' => $superAdminRoleId,
                'created_time' => time(),
            ]);
        }


        $this->command->info('✅绑定绑定开发者和超管的权限完成');
    }
}
