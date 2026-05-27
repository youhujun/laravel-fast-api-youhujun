<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-06 20:33:33
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-12 18:08:36
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Pub\V1\Qrcode\PubQrcodeFacadeService.php
 */

namespace App\Services\Facade\Pub\V1\Qrcode;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use App\Models\LaravelFastApi\V1\User\User;
use App\Exceptions\Common\CommonException;
use YouHuJun\Tool\App\Facades\V1\Qrcode\QrcodeFacade;

/**
 * @see \App\Facades\Pub\V1\Qrcode\PubQrcodeFacade
 */
class PubQrcodeFacadeService
{
    public function test()
    {
        echo "PubQrcodeFacadeService test";
    }

    //跳转路径
    protected $redirectUrl;
    //logo路径
    protected $logoPath;
    //提示信息
    protected $noticeInfo;
    //二维码保存路径
    protected $qrcodePath;
    //生成二维码的模式
    // 1 保存二维码 2直接输出 3生成img标签url
    protected $mode;

    public function init(User $userObject)
    {
        $shareString = $userObject->invite_code;

        $configUrl = Cache::get('qrcode.redirectUrl');

        $this->redirectUrl = "{$configUrl}?share={$shareString}";

        $configLogopath = Cache::get('qrcode.logo');

        //设置默认logo图片
        $this->logoPath = storage_path()."/app/public/config/file/config/default_logo.png";

        if ($configLogopath) {
            $this->logoPath = storage_path()."/app/public/{$configLogopath}";
        }

        //如果开启了存储桶,就使用存储桶
        if (Cache::get('cloud.store')) {
            $this->logoPath = $configLogopath;
        }

        $configNoticeInfo = Cache::get('qrcode.noticeTitle');

        //设置默认提示信息
        $this->noticeInfo = "二维码";

        if ($configNoticeInfo) {
            $this->noticeInfo = $configNoticeInfo;
        }

        //确保该目录可以存在
        Storage::disk('public')->makeDirectory("/user/album/{$userObject->user_uid}");

        $this->qrcodePath = storage_path()."/app/public/user/album/{$userObject->user_uid}/{$userObject->user_uid}_qrcode.png";
    }

    /**
     * 生成用户二维码
     */
    public function makeQrcdoeWithUser(User $userObject, $mode = 1)
    {
        $this->init($userObject);

        $user_uid = $userObject->user_uid;
        $inviteCode = $userObject->invite_code;
        $shareUrl =  $this->redirectUrl;

        $configArray = [
            'logoPath' => $this->logoPath,
            'noticeInfo' => $this->noticeInfo,
            'qrcodePath' => $this->qrcodePath,
            'size' => 300,
        ];

        $paramsArray = [
            'data' => $shareUrl,
        ];

        if ($mode == 1) {
            // 保存到文件
            $savePath = QrcodeFacade::makeQrcode($configArray, $paramsArray, 1);
        }

        if ($mode == 2) {
            QrcodeFacade::makeQrcode($configArray, $paramsArray, 2);
        }

        if ($mode == 3) {
            $dataUri = QrcodeFacade::makeQrcode($configArray, $paramsArray, 3);
            return $dataUri;
        }
    }
}
