<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-20 23:10:20
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 01:56:54
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\File\AdminUploadFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\File;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use App\Events\LaravelFastApi\V1\Admin\File\UploadFileEvent;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadConfigFileDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadFileDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadSinglePicutureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadMultiplePicutureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadUserAvatarDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadResetPictureDTO;
//模型
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Facades\Pub\V1\Store\QiNiuFacade;

//响应
use App\Http\Resources\LaravelFastApi\V1\Db\Admin\System\Picture\AlbumPictureResource;

/**
 * @see App\Http\Controllers\LaravelFastApi\V1\Admin\File\UploadController
 * @see \App\Facades\LaravelFastApi\V1\Admin\File\AdminUploadFacade
 */
class AdminUploadFacadeService
{
    public function test()
    {
        echo "AdminUploadFacadeService test";
    }

    protected $adminId = 0;

    //根据前端 $validate['use_type] 来决定配置还是管理员配置还是用户\
    protected static $adminTypeDirectory = [
        //系统配置
        '10' => DIRECTORY_SEPARATOR.'config'. DIRECTORY_SEPARATOR.'file'. DIRECTORY_SEPARATOR,
        // 后台管理员
        '20' => DIRECTORY_SEPARATOR.'admin'. DIRECTORY_SEPARATOR.'file'. DIRECTORY_SEPARATOR,
        //用户
        '30' => DIRECTORY_SEPARATOR.'user'. DIRECTORY_SEPARATOR.'file'. DIRECTORY_SEPARATOR
    ];

    //文件存储路径
    protected $filePath;

    //动作类型 配置文件导入,例如银行导入 Bank
    protected $actionType;

    /**
     * 处理文件存储路径
     *
     * @param [type] $validated
     * @return void
     */
    protected function makeSavePath(array $validated, Admin $adminObject, mixed $file)
    {
        //文件使用性质类型
        $useType = isset($validated['use_type']) ? $validated['use_type'] : null;
        //文件格式类型
        $fileType = isset($validated['file_type']) ? $validated['file_type'] : null;
        //文件存储目录
        $savePath = isset($validated['save_path']) ? $validated['save_path'] : null;

        //后缀名 xlsx
        $file_extension = $file->getClientOriginalExtension();

        //定义拼接目录 默认使用自动获取的文件名后缀
        $joinPath = $file_extension;

        //如果设置了文件格式类型 二级优先采用 文件格式目录
        if (isset($fileType) && !empty($fileType)) {
            $joinPath = $fileType;
        }

        //如果设置了存储目录  一级优先采用文件存储目录
        if (isset($savePath) && !empty($savePath)) {
            $joinPath = $savePath;
        }

        //部分路径 \user\file1\
        $this->filePath = self::$adminTypeDirectory[$useType].$joinPath;

        //如果不是平台配置 需要加上用户id
        if ($useType != 10) {
            $this->filePath = self::$adminTypeDirectory[$useType].$adminObject->biz_id.DIRECTORY_SEPARATOR.$joinPath;
        }
    }

    /**
     * 后台上传配置文件
     *
     * @param [type] $validated  [use_type,]
     * @param [type] $adminObject
     * @param [type] $file
     * @return void
     */
    public function uploadConfigFile(Admin $adminObject, UploadConfigFileDTO $uploadConfigFileDTO, mixed $file)
    {
        $result = code(config('admin_code.UploadConfigFileError'));

        if (!$file->isValid()) {
            throw new CommonException('uploadConfigFileAllowError');
        }

        $validated = $uploadConfigFileDTO->toArray();

        //处理保存路径
        $this->makeSavePath($validated, $adminObject, $file);

        //后缀名
        $fileType = $file->getClientOriginalExtension();
        //文件名带后缀 template.xlsx
        $file_file = $file->getClientOriginalName();

        //全路径  storage/app/public下
        // config/file/pem/apiclient_key.pem
        // admin/file/4/pem//apiclient_key.pem
        $path = $file->storeAs($this->filePath, $file_file, 'public');

        //保存玩以后再检测
        $exists = Storage::disk('public')->exists($path);

        if (!$exists) {
            throw new CommonException('UploadConfigFileSaveError');
        }

        $this->actionType = isset($validated['type']) ? $validated['type'] : '';

        //上传文件数据容器
        $uploadFileLogDataArray = [];
        //文件名 template
        $uploadFileLogDataArray['admin_uid'] = $adminObject->biz_id;
        $uploadFileLogDataArray['use_type'] = $validated['use_type'];
        $uploadFileLogDataArray['file_path'] = $this->filePath;
        $uploadFileLogDataArray['file_extension'] = $fileType;
        $uploadFileLogDataArray['file_name'] = pathinfo($path, \PATHINFO_FILENAME);
        $uploadFileLogDataArray['file'] = $file_file;

        //记录事件日志
        CommonEvent::dispatch($adminObject, $uploadFileLogDataArray, 'UploadConfigFile');

        /**
         * $path 文件位置
         * $this->$actionType 操作类型 例如 bank配合后端导入数据
         * $fileType 后缀名
         */
        UploadFileEvent::dispatch($adminObject, $uploadFileLogDataArray, $path, $fileType, $this->actionType);

        $result = code(['code' => 0,'msg' => '上传配置文件成功!'], ['data' => $path]);

        return $result;
    }

