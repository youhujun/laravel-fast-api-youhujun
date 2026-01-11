<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 10:17:17
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-10-08 16:54:24
 * @FilePath: \app\Http\Controllers\Phone\Notify\Pay\Wechat\NotifyController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\Notify\Pay\Wechat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckArray;
use App\Rules\Pub\FormatTime;
use App\Rules\Pub\CheckUnique;
use App\Rules\Pub\ChineseCodeNumberLine;
use App\Exceptions\Common\RuleException;

use App\Facade\LaravelFastApi\V1\Phone\Notify\PhonePayNotifyFacade;

class NotifyController extends Controller
{
	 /**
     * 获取默认选项
     */
    public function wechatJsPayNotify(Request $request)
    {

		//p($request->all());die;
		
        $inWechatpaySignature = $request->header('Wechatpay-Signature');
        $inWechatpayTimestamp = $request->header('Wechatpay-Timestamp');
        $inWechatpaySerial = $request->header('Wechatpay-Serial');
        $inWechatpayNonce = $request->header('Wechatpay-Nonce');
        $inBody = file_get_contents('php://input');

        $notifyData = [
			'wechatpay_signature' => $inWechatpaySignature,
			'wechatpay_timestamp' => $inWechatpayTimestamp,
			'wechatpay_serial' => $inWechatpaySerial,
			'wechatpay_nonce' => $inWechatpayNonce,
			'body' => $inBody
		];

        //p($notifyData);die;

        $result = PhonePayNotifyFacade::wechatJsPayNotify($notifyData);

        return $result;
    }


}
