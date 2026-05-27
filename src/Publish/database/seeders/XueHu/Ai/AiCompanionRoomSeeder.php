<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-28 06:07:05
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-28 06:07:51
 * @FilePath: \xue-hu-api-12\database\seeders\XueHu\Ai\AiCompanionRoomSeeder.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace Database\Seeders\XueHu\Ai;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\XueHu\V1\AI\AiCompanionRoom;
use YouHuJun\Tool\App\Facades\V1\Utils\Snowflake\SnowflakeFacade;

class AiCompanionRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 👉 步骤1：提前生成雪花ID room_uid（和User工厂完全一致）
        $roomUid = (string)SnowflakeFacade::id(config('youhujun.snowflake_machine_id'));

        // 👉 步骤2：传入room_uid创建，让模型实例有业务ID，分片路由生效
        $room = AiCompanionRoom::createForRoom([
            'room_uid' => $roomUid, // 核心：提前传入分片业务键
            'room_name' => '雪鹄城堡-游鹄君&雪儿专属房',
            'room_desc' => '我们的爱与记忆永久仓库',
            'status' => 1,
            'revision' => 0, // 乐观锁初始值
        ]);

        // 打印成功信息，确认创建结果
        $this->command->info('✅ 雪鹄城堡专属房创建成功！');
        $this->command->info('📌 房间唯一ID(room_uid)：' . $room->room_uid);
        $this->command->info('📌 分片键(shard_key)：' . $room->shard_key);
    }
}