    /**
     * 上传文件
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @param [type] $file
     * @return void
     */
    public function uploadFile(Admin $adminObject, UploadFileDTO $uploadFileDTO, mixed $file)
    {
        $result = code(config('admin_code.UploadFileError'));

        if (!$file->isValid()) {
            throw new CommonException('UploadFileAllowError');
        }

        $validated = $uploadFileDTO->toArray();

        //处理保存路径
        $this->makeSavePath($validated, $adminObject, $file);

        //后缀名
        $fileType = $file->getClientOriginalExtension();
        //文件名带后缀 template.xlsx
        $file_file = $file->getClientOriginalName();

        //全路径  storage/app/public下
        // config/file/pem/apiclient_key.pem
        // admin/file/4/pem//apiclient_key.pem
        $path = $file->storeAs($this->filePath, $file_file, 'public');

        //保存玩以后再检测
        $exists = Storage::disk('public')->exists($path);

        if (!$exists) {
            throw new CommonException('UploadFileSaveError');
        }

        $this->actionType = isset($validated['type']) ? $validated['type'] : '';

        //上传文件数据容器
        $uploadFileLogDataArray = [];
        //文件名 template
        $uploadFileLogDataArray['admin_uid'] = $adminObject->biz_id;
        $uploadFileLogDataArray['use_type'] = $validated['use_type'];
        $uploadFileLogDataArray['file_path'] = $this->filePath;
        $uploadFileLogDataArray['file_extension'] = $fileType;
        $uploadFileLogDataArray['file_name'] = pathinfo($path, \PATHINFO_FILENAME);
        $uploadFileLogDataArray['file'] = $file_file;

        //记录事件日志
        CommonEvent::dispatch($adminObject, $uploadFileLogDataArray, 'UploadFile');
        /**
         * $path 文件位置
         * $this->$actionType 操作类型 例如 bank配合后端导入数据
         * $fileType 后缀名
         */
        UploadFileEvent::dispatch($adminObject, $uploadFileLogDataArray, $path, $fileType, $this->actionType);

        $result = code(['code' => 0,'msg' => '上传文件成功!'], ['data' => asset('storage/'.$path)]);

        return $result;
    }

