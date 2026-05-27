<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-28 15:46:42
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-28 20:26:50
 * @FilePath: \xue-hu-api-12\database\seeders\XueHu\Ai\AiCompanionUserRoomUnionSeeder.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace Database\Seeders\XueHu\Ai;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\XueHu\V1\AI\AiCompanionUserRoomUnion;
use YouHuJun\Tool\App\Facades\V1\Utils\Snowflake\SnowflakeFacade;
use App\Models\XueHu\V1\AI\AiCompanionRoom;
use App\Models\LaravelFastApi\V1\User\User;

class AiCompanionUserRoomUnionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userColelction = User::getAll();

        $youHuJunUid = $userColelction[0]->user_uid;
        $xueErUid =   $userColelction[1]->user_uid;

        $roomObject = AiCompanionRoom::where('status', 1)->first();

        $xueHuRoomUid = $roomObject->room_uid;
        // 1. 绑定游鹄君到雪鹄城堡（房主）
        $this->bindUserToRoom($youHuJunUid, $xueHuRoomUid, '游鹄君');
        // 2. 绑定雪儿到雪鹄城堡（房主）
        $this->bindUserToRoom($xueErUid, $xueHuRoomUid, '雪儿');

        $this->command->info('✅ 游鹄君&雪儿成功绑定到雪鹄城堡专属房！从此这个房间只属于我们俩～');
    }

    /**
     * 通用绑定方法：用户ID → 房间ID
     */
    private function bindUserToRoom($userUid, $roomUid, $userName)
    {
        // 提前生成雪花ID
        $unionUid = (string)SnowflakeFacade::id(config('youhujun.snowflake_machine_id'));
        // 绑定关联
        AiCompanionUserRoomUnion::createForRoom([
            'union_uid' => $unionUid,
            'user_uid' => $userUid,
            'room_uid' => $roomUid,
            'status' => 1, // 有效
            'revision' => 0,
        ]);
        $this->command->info("📌 {$userName} 绑定成功！user_uid：{$userUid}");
    }
}
