<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-04-06 21:26:55
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-10 23:05:39
 * @FilePath: \app\Http\Controllers\TestController.php
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckArray;
use App\Rules\Pub\FormatTime;
use App\Rules\Pub\CheckUnique;
use App\Rules\Pub\ChineseCodeNumberLine;
use App\Events\TestEvent;
use App\Facade\LaravelFastApi\V1\Phone\Websocket\PhoneSocketFacade;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Facade\YouHuShop\V1\Admin\Goods\Attribute\AdminGoodsAttributeValueFacade;
use App\Facade\YouHuShop\V1\Admin\Goods\Spec\AdminGoodsSpecValueFacade;
use App\Facade\LaravelFastApi\V1\Admin\User\Admin\AdminPersonalFacade;
use App\Facade\Pub\V1\Qrcode\PubQrcodeFacade;
use App\Contract\LaravelFastApi\V1\Phone\Order\OrderPaymentHandlerContract;
use YouHuJun\Tool\App\Facade\V1\Calendar\CalendarFacade;
use App\Exceptions\Common\CommonException;
use App\Contract\LaravelFastApi\V1\Common\User\AddUserHandlerContract;

//use PhoneBanner;

class TestController extends Controller
{
    public function test(Request $request)
    {
        p(config('youhujun.publish'));
        //dd(config('common_code'));
        $param = [1,2,3];

        app(OrderPaymentHandlerContract::class)->handle($param);
    }

    /**
     * 获取身份证号码的后6位数字
     *
     * @param string $idCard 身份证号码字符串
     * @return string|false 返回身份证后6位，若参数无效则返回false
     */
    private function getLastSixDigits($idCard)
    {
        // 参数验证
        if (empty($idCard) || !is_string($idCard)) {
            return false;
        }

        // 获取字符串长度
        $length = strlen($idCard);

        // 验证身份证长度（标准身份证为18位）
        if ($length != 18) {
            return false;
        }

        // 截取后6位
        return substr($idCard, -6);
    }

    /**
     * 生成6位随机数字
     * @param bool $secure 是否使用更安全的生成方式
     * @return array 返回结果数组
     */
    private function generateRandomDigits($secure = true)
    {
        if ($secure) {
            // 使用更安全的random_int()
            $number = random_int(0, 999999);
        } else {
            // 使用mt_rand()，速度更快
            $number = mt_rand(0, 999999);
        }

        // 确保生成的是6位数，不足补0
        $result = str_pad($number, 6, '0', STR_PAD_LEFT);

        return $result;
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



        //$sevenDayTime = strtotime(now() . " + 7 days");

        //p($sevenDayTime->now());

        //PhoneLoginDouYinFacade::test();
    }

    public function testEvent()
    {
        //echo "测试事件";

        $admin = Admin::find(1);

        TestEvent::dispatch($admin);

        /* $order_id = 28;
        $transaction_id = '4200002390202410152324777768';
        $user_id = 16;
        $payer_total = 1;

         PayOrderEvent::dispatch($order_id,$transaction_id,$user_id,$payer_total);

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
                'user_id' => ['bail','nullable',new Numeric()],
                'send_type' => ['bail','nullable',new Numeric()],
                'event' => ['bail','nullable',new Numeric()],
            ],
            []
        );

        $validated = $validator->validated();

        ['user_id' => $user_id,'send_type' => $send_type,'event' => $event] = $validated + [];

        //p($validated);

        $data = [];
        //$data['user_id'] = $user->id;
        $data['user_id'] = isset($user_id) ? $user_id : 1;
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