    /**
     * 单图上传
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @param [type] $pictures
     * @return void
     */
    public function UploadSinglePicture(Admin $adminObject, UploadSinglePicutureDTO $uploadSinglePicutureDTO, mixed $picture)
    {
        $result = code(config('admin_code.UploadSinglePictureError'));

        $validated = $uploadSinglePicutureDTO->toArray();

        $album_uid = $this->findAdminDefaultAlbum($validated['album_uid'], $adminObject);

        $admin_uid = $adminObject->biz_id;

        if (!$picture->isValid()) {
            throw new CommonException('UploadSinglePictureAllowError');
        }

        //处理保存路径
        $this->makeSavePath($validated, $adminObject, $picture);
        //后缀名
        $fileType = $picture->getClientOriginalExtension();
        //文件名带后缀 template.xlsx
        $file_file = $picture->getClientOriginalName();
        //全路径  storage/app/public下
        // config/file/pem/apiclient_key.pem
        // admin/file/4/pem//apiclient_key.pem
        $path = $picture->storeAs($this->filePath, $file_file, 'public');
        //保存以后再检测
        $exists = Storage::disk('public')->exists($path);

        if (!$exists) {
            throw new CommonException('UploadSinglePictureSaveError');
        }

        $cloudStore = Cache::get('cloud.store');

        if ($cloudStore == 'qiniu') {
            QiNiuFacade::uploadFile(public_path('storage/'.$path), 'storage'.$this->filePath.$file_file);
        }

        $picture_info = getimagesize(storage_path('app/public/').$path);
        $picture_name = \pathinfo($path, \PATHINFO_FILENAME);

        $picture_size = round(filesize(storage_path('app/public/').$path) / 1024, 0);
        $picture_spec = "{$picture_info[0]}×{$picture_info[1]}";

        //声明数据容器
        $insertDataArray = [];

        $insertDataArray = [
            'album_picture_uid' => get_snow_flake_id(),
            'admin_uid' => $admin_uid,
            'user_uid' => $adminObject->user_uid,
            'album_uid' => $album_uid,
            'picture_name' => $picture_name,
            'picture_file' => $file_file,
            'picture_path' => $this->filePath,
            'picture_size' => $picture_size,
            'picture_spec' => $picture_spec,
            'picture_type' => 10,
            'created_at' => date('Y-m-d H:i:s', time()),
            'created_time' => time(),
        ];

        if ($cloudStore == 'qiniu') {
            $cdnUrl = trim(Cache::get('qiniu.cdn_url'));

            if (!$cdnUrl) {
                throw new CommonException('QiNiuCdnUrlError');
            }

            $insertDataArray['picture_type'] = 20;
            $insertDataArray['picture_url'] = $cdnUrl.'/storage'.$this->filePath.$file_file;
        }

        $albumPictureObject = ShardHelperFacade::createWithShard(AlbumPicture::class, $album_uid, $insertDataArray);

        if (!$albumPictureObject) {
            throw new CommonException('UploadSinglePictureSqlSaveError');
        }


        $indexName = config('common_es.indices.album.album_pictures');

        $configKey = get_shard_config_key();

        $dataArray = [
            '_docId' =>  $albumPictureObject->album_picture_uid,
            'shard_key' => $albumPictureObject->album_uid,
            'album_picture_uid' => $albumPictureObject->album_picture_uid,
            'shard_db' => ShardFacade::getDbName($albumPictureObject->album_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($albumPictureObject->album_uid, 'album_pictures', $configKey),
            'admin_uid' => $albumPictureObject->admin_uid,
            'user_uid' => $albumPictureObject->user_uid,
            'album_uid' => $albumPictureObject->album_uid,
            'picture_name' => $albumPictureObject->picture_name,
            'picture_file' => $albumPictureObject->picture_file,
            'picture_path' => $albumPictureObject->picture_path,
            'picture_size' => $albumPictureObject->picture_size,
            'picture_spec' => $albumPictureObject->picture_spec,
            'picture_type' => $albumPictureObject->picture_type,
            'picture_url' => $albumPictureObject->picture_url,
            'created_at' => $albumPictureObject->created_at,
            'updated_at' => $albumPictureObject->updated_at,
            'deleted_at' => $albumPictureObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $dataArray, $albumPictureObject->album_picture_uid);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加相册图片失败','$esResult' => $esResult], 'AdminUploadFacadeService', 'UploadSinglePictureError');
            throw new CommonException('EsUploadSinglePictureSaveError');
        }

        $this->actionType = isset($validated['type']) ? $validated['type'] : '';

        //上传文件数据容器
        $uploadFileLogDataArray = [];
        //文件名 template
        $uploadFileLogDataArray['admin_uid'] = $adminObject->biz_id;
        $uploadFileLogDataArray['use_type'] = $validated['use_type'];
        $uploadFileLogDataArray['file_path'] = $this->filePath;
        $uploadFileLogDataArray['file_extension'] = $fileType;
        $uploadFileLogDataArray['file_name'] = pathinfo($path, \PATHINFO_FILENAME);
        $uploadFileLogDataArray['file'] = $file_file;

        CommonEvent::dispatch($adminObject, $insertDataArray, 'UploadSinglePicture');

        /**
         * $path 文件位置
         * $this->$actionType 操作类型 例如 bank配合后端导入数据
         * $fileType 后缀名
         */
        UploadFileEvent::dispatch($adminObject, $uploadFileLogDataArray, $path, $fileType, $this->actionType);

        $result = code(['code' => 0,'msg' => '上传单图片成功!'], ['data' => new AlbumPictureResource($albumPictureObject)]);

        return $result;
    }

