<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-02 02:59:54
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-20 12:41:04
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Picture\AdminPictureFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Picture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Image;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\SetCoverDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\moveAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\MoveMultipleAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\DeletePictureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\DeleteMultiplePictureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\UpdatePictureNameDTO;

use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Http\Resources\LaravelFastApi\V1\Db\Admin\System\Picture\AlbumPictureResource;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Picture\PictureController
 * @see \App\Facades\LaravelFastApi\V1\Admin\Picture\AdminPictureFacade
 */
class AdminPictureFacadeService
{
    public function test()
    {
        echo "AdminPictureFacadeService test";
    }

    /**
     * 设置封面
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function setCover(SetCoverDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.SetCoverError'));

        $albumPictureIndexName = config('common_es.indices.album.album_pictures');

        $picture_uid = $requestDTO->picture_uid;

        $esAlbumPictureObject = EsQueryFacade::index($albumPictureIndexName)->where('album_picture_uid', $picture_uid)->get()->first();

        //降级熔断
        if (!$esAlbumPictureObject) {
            throw new CommonException('ServiceBusyError');
        }

        $album_uid = $esAlbumPictureObject->album_uid;

        $albumIndexName = config('common_es.indices.album.albums');

        $esAlbumObject = EsQueryFacade::index($albumIndexName)->where('album_uid', $album_uid)->get()->first();

        //降级熔断
        if (!$esAlbumObject) {
            throw new CommonException('ServiceBusyError');
        }

        $albumObject = Album::queryByShard($esAlbumObject->user_uid)->where('album_uid', $album_uid)->first();

        if (!$albumObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $updateDataArray = [
            'cover_album_picture_uid' => $picture_uid,
        ];

        $updateResult = $albumObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('SetCoverError');
        }

        $albumObject = $albumObject->fresh();

        $indexName = config('common_es.indices.album.albums');

        $updateDataArray =  [
            'cover_album_picture_uid' => $albumObject->cover_album_picture_uid,
            'album_uid' => $albumObject->album_uid,
            'album_type' => $albumObject->album_type,
            'album_name' => $albumObject->album_name,
            'album_description' => $albumObject->album_description,
            'sort' => $albumObject->sort,
            'updated_time' => $albumObject->updated_time,
            'updated_at' => $albumObject->updated_at,

        ];
        $esResult = EsFacade::updateDoc($indexName, $albumObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es更新相册失败','$albumObject' => $albumObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'AdminPictureFacadeService', 'handleError');
        }

        CommonEvent::dispatch($adminObject, ['picture_uid' => $picture_uid,'data' => $updateDataArray], 'SetCover');

        $result = code(['code' => 0,'msg' => '设置封面成功']);

        return $result;
    }

    /**
    * 单图片移动相册
    *
    * @param [type] $validated
    * @param [type] $adminObject
    * @return void
    */
    public function moveAlbum(moveAlbumDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MoveAlbumError'));

        $album_uid = $requestDTO->album_uid;

        $picture_uid = $requestDTO->picture_uid;

        $albumPictureIndexName = config('common_es.indices.album.album_pictures');

        $esAlbumPictureObject = EsQueryFacade::index($albumPictureIndexName)->where('album_picture_uid', $picture_uid)->get()->first();

        //降级熔断
        if (!$esAlbumPictureObject) {
            throw new CommonException('ServiceBusyError');
        }

        $albumIndexName = config('common_es.indices.album.albums');

        $esAlbumObject = EsQueryFacade::index($albumIndexName)->where('album_uid', $album_uid)->get()->first();

        //降级熔断
        if (!$esAlbumObject) {
            throw new CommonException('ServiceBusyError');
        }

        $albumPictureObject = AlbumPicture::queryByShard($esAlbumPictureObject->album_uid)->where('album_picture_uid', $picture_uid)->first();

        if (!$albumPictureObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        //因为涉及分片计算业务所有需要添加新数据
        $insertDataArray = [
            'album_picture_uid' => get_snow_flake_id(),
            'admin_uid' => $esAlbumPictureObject->admin_uid,
            'album_uid' => $album_uid,
            'user_uid' => $esAlbumPictureObject->user_uid,
            'picture_name' => $esAlbumPictureObject->picture_name,
            'picture_file' => $esAlbumPictureObject->picture_file,
            'picture_path' => $esAlbumPictureObject->picture_path,
            'picture_size' => $esAlbumPictureObject->picture_size,
            'picture_spec' => $esAlbumPictureObject->picture_spec,
            'picture_type' => $esAlbumPictureObject->picture_type,
        ];

        $newAlbumPictureObject = ShardHelperFacade::createWithShard(AlbumPicture::class, $album_uid, $insertDataArray);

        if (!isset($newAlbumPictureObject->biz_id)) {
            throw new CommonException('AddAlbumPictureError');
        }

        //删除原有的图片
        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s'),
        ];

        $updateResult = $albumPictureObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('MoveAlbumError');
        }

