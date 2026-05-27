<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-20 12:28:22
 * @FilePath: \youhu-laravel-api-12\app\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Policies\LaravelFastApi\V1\Admin\Picture;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Exceptions\Admin\CommonException;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\SetCoverDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\moveAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\MoveMultipleAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\DeletePictureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\DeleteMultiplePictureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\UpdatePictureNameDTO;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\Picture\Album;

class PicturePlolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 替换照片
     *
     * @param Admin $adminObject
     * @param [type] $id
     * @return void
     */
    public function resetUpload(Admin $adminObject, int|string $picture_uid)
    {
        //如果是开发者,超级管理员,相册管理员就直接放过
        if (is_develop($adminObject) || is_super($adminObject) || is_album_admin($adminObject)) {
            return true;
        }

        $indexName = config('common_es.indices.album.album_pictures');

        $esPictureObject = EsQueryFacade::index($indexName)->where('album_picture_uid', $picture_uid)->get()->first();

        if (empty($esPictureObject)) {
            throw new CommonException('ServiceBusyError');
        }

        return $adminObject->biz_id === $esPictureObject->admin_uid;
    }

    //设置相册封面
    public function setCover(Admin $adminObject, SetCoverDTO $requestDTO)
    {
        //如果是开发者,超级管理员,相册管理员就直接放过
        if (is_develop($adminObject) || is_super($adminObject) || is_album_admin($adminObject)) {
            return true;
        }

        $ablumPictureindexName = config('common_es.indices.album.album_pictures');

        $esPictureObject = EsQueryFacade::index($ablumPictureindexName)->where('album_picture_uid', $requestDTO->picture_uid)->get()->first();

        if (empty($esPictureObject)) {
            throw new CommonException('ServiceBusyError');
        }

        $ablumIndexName = config('common_es.indices.album.albums');

        $esAlbumObject = EsQueryFacade::index($ablumIndexName)->where('album_uid', $esPictureObject->album_uid)->get()->first();

        if (empty($esAlbumObject)) {
            throw new CommonException('ServiceBusyError');
        }

        return $adminObject->biz_id === $esAlbumObject->admin_uid;
    }

    //转移相册
    public function moveAlbum(Admin $adminObject, moveAlbumDTO $requestDTO)
    {
        //如果是开发者,超级管理员,相册管理员就直接放过
        if (is_develop($adminObject) || is_super($adminObject) || is_album_admin($adminObject)) {
            return true;
        }

        $ablumPictureindexName = config('common_es.indices.album.album_pictures');

        $esPictureObject = EsQueryFacade::index($ablumPictureindexName)->where('album_picture_uid', $requestDTO->picture_uid)->get()->first();

        if (empty($esPictureObject)) {
            throw new CommonException('ServiceBusyError');
        }

        $ablumIndexName = config('common_es.indices.album.albums');

        $esAlbumObject = EsQueryFacade::index($ablumIndexName)->where('album_uid', $esPictureObject->album_uid)->get()->first();

        if (empty($esAlbumObject)) {
            throw new CommonException('ServiceBusyError');
        }

        return $adminObject->biz_id === $esAlbumObject->admin_uid;
    }

    //批量转移相册
    public function moveMultipleAlbum(Admin $adminObject, MoveMultipleAlbumDTO $requestDTO)
    {
        //如果是开发者,超级管理员,相册管理员就直接放过
        if (is_develop($adminObject) || is_super($adminObject) || is_album_admin($adminObject)) {
            return true;
        }

        $result = true;

        $picture_uid_array = $requestDTO->picture_uid_array;

        $ablumPictureindexName = config('common_es.indices.album.album_pictures');

        $esPictureCollection = EsQueryFacade::index($ablumPictureindexName)->whereIn('album_picture_uid', $picture_uid_array)->get();

        foreach ($esPictureCollection as $key => $esPictureObject) {
            if ($esPictureObject->admin_uid !== $adminObject->biz_id) {
                $result = false;
                break;
            }
        }

        return $result;



        $album = Album::find($album_uid);

        if ($adminObject->biz_id === $album->admin_id && $pictureResult) {
            $result = true;
        }

        return  $result;
    }

    //批量删除图片
    public function deleteMultiplePicture(Admin $adminObject, DeleteMultiplePictureDTO $requestDTO)
    {
        //如果是开发者,超级管理员,相册管理员就直接放过
        if (is_develop($adminObject) || is_super($adminObject) || is_album_admin($adminObject)) {
            return true;
        }

        $result = true;

        $picture_uid_array = $requestDTO->picture_uid_array;

        $ablumIndexName = config('common_es.indices.album.albums');

        $esAlbumObject = EsQueryFacade::index($ablumIndexName)->where('album_uid', $requestDTO->album_uid)->get()->first();

        if (empty($esAlbumObject)) {
            throw new CommonException('ServiceBusyError');
        }

        if ($esAlbumObject->admin_uid !== $adminObject->biz_id) {
            $result = false;
            return $result;
        }

        $ablumPictureindexName = config('common_es.indices.album.album_pictures');

        $esPictureCollection = EsQueryFacade::index($ablumPictureindexName)->whereIn('album_picture_uid', $picture_uid_array)->get();

        foreach ($esPictureCollection as $key => $esPictureObject) {
            if ($esPictureObject->admin_uid !== $adminObject->biz_id) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    //删除图片
    public function deletePicture(Admin $adminObject, DeletePictureDTO $requestDTO)
    {
        //如果是开发者,超级管理员,相册管理员就直接放过
        if (is_develop($adminObject) || is_super($adminObject) || is_album_admin($adminObject)) {
            return true;
        }

        $ablumPictureindexName = config('common_es.indices.album.album_pictures');

        $esPictureObject = EsQueryFacade::index($ablumPictureindexName)->where('album_picture_uid', $requestDTO->picture_uid)->get()->first();

        if (empty($esPictureObject)) {
            throw new CommonException('ServiceBusyError');
        }

        return $esPictureObject->admin_uid === $adminObject->biz_id;
    }

    //修改图片名称
    public function updatePictureName(Admin $adminObject, UpdatePictureNameDTO $requestDTO)
    {
        //如果是开发者,超级管理员,相册管理员就直接放过
        if (is_develop($adminObject) || is_super($adminObject) || is_album_admin($adminObject)) {
            return true;
        }

        $ablumPictureindexName = config('common_es.indices.album.album_pictures');

        $esPictureObject = EsQueryFacade::index($ablumPictureindexName)->where('album_picture_uid', $requestDTO->picture_uid)->get()->first();

        if (empty($esPictureObject)) {
            throw new CommonException('ServiceBusyError');
        }

        return $esPictureObject->admin_uid === $adminObject->biz_id;
    }
}
