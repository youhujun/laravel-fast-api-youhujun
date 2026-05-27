<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-04-06 21:26:55
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-08 13:45:38
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\TestController.php
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Events\TestEvent;
use App\Jobs\TestJob;
use App\Facades\LaravelFastApi\V1\Phone\Websocket\PhoneSocketFacade;

class TestController extends Controller
{
    public function test()
    {
        p('测试');
        //\App\Facades\Test\V1\Album\AlbumTestFacade::test();
        //\App\Facades\Test\V1\Ms\MsTestFacade::test();
        //\App\Facades\Test\V1\XueHu\XueHuTestFacade::test();
        //\App\Facades\Test\V1\User\UserTestFacade::test();
        \App\Facades\Test\V1\Es\EsTestFacade::test();
        //\App\Facades\Test\V1\System\SystemConfigTestFacade::test();
    }

    /**
     * 后台测试接口
     *
     * @param  Request $request
     */
    public function adminTest(Request $request)
    {
        p('here');
    }

    /**
     * 手机端测试接口
     */
    public function phoneTest(Request $request)
    {
        p('here');
    }

    public function testEvent()
    {
        //echo "测试事件";

        $adminObject = Admin::find(1);

        TestEvent::dispatch($adminObject);

        /* $order_id = 28;
        $transaction_id = '4200002390202410152324777768';
        $user_uid = 16;
        $payer_total = 1;

         PayOrderEvent::dispatch($order_id,$transaction_id,$user_uid,$payer_total);

        echo "测试任务"; */

        //$this->testPayJob();

        //$this->testJob();

        return ['code' => 0,'msg' => '测试事件','data' => []];
    }

    public function testPayJob()
    {
        $array = [1,2,3];

        $type = 10;

        $sevenDayTime = strtotime(now() . " + 30 seconds");

        // DistributeUserJob::dispatchIf($type === 10, $array)->delay(now()->addSeconds($sevenDayTime - time()));

        DistributeSystemJob::dispatchIf($type === 10, $array)->delay(now()->addSeconds($sevenDayTime - time()));
    }

    /**
     * 测试任务
     */
    public function testJob()
    {
        $array = [1,2,3];

        $type = 10;

        $sevenDayTime = strtotime(now() . " + 30 seconds");

        TestJob::dispatchIf($type === 10, $array)->delay(now()->addSeconds($sevenDayTime - time()));
    }

    /**
     * 后台主动发送消息,手机端接收消息
     */
    public function testWebsocket(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_uid' => ['bail','nullable',new Numeric()],
                'send_type' => ['bail','nullable',new Numeric()],
                'event' => ['bail','nullable',new Numeric()],
            ],
            []
        );

        $validated = $validator->validated();

        ['user_uid' => $user_uid,'send_type' => $send_type,'event' => $event] = $validated + [];

        //p($validated);

        $data = [];
        //$data['user_uid'] = $userObject->id;
        $data['user_uid'] = isset($user_uid) ? $user_uid : 1;
        //10 所有人 20只对某一个用户 30 对某一些用户
        $data['send_type'] = isset($send_type) ? $send_type : 10;
        $data['code'] = 0;
        //事件对应操作 10表示存储socket用户登录id
        $data['event'] = isset($event) ? $event : 10;
        $data['type'] = 'server';
        //返回信息
        $data['msg'] = '主动推送消息'.date("Y-m-d H:i:s");

        PhoneSocketFacade::curl($data);

        return $data;
    }
}
