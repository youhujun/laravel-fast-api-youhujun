<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-23 19:11:28
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 01:01:59
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Es\V1\User\User\EsUserUpdateEvent\EsUserUpdateListener.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Listeners\Es\V1\User\User\EsUserUpdateEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\User\Info\UserCascader;
/**
 * @see \App\Events\Es\V1\User\User\EsUserUpdateEvent
 */
class EsUserUpdateListener
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
        $userObject = $this->userObject;
        $isTransation = $event->isTransation;

        $indexName = config('common_es.indices.user.users');

        $userInfoObject = UserInfo::queryByShard($userObject->biz_id)->where('user_uid', $userObject->biz_id)->first();

        $albumObject = Album::queryByShard($userObject->biz_id)->where('user_uid', $userObject->biz_id)->where('album_type', 20)->where('is_default', 1)->first();

        $albumPictureObject = AlbumPicture::queryByShard($albumObject?->biz_id)->where('user_uid', $userObject->biz_id)->first();

        $avatarUrl = null;

        //头像图片类型
        $ablum_picture_pciture_type = $albumPictureObject?->picture_type;

        //本地存储
        if ($ablum_picture_pciture_type == 10) {
            $avatarUrl = asset('/storage'.$albumPictureObject->picture_path.DIRECTORY_SEPARATOR.$albumPictureObject->picture_file);
        }
        //云端存储
        if ($ablum_picture_pciture_type == 20) {
            $avatarUrl = $albumPictureObject->picture_url;
        }

        $userCascaderObject = UserCascader::queryByShard($userObject->biz_id)->where('user_uid', $userObject->user_uid)->first();

        $updateDataArray = [
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
             //cascader
            'role_cascader_json' => $userCascaderObject?->role_cascader_json,
        ];

        $result = EsFacade::updateDoc($indexName, $userObject->biz_id, $updateDataArray);

        //plog(['info' => 'es添加用户完成','$result' => $result], 'EsAddUserJob', 'handle');

        if (!isset($result['code']) || $result['code'] != 0) {
            plog(['error' => 'es更新用户失败','$result' => $result], 'EsUserUpdateListener', 'handleError');

            if ($isTransation) {
                DB::rollBack();
            }
            
            throw new CommonException('EsUpdateUserError');
        }
    }
}