        $albumPictureIndexName = config('common_es.indices.album.album_pictures');

        $configKey = get_shard_config_key();
        //es先添加
        $inserDataArray = [
            '_docId' => $newAlbumPictureObject->album_picture_uid,
            'shard_db' => ShardFacade::getDbName($newAlbumPictureObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($newAlbumPictureObject->user_uid, 'album_pictures', $configKey),
            'album_picture_uid' => $newAlbumPictureObject->album_picture_uid,
            'album_uid' => $newAlbumPictureObject->album_uid,
            'admin_uid' => $newAlbumPictureObject->admin_uid,
            'user_uid' => $newAlbumPictureObject->user_uid,
            'picture_name' => $newAlbumPictureObject->picture_name,
            'picture_tag' => $newAlbumPictureObject->picture_tag,
            'picture_path' => $newAlbumPictureObject->picture_path,
            'picture_file' => $newAlbumPictureObject->picture_file,
            'picture_size' => $newAlbumPictureObject->picture_size,
            'picture_spec' => $newAlbumPictureObject->picture_spec,
            'picture_type' => $newAlbumPictureObject->picture_type,
            'picture_url' => $newAlbumPictureObject->picture_url,
            'created_time' => $newAlbumPictureObject->created_time,
            'updated_time' => $newAlbumPictureObject->updated_time,
            'created_at' => $newAlbumPictureObject->created_at,
            'updated_at' => $newAlbumPictureObject->updated_at,
            'deleted_at' => $newAlbumPictureObject->deleted_at
        ];

        $insertResult = EsFacade::createDoc($albumPictureIndexName, $inserDataArray, $newAlbumPictureObject->album_picture_uid);

        if (!isset($insertResult) || !isset($insertResult['code']) || $insertResult['code'] != 0) {
            plog(['error' => 'es添加相册图片数据失败','$insertResult' => $insertResult,'$newAlbumPictureObject' => $newAlbumPictureObject,'$adminObject' => $adminObject], 'AdminPictureFacadeService', 'handleError');
        }

        //更新删除
        $udpateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s', time()),
        ];

        $updateResult = EsFacade::updateDoc($albumPictureIndexName, $albumPictureObject->album_picture_uid, $udpateDataArray);