    /**
     * 多图上传
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @param [type] $pictures
     * @return void
     */
    public function uploadMultiplePicture(Admin $adminObject, UploadMultiplePicutureDTO $uploadMultiplePicutureDTO, mixed $pictures)
    {
        $result = code(config('admin_code.UploadMultiplePictureError'));

        $validated = $uploadMultiplePicutureDTO->toArray();

        $album_uid = $this->findAdminDefaultAlbum($uploadMultiplePicutureDTO->album_uid, $adminObject);

        $admin_uid = $adminObject->biz_id;

        //声明图片容器
        $insertDataArray = [];

        //上传文件数据容器
        $uploadFileLogDataArray = [];

        foreach ($pictures as $k => $picture) {
            if (!$picture->isValid()) {
                throw new CommonException('UploadMultiplePictureAllowError');
            }

            //处理保存路径
            $this->makeSavePath($validated, $adminObject, $picture);
            //后缀名
            $fileType = $picture->getClientOriginalExtension();
            //文件名带后缀 template.xlsx
            $file_file = $picture->getClientOriginalName();
            //全路径  storage/app/public下
            // config/file/pem/apiclient_key.pem
            // admin/file/4/pem//apiclient_key.pem
            $path = $picture->storeAs($this->filePath, $file_file, 'public');
            //保存以后再检测
            $exists = Storage::disk('public')->exists($path);

            if (!$exists) {
                throw new CommonException('UploadMultiplePictureSaveError');
            }

            $cloudStore = Cache::get('cloud.store');

            if ($cloudStore == 'qiniu') {
                QiNiuFacade::uploadFile(public_path('storage/'.$path), 'storage'.$this->filePath.$file_file);
            }

            $picture_info = getimagesize(storage_path('app/public/').$path);
            $picture_name = \pathinfo($path, \PATHINFO_FILENAME);

            $picture_size = round(filesize(storage_path('app/public/').$path) / 1024, 0);
            $picture_spec = "{$picture_info[0]}×{$picture_info[1]}";

            $insertDataArray[$k] = [
                'album_picture_uid' => get_snow_flake_id(),
                'admin_uid' => $admin_uid,
                'album_uid' => $album_uid,
                'user_uid' => $adminObject->user_uid,
                'picture_name' => $picture_name,
                'picture_file' => $file_file,
                'picture_path' => $this->filePath,
                'picture_size' => $picture_size,
                'picture_spec' => $picture_spec,
                'picture_type' => 10,
                'created_at' => date('Y-m-d H:i:s', time()),
                'created_time' => time(),
            ];

            if ($cloudStore == 'qiniu') {
                $cdnUrl = trim(Cache::get('qiniu.cdn_url'));

                if (!$cdnUrl) {
                    throw new CommonException('QiNiuCdnUrlError');
                }

                $insertDataArray[$k]['picture_type'] = 20;
                $insertDataArray[$k]['picture_url'] = $cdnUrl.'/storage'.$this->filePath.$file_file;
            }

            //文件名 template
            $uploadFileLogDataArray[$k]['admin_upload_file_log_uid'] = get_snow_flake_id();
            $uploadFileLogDataArray[$k]['admin_uid'] = $adminObject->biz_id;
            $uploadFileLogDataArray[$k]['use_type'] = $validated['use_type'];
            $uploadFileLogDataArray[$k]['file_path'] = $this->filePath;
            $uploadFileLogDataArray[$k]['file_extension'] = $fileType;
            $uploadFileLogDataArray[$k]['file_name'] = pathinfo($path, \PATHINFO_FILENAME);
            $uploadFileLogDataArray[$k]['file'] = $file_file;
        }

        $insertResult = ShardHelperFacade::insertBatchWithShard(AlbumPicture::class, $insertDataArray, 'album_uid');

        if (!$insertResult) {
            throw new CommonException('UploadMultiplePictureSqlSaveError');
        }

        $indexName = config('common_es.indices.album.album_pictures');

        $configKey = get_shard_config_key();

        // 计算总分片数：库数 × 表数
        $dbCount = config($configKey.'.shard.db_count');
        $tableCount = config($configKey.'.shard.table_count');
        $shardTotal = (int) bcmul($dbCount, $tableCount);


        foreach ($insertDataArray as $key => &$insertData) {
            // 计算 shard_key
            $shardKey = strval($insertData['album_uid'] % $shardTotal);
            $insertData = [
                '_docId' =>  $insertData['album_picture_uid'],
                'shard_key' => $shardKey,
                'album_picture_uid' => $insertData['album_picture_uid'],
                'shard_db' => ShardFacade::getDbName($insertData['album_uid'], $configKey),
                'shard_table' => ShardFacade::getTableName($insertData['album_uid'], 'album_pictures', $configKey),
                'admin_uid' => $insertData['admin_uid'],
                'user_uid' => $insertData['user_uid'],
                'album_uid' => $insertData['album_uid'],
                'picture_name' => $insertData['picture_name'],
                'picture_file' => $insertData['picture_file'],
                'picture_path' => $insertData['picture_path'],
                'picture_size' => $insertData['picture_size'],
                'picture_spec' => $insertData['picture_spec'],
                'picture_type' => $insertData['picture_type'],
                'picture_url' => '',
                'created_at' => $insertData['created_at'],
                'updated_at' => null,
                'deleted_at' => null
            ];
        }

        $esResult = EsFacade::batchActDoc($indexName, $insertDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es批量添加相册图片失败','$esResult' => $result], 'AdminUploadFacadeService', 'uploadMultiplePictureError');
             throw new CommonException('EsUploadSinglePictureSaveError');
        }

        $this->actionType = isset($validated['type']) ? $validated['type'] : '';

        CommonEvent::dispatch($adminObject, $insertDataArray, 'UploadMultiplePicture');

        /**
         * $path 文件位置
         * $this->$actionType 操作类型 例如 bank配合后端导入数据
         * $fileType 后缀名
         */
        UploadFileEvent::dispatch($adminObject, $uploadFileLogDataArray, $path, $fileType, $this->actionType, 20);

        $pictureListColelction = collect($insertDataArray)->map(function ($item) {
            $model = new AlbumPicture();
            $model->fill($item);
            return $model;
        });

        $result = code(['code' => 0,'msg' => '上传多图成功!'], ['data' => AlbumPictureResource::collection($pictureListColelction)]);

        return $result;
    }


