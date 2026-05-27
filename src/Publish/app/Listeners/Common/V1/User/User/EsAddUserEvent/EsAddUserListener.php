<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-10 23:35:25
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 00:33:16
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Common\V1\User\User\EsAddUserEvent\EsAddUserListener.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\EsAddUserEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\Common\CommonException;
use Illuminate\Support\Facades\DB;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserQrcode;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\User\Info\UserCascader;

/**
 * @see \App\Events\Common\V1\User\User\EsAddUserEvent
 */
class EsAddUserListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {

        $userObject = $event->userObject;
        $isTransation = $event->isTransation;

        $indexName = config('common_es.indices.user.users');

        $configKey = get_shard_config_key();

        //查用户信息
        $userInfoObject = UserInfo::queryByShard($userObject->biz_id)->where('user_uid', $userObject->biz_id)->first();

        //查询用户默认相册
        $albumObject = Album::queryByShard($userObject->biz_id)->where('user_uid', $userObject->biz_id)->where('album_type', 20)->where('is_default', 1)->first();

        //查询用户默认头像
        $userAvatarObject = UserAvatar::queryByShard($userObject->biz_id)->where('user_uid', $userObject->biz_id)->where('is_default', 1)->first();

        //查询用户二维码
        $userQrcodeObject = UserQrcode::queryByShard($userObject->biz_id)->where('user_uid', $userObject->biz_id)->where('is_default', 1)->first();

        //注意:二维码是用户默认相册,但是默认头像是系统默认相册
        $system_album_uid = get_system_album_uid();

        //查询头像图片
        //注意这里直接使用系统默认头像
        $avatarAlbumPictureObject = AlbumPicture::queryByShard($system_album_uid)->where('album_picture_uid', $userAvatarObject?->album_picture_uid)->where('picture_tag', 'avatar')->first();
        //查询二维码图片 
        //
        $qrcodeAlbumPictureObject = AlbumPicture::queryByShard($albumObject?->biz_id)->where('user_uid', $userObject->biz_id)->where('album_picture_uid', $userQrcodeObject?->album_picture_uid)->first();

        //头像图片地址
        $avatarUrl = null;

        //头像图片类型
        $avatar_ablum_picture_pciture_type = $avatarAlbumPictureObject?->picture_type;

        //本地存储
        if ($avatar_ablum_picture_pciture_type == 10) {
            $avatarUrl = asset('/storage'.$avatarAlbumPictureObject->picture_path.DIRECTORY_SEPARATOR.$avatarAlbumPictureObject->picture_file);
        }
        //云端存储
        if ($avatar_ablum_picture_pciture_type == 20) {
            $avatarUrl = $avatarAlbumPictureObject->picture_url;
        }

        //二维码图片地址
        $qrcodeUrl = null;
        
        //二维码图片类型
        $qrcode_ablum_picture_pciture_type = $qrcodeAlbumPictureObject?->picture_type;

        if($qrcode_ablum_picture_pciture_type == 10){
            $qrcodeUrl = asset('/storage'.$qrcodeAlbumPictureObject->picture_path.$qrcodeAlbumPictureObject->picture_file);
        }

        if($qrcode_ablum_picture_pciture_type == 20){
            $qrcodeUrl = $qrcodeAlbumPictureObject->picture_url;
        }

        $userCascaderObject = UserCascader::queryByShard($userObject->biz_id)->where('user_uid', $userObject->user_uid)->first();

       
        $insertDataArray = [
            '_docId' => $userObject->user_uid,
            'shard_key' => $userObject->shard_key,
            'shard_db' => ShardFacade::getDbName($userObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($userObject->user_uid, 'users', $configKey),
            'user_uid' => $userObject->user_uid,
            'source_user_uid' => $userObject->source_user_uid,
            'parent_user_uid' => $userObject->parent_user_uid,
            'account_status' => $userObject->account_status,
            'real_auth_status' => $userObject->real_auth_status,
            'level_id' => $userObject->level_id,
            'source' => $userObject->source,
            'remember_token' => $userObject->remember_token,
            'auth_token' => $userObject->auth_token,
            'account_name' => $userObject->account_name,
            'invite_code' => $userObject->invite_code,
            'phone_area_code' => $userObject->phone_area_code,
            'phone' => $userObject->phone,
            'password' => $userObject->password,
            'email' => $userObject->email,
            'created_time' => $userObject->created_time,
            'updated_time' => $userObject->updated_time,
            'created_at' => $userObject->created_at,
            'updated_at' => $userObject->updated_at,
            'deleted_at' => $userObject->deleted_at,
            //userInfo
            'id_number' => $userInfoObject->id_number,
            'nick_name' => $userInfoObject->nick_name,
            'real_name' => $userInfoObject->real_name,
            'solar_birthday_at' => $userInfoObject->solar_birthday_at,
            'chinese_birthday_at' => $userInfoObject->chinese_birthday_at,
            'sex' => $userInfoObject->sex,
            'introduction' => $userInfoObject->introduction,
            //album
            'ablum_uid' => $albumObject->album_uid,
            //avatar
            'avatar' => $avatarUrl,
            //qrcode
            'qrcode' => $qrcodeUrl,
            //cascader
            'role_cascader_json' => $userCascaderObject?->role_cascader_json,
        ];

        $result = EsFacade::createDoc($indexName, $insertDataArray, $userObject->biz_id);

        if (!isset($result['code']) || $result['code'] != 0) {
            plog(['error' => 'es添加用户失败','$result' => $result], 'EsAddUserListener', 'handleError');

            if ($isTransation) {
                DB::rollBack();
            }
            
            throw new CommonException('EsAddUserError');

        }
    }
}
