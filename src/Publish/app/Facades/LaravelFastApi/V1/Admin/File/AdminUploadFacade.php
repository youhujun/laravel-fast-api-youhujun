<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-20 23:10:20
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-05-20 23:10:26
 * @FilePath: \App\Facades\LaravelFastApi\V1\Admin\File\AdminUploadFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Admin\File;

use Illuminate\Support\Facades\Facade;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadConfigFileDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadFileDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadSinglePicutureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadMultiplePicutureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadUserAvatarDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadResetPictureDTO;
use App\Models\LaravelFastApi\V1\Admin\Admin;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\File\AdminUploadFacadeService
 */
class AdminUploadFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminUploadFacade";
    }

    public static function uploadConfigFile(Admin $adminObject, UploadConfigFileDTO $uploadConfigFileDTO, mixed $file)
    {
        return static::getFacadeRoot()->uploadConfigFile($adminObject, $uploadConfigFileDTO, $file);
    }

    public static function uploadFile(Admin $adminObject, UploadFileDTO $uploadFileDTO, mixed $file)
    {
        return static::getFacadeRoot()->uploadFile($adminObject, $uploadFileDTO, $file);
    }

    public static function uploadSinglePicuture(Admin $adminObject, UploadSinglePicutureDTO $uploadSinglePicutureDTO, mixed $file)
    {
        return static::getFacadeRoot()->uploadSinglePicuture($adminObject, $uploadSinglePicutureDTO, $file);
    }

    public static function uploadMultiplePicuture(Admin $adminObject, UploadMultiplePicutureDTO $uploadMultiplePicutureDTO, mixed $file)
    {
        return static::getFacadeRoot()->uploadMultiplePicuture($adminObject, $uploadMultiplePicutureDTO, $file);
    }

    public static function uploadUserAvatar(Admin $adminObject, UploadUserAvatarDTO $uploadUserAvatarDTO, mixed $file)
    {
        return static::getFacadeRoot()->uploadUserAvatar($adminObject, $uploadUserAvatarDTO, $file);
    }

    public static function uploadResetPicture(Admin $adminObject, UploadResetPictureDTO $uploadResetPictureDTO, mixed $file)
    {
        return static::getFacadeRoot()->uploadResetPicture($adminObject, $uploadResetPictureDTO, $file);
    }
}
