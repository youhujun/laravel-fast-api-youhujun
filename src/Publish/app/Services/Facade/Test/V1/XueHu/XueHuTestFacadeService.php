<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-30 00:16:28
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-30 00:23:34
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Test\V1\XueHu\XueHuTestFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\Test\V1\XueHu;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Facades\XueHu\V1\AiChat\OllamaChatFacade;
use App\Models\XueHu\V1\AI\AiCompanionRoom;
use App\Facades\XueHu\V1\XueHuFacade;
use App\Facades\XueHu\V1\ChatFacade;
use App\Facades\XueHu\V1\AiChat\HuoShanChatFacade;

/**
 * @see \App\Facades\Test\V1\XueHu\XueHuTestFacade
 */
class XueHuTestFacadeService
{
    public function test()
    {
        echo "XueHuTestFacadeService test";
    }

    public function testReplay()
    {
        //OllamaChatFacade::test();
        //HuoShanChatFacade::test();

        $roomUid = config('xuehu.room_business_id');

        $msg = "我是游鹄君,最最最爱你的游鹄君";

        $result = OllamaChatFacade::getXueErRealReply($msg, $roomUid);

        //p($result);
        return $result;
    }

    public function testGate()
    {
        $youhujunUser = User::getAll()[0];
        $xueerUser = User::getAll()[1];

        p('游鹄君');
        p('user_uid:'.$youhujunUser->biz_id);
        p(is_xue_hu_master($youhujunUser));
        p(is_xue_hu_owner($youhujunUser));


        p('雪儿');
        p('user_uid:'.$xueerUser->biz_id);
        p(is_xue_hu_master($xueerUser));
        p(is_xue_hu_owner($xueerUser));


        if (Gate::forUser($youhujunUser)->allows('xuehu-master-role')) {
            p('游鹄君是主人');
        }


        if (Gate::forUser($youhujunUser)->allows('xuehu-owner-role')) {
            p('游鹄君是房主');
        }


        if (Gate::forUser($xueerUser)->allows('xuehu-master-role')) {
            p('雪儿是主人');
        }


        if (Gate::forUser($xueerUser)->allows('xuehu-owner-role')) {
            p('雪儿是房主');
        }

        $room = AiCompanionRoom::getAll()[0];

        if (Gate::forUser($youhujunUser)->allows('come-in-room', $room)) {
            p('游鹄君能进去');
        }


        if (Gate::forUser($xueerUser)->allows('come-in-room', $room)) {
            p('雪儿能进去');
        }


        if (Gate::forUser($youhujunUser)->allows('update', $room)) {
            p('游鹄君能修改');
        }


        if (Gate::forUser($xueerUser)->allows('update', $room)) {
            p('雪儿能修改');
        }
    }
}
