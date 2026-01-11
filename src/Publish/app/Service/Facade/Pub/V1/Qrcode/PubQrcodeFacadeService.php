<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-06 20:33:33
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-10 12:21:28
 * @FilePath: \app\Service\Facade\Pub\V1\Qrcode\PubQrcodeFacadeService.php
 */

namespace App\Service\Facade\Pub\V1\Qrcode;

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
use YouHuJun\Tool\App\Facade\V1\Qrcode\QrcodeFacade;

/**
 * @see \App\Facade\Pub\V1\Qrcode\PubQrcodeFacade
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

    public function init(User $user)
    {
        $shareString = $user->invite_code;

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
        Storage::disk('public')->makeDirectory("/user/album/{$user->id}");

        $this->qrcodePath = storage_path()."/app/public/user/album/{$user->id}/{$user->id}_qrcode.png";
    }

    /**
     * 生成用户二维码
     */
    public function makeQrcdoeWithUser(User $user, $mode = 1)
    {
        $this->init($user);

        $userId = $user->id;
        $inviteCode = $user->invite_code;
        $shareUrl =  $this->redirectUrl;

        $config = [
            'logoPath' => $this->logoPath,
            'noticeInfo' => $this->noticeInfo,
            'qrcodePath' => $this->qrcodePath,
            'size' => 300,
        ];

        $params = [
            'data' => $shareUrl,
        ];

        if ($mode == 1) {
            // 保存到文件
            $savePath = QrcodeFacade::makeQrcode($config, $params, 1);
        }

        if ($mode == 2) {
            QrcodeFacade::makeQrcode($config, $params, 2);
        }

        if ($mode == 3) {
            $dataUri = QrcodeFacade::makeQrcode($config, $params, 3);
            return $dataUri;
        }
    }
}