        if (!isset($updateResult) || !isset($updateResult['code']) || $updateResult['code'] != 0) {
            plog(['error' => 'es删除相册图片数据失败','$updateResult' => $updateResult,'$albumPictureObject' => $albumPictureObject,'$adminObject' => $adminObject], 'AdminPictureFacadeService', 'handleError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'MoveAlbum');

        $result = code(['code' => 0,'msg' => '移动相册成功!']);

        return $result;
    }

    /**
     * 相册
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function moveMultipleAlbum(MoveMultipleAlbumDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MoveMultipleAlbumError'));

        $album_uid = $requestDTO->album_uid;

        $picture_uid_array = $requestDTO->picture_uid_array;

        $ablumPictureIndexName = config('common_es.indices.album.album_pictures');

        $esPictureCollection = EsQueryFacade::index($ablumPictureIndexName)->whereIn('album_picture_uid', $picture_uid_array)->get();

        //降级熔断
        if (empty($esPictureCollection) || $esPictureCollection->count() != count($picture_uid_array)) {
            throw new CommonException('ServiceBusyError');
        }

        foreach ($esPictureCollection as $key => $esAlbumPictureObject) {
            $albumPictureObject = AlbumPicture::queryByShard($esAlbumPictureObject->album_uid)->where('album_picture_uid', $esAlbumPictureObject->album_picture_uid)->first();

            if (!$albumPictureObject) {
                plog(['error' => '图片查找失败','$esAlbumPictureObject' => $esAlbumPictureObject], 'AdminPictureFacadeService', 'moveMultipleAlbumError');
                continue;
            }

            $insertDataArray = [
                'album_picture_uid' => get_snow_flake_id(),
                'admin_uid' => $esAlbumPictureObject->admin_uid,
                'album_uid' => $album_uid,
                'user_uid' => $esAlbumPictureObject->user_uid,
                'picture_name' => $esAlbumPictureObject->picture_name,
                'picture_file' => $esAlbumPictureObject->picture_file,
                'picture_path' => $esAlbumPictureObject->picture_path,
                'picture_size' => $esAlbumPictureObject->picture_size,
                'picture_spec' => $esAlbumPictureObject->picture_spec,
                'picture_type' => $esAlbumPictureObject->picture_type,
            ];

            $newAlbumPictureObject = ShardHelperFacade::createWithShard(AlbumPicture::class, $album_uid, $insertDataArray);

            if (!isset($newAlbumPictureObject->biz_id)) {
                plog(['error' => '图片添加失败','$esAlbumPictureObject' => $esAlbumPictureObject,'$insertDataArray' => $insertDataArray], 'AdminPictureFacadeService', 'moveMultipleAlbumError');
                continue;
            }

            //删除原有的图片
            $updateDataArray = [
                'deleted_at' => date('Y-m-d H:i:s'),
            ];

            $updateResult = $albumPictureObject->updateWithShard($updateDataArray);

            if (!$updateResult) {
                plog(['error' => '图片删除失败','$albumPictureObject' => $albumPictureObject], 'AdminPictureFacadeService', 'moveMultipleAlbumError');
                continue;
            }

             $albumPictureIndexName = config('common_es.indices.album.album_pictures');

            $configKey = get_shard_config_key();
            //es先添加
            $inserDataArray = [
                '_docId' => $newAlbumPictureObject->album_picture_uid,
                'shard_db' => ShardFacade::getDbName($newAlbumPictureObject->user_uid, $configKey),
                'shard_table' => ShardFacade::getTableName($newAlbumPictureObject->user_uid, 'album_pictures', $configKey),
                'album_picture_uid' => $newAlbumPictureObject->album_picture_uid,
                'album_uid' => $newAlbumPictureObject->album_uid,
                'admin_uid' => $newAlbumPictureObject->admin_uid,
                'user_uid' => $newAlbumPictureObject->user_uid,
                'picture_name' => $newAlbumPictureObject->picture_name,
                'picture_tag' => $newAlbumPictureObject->picture_tag,
                'picture_path' => $newAlbumPictureObject->picture_path,
                'picture_file' => $newAlbumPictureObject->picture_file,
                'picture_size' => $newAlbumPictureObject->picture_size,
                'picture_spec' => $newAlbumPictureObject->picture_spec,
                'picture_type' => $newAlbumPictureObject->picture_type,
                'picture_url' => $newAlbumPictureObject->picture_url,
                'created_time' => $newAlbumPictureObject->created_time,
                'updated_time' => $newAlbumPictureObject->updated_time,
                'created_at' => $newAlbumPictureObject->created_at,
                'updated_at' => $newAlbumPictureObject->updated_at,
                'deleted_at' => $newAlbumPictureObject->deleted_at
            ];

            $insertResult = EsFacade::createDoc($albumPictureIndexName, $inserDataArray, $newAlbumPictureObject->album_picture_uid);

            if (!isset($insertResult) || !isset($insertResult['code']) || $insertResult['code'] != 0) {
                plog(['error' => 'es添加相册图片数据失败','$insertResult' => $insertResult,'$newAlbumPictureObject' => $newAlbumPictureObject,'$adminObject' => $adminObject], 'EsMoveAlbumJob', 'handleError');
            }

            //更新删除
            $udpateDataArray = [
                'deleted_at' => date('Y-m-d H:i:s', time()),
            ];

            $updateResult = EsFacade::updateDoc($albumPictureIndexName, $albumPictureObject->album_picture_uid, $udpateDataArray);

            if (!isset($updateResult) || !isset($updateResult['code']) || $updateResult['code'] != 0) {
                plog(['error' => 'es删除相册图片数据失败','$updateResult' => $updateResult,'$albumPictureObject' => $albumPictureObject,'$adminObject' => $adminObject], 'EsMoveAlbumJob', 'handleError');
            }
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'MoveMultipleAlbum');


        $result = code(['code' => 0,'msg' => '批量移动相册成功!']);

        return $result;
    }


    /**
     * 删除图片
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function deletePicture(DeletePictureDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeletePictureError'));

        $ablumPictureIndexName = config('common_es.indices.album.album_pictures');

        $esPictureObject = EsQueryFacade::index($ablumPictureIndexName)->where('album_picture_uid', $requestDTO->picture_uid)->get()->first();

        if (empty($esPictureObject)) {
            throw new CommonException('ServiceBusyError');
        }

        $albumPictureObject = AlbumPicture::queryByShard($esPictureObject->album_uid)->where('album_picture_uid', $requestDTO->picture_uid)->first();

        if (!$albumPictureObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s', time()),
        ];

        $updateResult = $albumPictureObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('DeletePictureError');
        }

        $albumPictureIndexName = config('common_es.indices.album.album_pictures');

        //更新删除
        $udpateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s', time()),
        ];

        $updateResult = EsFacade::updateDoc($albumPictureIndexName, $albumPictureObject->album_picture_uid, $udpateDataArray);

        if (!isset($updateResult) || !isset($updateResult['code']) || $updateResult['code'] != 0) {
            plog(['error' => 'es删除相册图片数据失败','$updateResult' => $updateResult,'$albumPictureObject' => $albumPictureObject,'$adminObject' => $adminObject], 'EsDeltePictureJob', 'handleError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'DeletePicture');

        $result = code(['code' => 0,'msg' => '删除图片成功!']);

        return $result;
    }

    /**
     * 批量删除图片
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function deleteMultiplePicture(DeleteMultiplePictureDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteMultiplePictureError'));

        $picture_uid_array = $requestDTO->picture_uid_array;

        $ablumPictureIndexName = config('common_es.indices.album.album_pictures');

        $esPictureCollection = EsQueryFacade::index($ablumPictureIndexName)->whereIn('album_picture_uid', $picture_uid_array)->get();

        //降级熔断
        if (empty($esPictureCollection) || $esPictureCollection->count() != count($picture_uid_array)) {
            throw new CommonException('ServiceBusyError');
        }

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s', time()),
        ];

        foreach ($esPictureCollection as $key => $esPictureObject) {
            $albumPictureObject = AlbumPicture::queryByShard($esPictureObject->album_uid)->where('album_picture_uid', $esPictureObject->album_picture_uid)->first();

            if (!$albumPictureObject) {
                continue;
            }

            $albumPictureObject->updateWithShard($updateDataArray);

            $albumPictureIndexName = config('common_es.indices.album.album_pictures');

            //更新删除
            $udpateDataArray = [
                'deleted_at' => date('Y-m-d H:i:s', time()),
            ];

            $updateResult = EsFacade::updateDoc($albumPictureIndexName, $albumPictureObject->album_picture_uid, $udpateDataArray);

            if (!isset($updateResult) || !isset($updateResult['code']) || $updateResult['code'] != 0) {
                plog(['error' => 'es删除相册图片数据失败','$updateResult' => $updateResult,'$albumPictureObject' => $albumPictureObject,'$adminObject' => $adminObject], 'EsDeltePictureJob', 'handleError');
            }
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'DeleteMultiplePicture');

        $result = code(['code' => 0,'msg' => '批量删除图片成功!']);

        return $result;
    }



    /**
     * 修改图片名称
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updatePictureName(UpdatePictureNameDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdatePictureNameError'));

        $ablumPictureIndexName = config('common_es.indices.album.album_pictures');

        $esPictureObject = EsQueryFacade::index($ablumPictureIndexName)->where('album_picture_uid', $requestDTO->picture_uid)->get()->first();

        if (empty($esPictureObject)) {
            throw new CommonException('ServiceBusyError');
        }

        $albumPictureObject = AlbumPicture::queryByShard($esPictureObject->album_uid)->where('album_picture_uid', $requestDTO->picture_uid)->first();

        if (!$albumPictureObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $updateDataArray = [
            'picture_name' => $requestDTO->picture_name ?? $esPictureObject->picture_name,
        ];

        $updateResult = $albumPictureObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('UpdatePictureNameError');
        }

        $indexName = config('common_es.indices.album.album_pictures');

        $updateDataArray = [
            'picture_name' => $albumPictureObject->picture_name,
            'updated_at' => $albumPictureObject->updated_at,
            'updated_time' => $albumPictureObject->updated_time,
        ];

        $esResult = EsFacade::updateDoc($indexName, $albumPictureObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es更新相册图片失败','$albumPictureObject' => $albumPictureObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'EsUpdatePictureJob', 'handleError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'UpdatePictureName');

        $result = code(['code' => 0,'msg' => '更新图片成功!']);

        return $result;
    }
}