    /**
     * 上传头像
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @param [type] $picture
     * @return void
     */
    public function uploadUserAvatar(Admin $adminObject, UploadUserAvatarDTO $uploadUserAvatarDTO, mixed $picture)
    {
        $result = code(config('admin_code.UploadUserAvatarError'));

        $validated = $uploadUserAvatarDTO->toArray();

        $admin_uid = $adminObject->biz_id;

        //p($admin_uid);die;

        $album_uid = $validated['album_uid'];
        $user_uid = $validated['user_uid'];

        //p($user_uid);die;

        //如果没有相册id 就把图片存入到默用户认相册下
        if ($album_uid == 0 || !isset($album_uid)) {
            $album_uid = get_user_album_uid($user_uid);
        }

        if (!$picture->isValid()) {
            throw new CommonException('UploadSinglePictureAllowError');
        }

        //处理保存路径
        $this->makeSavePath($validated, $adminObject, $picture);
        //后缀名
        $fileType = $picture->getClientOriginalExtension();
        //文件名带后缀 template.xlsx
        $file_file = $picture->getClientOriginalName();
        //全路径  storage/app/public下
        // config/file/pem/apiclient_key.pem
        // admin/file/4/pem//apiclient_key.pem
        $path = $picture->storeAs($this->filePath, $file_file, 'public');
        //保存以后再检测
        $exists = Storage::disk('public')->exists($path);

        if (!$exists) {
            throw new CommonException('UploadSinglePictureSaveError');
        }

        $cloudStore = Cache::get('cloud.store');

        if ($cloudStore == 'qiniu') {
            QiNiuFacade::uploadFile(public_path('storage/'.$path), 'storage'.$this->filePath.$file_file);
        }

        $picture_info = getimagesize(storage_path('app/public/').$path);
        $picture_name = \pathinfo($path, \PATHINFO_FILENAME);

        $picture_size = round(filesize(storage_path('app/public/').$path) / 1024, 0);
        $picture_spec = "{$picture_info[0]}×{$picture_info[1]}";

        //声明数据容器
        $insertDataArray = [];

        $insertDataArray = [
            'album_picture_uid' => get_snow_flake_id(),
            'admin_uid' => 0,
            'user_uid' => $user_uid,
            'album_uid' => $album_uid,
            'picture_name' => $picture_name,
            'picture_file' => $file_file,
            'picture_path' => $this->filePath,
            'picture_size' => $picture_size,
            'picture_spec' => $picture_spec,
            'picture_type' => 10,
            'picture_url' => ''
        ];

        if ($cloudStore == 'qiniu') {
            $cdnUrl = trim(Cache::get('qiniu.cdn_url'));

            if (!$cdnUrl) {
                throw new CommonException('QiNiuCdnUrlError');
            }

            $insertDataArray['picture_type'] = 20;
            $insertDataArray['picture_url'] = $cdnUrl.'/storage'.$this->filePath.$file_file;
        }

        $albumPictureObject = ShardHelperFacade::createWithShard(AlbumPicture::class, $album_uid, $insertDataArray);

        if (!$albumPictureObject) {
            throw new CommonException('UploadSinglePictureSqlSaveError');
        }

        $indexName = config('common_es.indices.album.album_pictures');

        $configKey = get_shard_config_key();

        $dataArray = [
            '_docId' =>  $albumPictureObject->album_picture_uid,
            'shard_key' => $albumPictureObject->album_uid,
            'album_picture_uid' => $albumPictureObject->album_picture_uid,
            'shard_db' => ShardFacade::getDbName($albumPictureObject->album_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($albumPictureObject->album_uid, 'album_pictures', $configKey),
            'admin_uid' => $albumPictureObject->admin_uid,
            'user_uid' => $albumPictureObject->user_uid,
            'album_uid' => $albumPictureObject->album_uid,
            'picture_name' => $albumPictureObject->picture_name,
            'picture_file' => $albumPictureObject->picture_file,
            'picture_path' => $albumPictureObject->picture_path,
            'picture_size' => $albumPictureObject->picture_size,
            'picture_spec' => $albumPictureObject->picture_spec,
            'picture_type' => $albumPictureObject->picture_type,
            'picture_url' => $albumPictureObject->picture_url,
            'created_at' => $albumPictureObject->created_at,
            'updated_at' => $albumPictureObject->updated_at,
            'deleted_at' => $albumPictureObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $dataArray, $albumPictureObject->album_picture_uid);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加相册图片失败','$esResult' => $esResult], 'AdminUploadFacadeService', 'uploadUserAvatarError');
            throw new CommonException('EsUploadSinglePictureSaveError');
        }

        //先把原来的默认头像改为非默认
        $oldDefaultAvatarObject = UserAvatar::queryByShard($user_uid)->where('user_uid', $user_uid)->where('is_default', 1)->first();

        if ($oldDefaultAvatarObject) {
            $oldDefaultAvatarObject->is_default = 0;
            $oldDefaultAvatarObject->save();
        }

        $newUserAvatarArray = [
            'user_uid' => $user_uid,
            'album_picture_id' => $albumPictureObject->album_picture_uid,
            'created_at' => date('Y-m-d H:i:s', time()),
            'created_time' => time(),
            'is_default' => 1,
        ];

        $newUserAatarObject = ShardHelperFacade::createWithShard(UserAvatar::class, $user_uid, $newUserAvatarArray);

        if (!$newUserAatarObject) {
            throw new CommonException('UploadUserAvatarSaveError');
        }

        //清理与头像相关的缓存
        $AdminRedisKey = config('common_redis.admin_info.key');
        $AdminRedisField = config('common_redis.admin_info.field');
        $UserRedisKey = config('common_redis.user_info.key');
        $UserRedisField = config('common_redis.user_info.field');

        Redis::hdel($AdminRedisKey, $AdminRedisField.$user_uid);
        Redis::hdel($UserRedisKey, $UserRedisField.$user_uid);


        $this->actionType = isset($validated['type']) ? $validated['type'] : '';

        //上传文件数据容器
        $uploadFileLogDataArray = [];
        //文件名 template
        $uploadFileLogDataArray['admin_uid'] = $adminObject->biz_id;
        $uploadFileLogDataArray['use_type'] = $validated['use_type'];
        $uploadFileLogDataArray['file_path'] = $this->filePath;
        $uploadFileLogDataArray['file_extension'] = $fileType;
        $uploadFileLogDataArray['file_name'] = pathinfo($path, \PATHINFO_FILENAME);
        $uploadFileLogDataArray['file'] = $file_file;

        CommonEvent::dispatch($adminObject, $insertDataArray, 'UploadUserAvatar');

        /**
         * $path 文件位置
         * $this->$actionType 操作类型 例如 bank配合后端导入数据
         * $fileType 后缀名
         */
        UploadFileEvent::dispatch($adminObject, $uploadFileLogDataArray, $path, $fileType, $this->actionType);

        $result = code(['code' => 0,'msg' => '上传头像成功!'], ['data' => new AlbumPictureResource($albumPictureObject)]);

        return $result;
    }

