<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-11 09:10:04
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-25 01:59:21
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Phone\File\PhonePictureFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\File;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Image;
use App\Exceptions\Phone\CommonException;
use App\Events\Phone\CommonEvent;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
//evnet
use App\Events\Es\V1\Picture\Album\EsAddSingleAlbumPictureEvent;
use App\Events\Es\V1\User\User\EsUpdateUserAvatarEvent;
use App\Facades\Pub\V1\Store\QiNiuFacade;

/**
 * @see App\Http\Controllers\LaravelFastApi\V1\Phone\File\FileController
 * @see \App\Facades\Phone\File\PhonePictureFacade
 */
class PhonePictureFacadeService
{
    public function test()
    {
        echo "PhonePictureFacadeService test";
    }

    protected static $user_picture_path = DIRECTORY_SEPARATOR.'user'. DIRECTORY_SEPARATOR.'album'.DIRECTORY_SEPARATOR;

    protected static $storage_public_path = DIRECTORY_SEPARATOR.'app'. DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;

    /**
     * 单图上传
     *
     * @param [type] $userObject
     * @param [type] $picture
     * @return void
     */
    public function singleUploadPicture(User $userObject, mixed $picture)
    {
        $result = code(config('phone_code.SinglePictureUploadError'));

        $user_uid = $userObject->user_uid;

        $album_uid = get_user_album_uid($user_uid);

        $insertDataArray = [];

        //图片上传结果设置为1
        if ($picture->isValid()) {
            $picture_file = $picture->getClientOriginalName();

            $picture_path = self::$user_picture_path.$user_uid.DIRECTORY_SEPARATOR;

            $path = $picture->storeAs($picture_path, $picture_file, 'public');

            $exists = Storage::disk('public')->exists($path);

            if (!$exists) {
                throw new CommonException('SinglePictureUploadSaveError');
            }

            $cloudStore = Cache::get('cloud.store');

            if ($cloudStore == 'qiniu') {
                QiNiuFacade::uploadFile(public_path('storage/'.$path), 'storage'.$picture_path.$picture_file);
            }


            $picture_info = getimagesize(storage_path().self::$storage_public_path.$path);
            $picture_name = \pathinfo($path, \PATHINFO_FILENAME);

            $picture_size = round(filesize(storage_path().self::$storage_public_path.$path) / 1024, 0);
            $picture_spec = "{$picture_info[0]}×{$picture_info[1]}";

            $insertDataArray = [
                'album_picture_uid' => get_snow_flake_id(),
                'user_uid' => $user_uid,
                'album_uid' => $album_uid,
                'picture_name' => $picture_name,
                'picture_file' => $picture_file,
                'picture_path' => $picture_path,
                'picture_size' => $picture_size,
                'picture_spec' => $picture_spec,
                'picture_type' => 10
            ];

            if ($cloudStore == 'qiniu') {
                $cdnUrl = trim(Cache::get('qiniu.cdn_url'));

                if (!$cdnUrl) {
                    throw new CommonException('QiNiuCdnUrlError');
                }

                $insertData['picture_type'] = 20;
                $insertData['picture_url'] = $cdnUrl.'/storage'.$picture_path.$picture_file;
            }
        }

        $albumPictureObject = ShardHelperFacade::createWithShard(AlbumPicture::class, $album_uid, $insertDataArray);

        if (!isset($albumPictureObject->biz_id)) {
            throw new CommonException('SinglePictureUploadSqlError');
        }

        CommonEvent::dispatch($userObject, $insertDataArray, 'SingleUploadPicture');

        EsAddSingleAlbumPictureEvent::dispatch($albumPictureObject);

        $result = code(['code' => 0,'msg' => '图片上传成功!'], ['data' => $insertDataArray,'picture_uid' => $albumPictureObject->biz_id,'picture' => asset('storage/'.$picture_path.$picture_file)]);

        return $result;
    }

    /**
     *
     *
     * @param [type] $userObject
     * @param [type] $picture
     * @return void
     */
    public function uploadUserAvatar(User $userObject, mixed $picture)
    {
        $result = code(config('phone_code.UplaodUserAvatarError'));

        $user_uid = $userObject->user_uid;

        $album_uid = get_user_album_uid($user_uid);

        $insertDataArray = [];

        //图片上传结果设置为1
        if ($picture->isValid()) {
            $picture_file = $picture->getClientOriginalName();

            $picture_path = self::$user_picture_path.$user_uid.DIRECTORY_SEPARATOR;

            $path = $picture->storeAs($picture_path, $picture_file, 'public');

            $exists = Storage::disk('public')->exists($path);

            if (!$exists) {
                throw new CommonException('SinglePictureUploadSaveError');
            }

            $cloudStore = Cache::get('cloud.store');

            if ($cloudStore == 'qiniu') {
                QiNiuFacade::uploadFile(public_path('storage/'.$path), 'storage'.$picture_path.$picture_file);
            }


            $picture_info = getimagesize(storage_path().self::$storage_public_path.$path);
            $picture_name = \pathinfo($path, \PATHINFO_FILENAME);

            $picture_size = round(filesize(storage_path().self::$storage_public_path.$path) / 1024, 0);
            $picture_spec = "{$picture_info[0]}×{$picture_info[1]}";

            $insertDataArray = [
                'album_picture_uid' => get_snow_flake_id(),
                'user_uid' => $user_uid,
                'album_uid' => $album_uid,
                'picture_name' => $picture_name,
                'picture_file' => $picture_file,
                'picture_path' => $picture_path,
                'picture_size' => $picture_size,
                'picture_spec' => $picture_spec,
                'picture_type' => 10
            ];

            if ($cloudStore == 'qiniu') {
                $cdnUrl = trim(Cache::get('qiniu.cdn_url'));

                if (!$cdnUrl) {
                    throw new CommonException('QiNiuCdnUrlError');
                }

                $insertData['picture_type'] = 20;
                $insertData['picture_url'] = $cdnUrl.'/storage'.$picture_path.$picture_file;
            }
        }

        $albumPictureObject = ShardHelperFacade::createWithShard(AlbumPicture::class, $album_uid, $insertDataArray);

        if (!isset($albumPictureObject->biz_id)) {
            throw new CommonException('UplaodUserAvatarError');
        }

        $userAvatarDataArray = [
            'user_avatar_uid' => get_snow_flake_id(),
            'user_uid' => $user_uid,
            'album_picture_uid' => $albumPictureObject->biz_id,
            'is_default' => 1,
        ];

        $userAvatarObject = ShardHelperFacade::createWithShard(UserAvatar::class, $user_uid, $userAvatarDataArray);

        if (!isset($userAvatarObject->biz_id)) {
            throw new CommonException('BindUserAvatarError');
        }

        $logDataArray = ['$insertDataArray' => $insertDataArray,'$userAvatarDataArray' => $userAvatarDataArray];


        CommonEvent::dispatch($userObject, $logDataArray, 'UplaodUserAvatar');

        $albumPictureObject = $albumPictureObject->fresh();

        EsAddSingleAlbumPictureEvent::dispatch($albumPictureObject);

        $avatar = asset('storage/'.$picture_path.DIRECTORY_SEPARATOR.$picture_file);
        
        EsUpdateUserAvatarEvent::dispatch($userObject, $avatar);

        $result = code(['code' => 0,'msg' => '头像上传成功!'], ['picture_uid' => $albumPictureObject->biz_id,'avatar' => $avatar]);

        return $result;
    }
}
