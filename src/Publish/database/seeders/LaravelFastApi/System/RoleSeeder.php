<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-01-06 12:36:10
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-06-12 11:19:17
 * @FilePath: \youhu-laravel-api-13\database\seeders\LaravelFastApi\System\RoleSeeder.php
 */

namespace Database\Seeders\LaravelFastApi\System;

use Illuminate\Database\Seeder;
use App\Models\LaravelFastApi\V1\System\Role\Role;
use App\Attributes\Common\DocParams;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$roleTreeArray = [
			[
				'role_name' => '开发者',
				'logic_name' => 'develop',
				'type' => 10,
				'is_system' => 1
			],
			[
				'role_name' => '超级管理员',
				'logic_name' => 'super',
				'type' => 10,
				'is_system' => 1
			],
			[
				'role_name' => '管理员',
				'logic_name' => 'admin',
				'type' => 10,
				'is_system' => 1,
				'children'=>[
					[
						'role_name' => '配置管理员',
						'logic_name' => 'config_admin',
						'type' => 10,
						'is_system' => 1,
					],
					[
						'role_name' => '相册管理员',
						'logic_name' => 'album_admin',
						'type' => 10,
						'is_system' => 1,
					],
					[
						'role_name' => '订单管理员',
						'logic_name' => 'order_admin',
						'type' => 10,
						'is_system' => 1
					],
					[
						'role_name' => '文章管理员',
						'logic_name' => 'article_admin',
						'type' => 10,
						'is_system' => 1,
					]

				]
			],
			[
				'role_name' => '用户',
				'logic_name' => 'user',
				'type' => 20,
				'is_system' => 1,
			]
		];

		
		Role::truncate();

		$this->command->info('开始填充系统角色');

		$this->insertTree($roleTreeArray, 0, 1);

		$this->command->info('✅填充系统角色完成');

      
    }

	#[DocParams(note:'递归插入角色树',params:['nodes'=>'当前层级节点数组','parentId'=>'父级ID','deep'=>'当前深度'])]
    protected function insertTree(array $nodes, int $parentId, int $deep)
    {
        foreach ($nodes as $node) {
            // 插入当前节点
            $item = Role::create([
                'role_name' => $node['role_name'],
                'logic_name' => $node['logic_name'],
                'type' => $node['type'],
                'is_system' => $node['is_system'],
                'parent_id' => $parentId,
                'deep' => $deep,
				'switch' => 1,
                'created_time' => time(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // 递归插入子节点，父ID为刚插入的主键
            if (!empty($node['children'])) {
                $this->insertTree($node['children'], $item->id, $deep + 1);
            }
        }
    }
}