    /**
     * 上传替换图片
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @param [type] $pictures
     * @return void
     */
    public function uploadResetPicture(Admin $adminObject, UploadResetPictureDTO $uploadResetPictureDTO, mixed $picture)
    {
        $result = code(config('admin_code.UploadResetPictureError'));

        $validated = $uploadResetPictureDTO->toArray();

        $album_uid = $validated['album_uid'];

        $admin_uid = $adminObject->biz_id;

        //如果没有相册id 就把图片存入到管理员默认相册下
        if ($album_uid == 0 || !isset($album_uid)) {
            $album_uid = get_admin_album_uid($admin_uid);
        }

        $admin_uid = $adminObject->biz_id;

        if (!$picture->isValid()) {
            throw new CommonException('UploadSinglePictureAllowError');
        }

        //处理保存路径
        $this->makeSavePath($validated, $adminObject, $picture);
        //后缀名
        $fileType = $picture->getClientOriginalExtension();
        //文件名带后缀 template.xlsx
        $file_file = $picture->getClientOriginalName();
        //全路径  storage/app/public下
        // config/file/pem/apiclient_key.pem
        // admin/file/4/pem//apiclient_key.pem
        $path = $picture->storeAs($this->filePath, $file_file, 'public');
        //保存以后再检测
        $exists = Storage::disk('public')->exists($path);

        if (!$exists) {
            throw new CommonException('UploadSinglePictureSaveError');
        }

        $cloudStore = Cache::get('cloud.store');

        if ($cloudStore == 'qiniu') {
            QiNiuFacade::uploadFile(public_path('storage/'.$path), 'storage'.$this->filePath.$file_file);
        }

        $picture_info = getimagesize(storage_path('app/public/').$path);
        $picture_name = \pathinfo($path, \PATHINFO_FILENAME);

        $picture_size = round(filesize(storage_path('app/public/').$path) / 1024, 0);
        $picture_spec = "{$picture_info[0]}×{$picture_info[1]}";


        $picture_uid = $validated['picture_uid'];

        $indexName = config('common_es.indices.album.album_pictures');

        $esAlbumPictureObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('album_picture_uid',$picture_uid)->get()->first();

        //p($esAlbumPictureObject);die;

        if (!isset($esAlbumPictureObject->album_picture_uid) || !$esAlbumPictureObject->album_picture_uid) {
            throw new CommonException('EsFindPictureError');
        }

        $oldPictureObject = AlbumPicture::queryByShard($esAlbumPictureObject->album_uid)->where('album_picture_uid', $picture_uid)->first();

        if (!isset($oldPictureObject->biz_id)) {
            throw new CommonException('FindPictureError');
        }

        $updateDataArray = [
            'album_picture_uid' => $oldPictureObject->biz_id,
            'album_uid' => $oldPictureObject->album_uid,
            'picture_file' => $file_file,
            'picture_path' => $this->filePath,
            'picture_size' => $picture_size,
            'picture_spec' => $picture_spec,
            'picture_type' => 10,
            'picture_url' => ''
        ];

        if ($cloudStore == 'qiniu') {
            $cdnUrl = trim(Cache::get('qiniu.cdn_url'));

            if (!$cdnUrl) {
                throw new CommonException('QiNiuCdnUrlError');
            }

            $updateDataArray['picture_type'] = 20;
            $updateDataArray['picture_url'] = $cdnUrl.'/storage'.$this->filePath.$file_file;
        }

        $updateResult = $oldPictureObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('UploadResetPictureSqlError');
        }

