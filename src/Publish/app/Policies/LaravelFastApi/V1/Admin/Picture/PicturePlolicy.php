<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-25 11:50:50
 * @FilePath: \app\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Policies\LaravelFastApi\V1\Admin\Picture;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;

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
     * @param Admin $admin
     * @param [type] $id
     * @return void
     */
    public function resetUpload($admin,$id)
    {
        $picture = AlbumPicture::find($id);

        return $admin->id === $picture->admin_id;
    }

    //设置相册封面
    public function setCover($admin,$id)
    {
        $picture = AlbumPicture::find($id);

        $album_id = $picture->album_id;

        $album = Album::find($album_id);

        return $admin->id === $album->admin_id;
    }

    //转移相册
    public function moveAlbum($admin, $album_id,$picture_id)
    {
        $result = false;

        $picture = AlbumPicture::find($picture_id);

        $pictureResult = false;

        if($picture->admin_id === $admin->id)
        {
            $pictureResult = true;
        }

        $album = Album::find($album_id);

        if($admin->id === $album->admin_id && $pictureResult)
        {
            $result = true;
        }

        return  $result;
    }

    //批量转移相册
    public function moveMultipleAlbum($admin, $album_id,$pictureId)
    {
        $result = false;

        $pictures = AlbumPicture::whereIn('id',$pictureId )->get();

        $pictureResult = true;

        foreach ($pictures as $key => $value)
        {
            if($value->admin_id !== $admin->id)
            {
                $pictureResult = false;
                break;
            }
        }

        $album = Album::find($album_id);

        if($admin->id === $album->admin_id && $pictureResult)
        {
            $result = true;
        }

        return  $result;
    }

    //批量删除图片
    public function  deleteMultiplePicture($admin, $pictureId)
    {
        $result = false;

        $pictures = AlbumPicture::whereIn('id',$pictureId )->get();

        foreach ($pictures as $key => $value)
        {
            if($value->admin_id !== $admin->id)
            {
                $result = false;
                break;
            }
        }

        return $result;
    }

    //删除图片
    public function deletePicture($admin, $picture_id)
    {
        $result = false;

        $picture = AlbumPicture::find($picture_id);

        if($picture->admin_id !== $admin->id)
        {
            $result = false;
        }

        return $result;
    }

    //修改图片名称
    public function updatePictureName($admin, $picture_id)
    {
        $result = false;

        $picture = AlbumPicture::find($picture_id);

        if($picture->admin_id !== $admin->id)
        {
            $result = false;
        }

        return $result;
    }
}
