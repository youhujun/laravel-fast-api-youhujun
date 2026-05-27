<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-17 12:19:11
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-05-20 17:46:56
 * @FilePath: \App\Services\Facade\Pub\V1\Store\QiNiuFacadeService.php
 */

namespace App\Services\Facade\Pub\V1\Store;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\Common\CommonException;
use YouHuJun\Tool\App\Facades\V1\Store\QiNiuFacade;
//引入鉴权类
use Qiniu\Auth as QiniuAuth;
// 引入上传类
use Qiniu\Storage\UploadManager;
//文件管理
use Qiniu\Storage\BucketManager;

/**
 * @see \App\Facades\Pub\V1\Store\QiNiuFacade
 */
class QiNiuFacadeService
{
    public function test()
    {
        echo "QiNiuFacadeService test";
    }

    private $accessKey;
    private $secretKey;
    protected $bucket;

    protected $auth;
    protected $uploadToken;
    protected $cdnUrl;


    public function __construct()
    {
        $this->accessKey = trim(Cache::get('qiniu.accessKey'));

        if (!$this->accessKey) {
            throw new CommonException('QiNiuAccessKeyError');
        }

        $this->secretKey = trim(Cache::get('qiniu.secretKey'));

        if (!$this->secretKey) {
            throw new CommonException('QiNiuSecretKeyError');
        }


        $this->bucket = trim(Cache::get('qiniu.bucket.default'));

        if (!$this->bucket) {
            throw new CommonException('QiNiuDefaultBucketError');
        }


        $this->cdnUrl = trim(Cache::get('qiniu.cdnUrl'));


        if (! $this->cdnUrl) {
            throw new CommonException('QiNiuCdnUrlError');
        }


        QiNiuFacade::init(
            $this->accessKey,
            $this->secretKey,
            $this->cdnUrl,
            7200  // 上传凭证有效期(秒)
        );
    }

    /**
     * 获取上传凭证
     */
    protected function getToken()
    {
        $this->bucket = trim(Cache::get('qiniu.bucket.default'));

        if (!$this->bucket) {
            throw new CommonException('QiNiuDefaultBucketError');
        }

        //上传凭证有效时间
        $expires = 7200;
        //覆盖上传的文件
        //$keyToOverwrite = 'qiniu.mp4';
        $keyToOverwrite = null;
        //自定义返回值
        //$policy = null;
        $returnBody = '{"key":"$(key)","hash":"$(etag)","fsize":$(fsize),"name":"$(fname)"}';
        $policy = array(
            'returnBody' => $returnBody
        );

        $this->uploadToken = $this->auth->uploadToken($this->bucket, $keyToOverwrite, $expires, $policy, true);
    }

    /**
     * 七牛云上传文件
     *
     * @param  [type] $filePath 文件路径
     * @param  [type] $savePath 保存路径
     */
    public function uploadFile($filePath, $savePath)
    {
        $result = QiNiuFacade::uploadFile(
            $filePath,
            $this->bucket,
            $savePath
        );

        return $result;
    }

    /**
     * 获取私有空间的链接
     * @param  [type] $savePath
     */
    public function getPrivateFileUrl($savePath)
    {
        $url = QiNiuFacade::getPrivateFileUrl($savePath);


        return $url;
    }

    /**
     * 获取公有链接
     */
    public function getPublicFileUrl($savePath)
    {
        $url = QiNiuFacade::getPublicFileUrl($savePath);

        return $url;
    }
}