        $newAlbumPictureObject = new AlbumPicture();

        $newAlbumPictureObject->fill($updateDataArray);

        $indexName = config('common_es.indices.album.album_pictures');

        $configKey = get_shard_config_key();

        $dataArray = [
            '_docId' =>  $newAlbumPictureObject->album_picture_uid,
            'shard_key' => $newAlbumPictureObject->album_uid,
            'album_picture_uid' => $newAlbumPictureObject->album_picture_uid,
            'shard_db' => ShardFacade::getDbName($newAlbumPictureObject->album_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($newAlbumPictureObject->album_uid, 'album_pictures', $configKey),
            'admin_uid' => $newAlbumPictureObject->admin_uid,
            'user_uid' => $newAlbumPictureObject->user_uid,
            'album_uid' => $newAlbumPictureObject->album_uid,
            'picture_name' => $newAlbumPictureObject->picture_name,
            'picture_file' => $newAlbumPictureObject->picture_file,
            'picture_path' => $newAlbumPictureObject->picture_path,
            'picture_size' => $newAlbumPictureObject->picture_size,
            'picture_spec' => $newAlbumPictureObject->picture_spec,
            'picture_type' => $newAlbumPictureObject->picture_type,
            'picture_url' => $newAlbumPictureObject->picture_url,
            'created_at' => $newAlbumPictureObject->created_at,
            'updated_at' => $newAlbumPictureObject->updated_at,
            'deleted_at' => $newAlbumPictureObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $dataArray, $newAlbumPictureObject->album_picture_uid);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加相册图片失败','$esResult' => $esResult], 'AdminUploadFacadeService', 'uploadResetPictureError');
            throw new CommonException('EsUploadSinglePictureSaveError');
        }

