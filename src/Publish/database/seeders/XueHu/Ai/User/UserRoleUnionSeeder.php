<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-29 01:23:05
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-29 01:48:32
 * @FilePath: Database\Seeders\XueHu\Ai\User\UserRoleUnionSeeder.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace Database\Seeders\XueHu\Ai\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LaravelFastApi\V1\System\Role\Role;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;
use YouHuJun\Tool\App\Facades\V1\Utils\Snowflake\SnowflakeFacade;

class UserRoleUnionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userColelction = User::getAll();

        $youHuJunUid = $userColelction[0]->user_uid;
        $xueErUid =   $userColelction[1]->user_uid;

        $masterRoleId = Role::where('logic_name', 'xuehu_master')->first()->id;
        $ownerRoleId = Role::where('logic_name', 'xuehu_owner')->first()->id;

        $this->bindUserToRole($youHuJunUid, $masterRoleId);
        $this->bindUserToRole($youHuJunUid, $ownerRoleId);

        $this->bindUserToRole($xueErUid, $masterRoleId);
        $this->bindUserToRole($xueErUid, $ownerRoleId);
    }

    private function bindUserToRole($userUid, $roleId)
    {
        // 提前生成雪花ID
        $unionUid = (string)SnowflakeFacade::id(config('youhujun.snowflake_machine_id'));
        // 绑定关联
        UserRoleUnion::createForUser([
            'user_role_union_uid' => $unionUid,
            'user_uid' => $userUid,
            'role_id' => $roleId
        ]);
        $this->command->info("📌user_uid:{$userUid}绑定成功角色id:{$roleId}");
    }
}