        $this->actionType = isset($validated['type']) ? $validated['type'] : '';

        //上传文件数据容器
        $uploadFileLogDataArray = [];
        //文件名 template
        $uploadFileLogDataArray['admin_uid'] = $adminObject->biz_id;
        $uploadFileLogDataArray['use_type'] = $validated['use_type'];
        $uploadFileLogDataArray['file_path'] = $this->filePath;
        $uploadFileLogDataArray['file_extension'] = $fileType;
        $uploadFileLogDataArray['file_name'] = pathinfo($path, \PATHINFO_FILENAME);
        $uploadFileLogDataArray['file'] = $file_file;

        CommonEvent::dispatch($adminObject, ['picture_uid' => $picture_uid,'data' => $updateDataArray], 'UploadResetPicture');

        /**
         * $path 文件位置
         * $this->$actionType 操作类型 例如 bank配合后端导入数据
         * $fileType 后缀名
         */
        UploadFileEvent::dispatch($adminObject, $uploadFileLogDataArray, $path, $fileType, $this->actionType);

        $result = code(['code' => 0,'msg' => '图片替换上传成功!'], ['data' => new AlbumPictureResource($newAlbumPictureObject)]);

        return $result;
    }


    /**
     * 检测用户的相册id是否合法,不合法就替换为用户的默认相册
     *
     * @param  [type] $album_uid
     * @param  [type] $adminObject
     */
    private function findAdminDefaultAlbum(?string $album_uid = null, Admin $adminObject)
    {
        $admin_uid = $adminObject->biz_id;
        //如果没有相册id 就把图片存入到该用户默认相册下
        if ($album_uid == 0 || !isset($album_uid) || $album_uid == null) {
            $album_uid = get_admin_album_uid($admin_uid);
        } elseif ($album_uid == 1) {
            //如果相册id为1 是config系统相册
            $album_uid = get_system_album_uid();
            //判断用户是否是相册管理员,不是的话就要修改相册uid
            if (!is_album_admin($adminObject)) {
                $album_uid = get_admin_album_uid($admin_uid);
            }
        } else {
            //先判断是否是相册管理员,不是相册管理员再行判断
            if (!is_album_admin($adminObject)) {
                $originAlbumArray = [];

                $indexName = config('common_es.indices.album.albums');

                $esAlbumObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('album_uid', $album_uid)->get()->first();

                if (!isset($esAlbumObject->ablum_uid)) {
                    throw new CommonException('FindEsAlbumError');
                }

                //如果admin_id不等于用户id,就需要查找默认相册
                if ($esAlbumObject->amdin_uid !== $adminObject->biz_id) {
                    $album_uid = get_admin_album_uid($admin_uid);
                }
            }
        }

        return $album_uid;
    }
}
