<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-25 05:53:54
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-18 04:12:29
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Sync\User\EsSyncAdminFacadeService.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Es\Sync\User;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\Facade\Common\V1\Es\Console\Traits\EsFacadeServiceBaseTrait;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Attributes\Common\DocNote;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\Admin\Info\AdminCascader;

/**
 * @see \App\Facades\LaravelFastApi\V1\Es\Sync\User\EsSyncAdminFacade
 */
class EsSyncAdminFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsSyncAdminFacadeService test";
    }

    public function __construct()
    {
    }

    /**
     * 执行数据同步
     */
    public function syncAdmin(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有admins数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.user.admins');

		//先清空索引数据
		EsFacade::clearAllDoc($indexName);

        Admin::queryByAllShard()
        ->select(['admin_uid','shard_key','user_uid', 'account_status','account_name','email','phone','created_at', 'updated_at','deleted_at'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $adminCollection = $chunk;
            // 1. 先批量获取这批用户的所有ID
            $userUidArray = $adminCollection->pluck('user_uid')->toArray();
            $adminUidArray = $adminCollection->pluck('admin_uid')->toArray();

            $userCollection = User::queryByAllShard()
                ->whereIn('user_uid', $userUidArray)
                ->get()
                ->keyBy('user_uid');
            // 2. 批量查询
            //用户信息
            $userInfoCollection = UserInfo::queryByAllShard()
                ->whereIn('user_uid', $userUidArray)
                ->get()
                ->keyBy('user_uid');

            //用户相册与头像
            $adminAlbumCollection = Album::queryByAllShard()
                ->whereIn('admin_uid', $adminUidArray)
                //默认用户相册
                ->where('album_type', 10)
                ->where('is_default', 1)
                ->get()
                ->keyBy('user_uid');

            $userAvatarCollection = UserAvatar::queryByAllShard()
                ->whereIn('user_uid', $userUidArray)
                ->get()
                ->keyBy('user_uid');

            $adminCascaderCollection = AdminCascader::queryByAllShard()
                ->whereIn('admin_uid', $adminUidArray)
                ->get()
                ->keyBy('admin_uid');

            //获取所有的图片uid
            $albumPcitureUidArray = $userAvatarCollection->pluck('album_picture_uid')->toArray();

            $albumPictureCollection = AlbumPicture::queryByAllShard()
                ->whereIn('album_picture_uid', $albumPcitureUidArray)
                ->get()
                ->keyBy('album_picture_uid');

            $paramArray = [
                'userInfoCollection' => $userInfoCollection,
                'adminAlbumCollection' => $adminAlbumCollection,
                'userAvatarCollection' => $userAvatarCollection,
                'albumPictureCollection' => $albumPictureCollection,
                'adminCascaderCollection'=>$adminCascaderCollection,
            ];

            //p($paramArray);

            $esDataArray = $adminCollection->map(function ($adminObject) use ($paramArray) {
                extract($paramArray);

                // p($paramArray);
                // die;

                $userInfoObject = $userInfoCollection->get($adminObject->user_uid);
                $userAvatarObject = $userAvatarCollection->get($adminObject->user_uid);
                $albumObject = $adminAlbumCollection->get($adminObject->user_uid);
                $albumPictureObject = $albumPictureCollection->get($userAvatarObject->album_picture_uid);
                $adminCascaderObject = $adminCascaderCollection->get($adminObject->admin_uid);

                $avatarUrl = null;

                //头像图片类型
                $ablum_picture_pciture_type = $albumPictureObject?->picture_type;

                //本地存储
                if ($ablum_picture_pciture_type == 10) {
                    $avatarUrl = asset('/storage'.$albumPictureObject->picture_path.$albumPictureObject->picture_file);
                }
                //云端存储
                if ($ablum_picture_pciture_type == 20) {
                    $avatarUrl = $albumPictureObject->picture_url;
                }

                $configKey = get_shard_config_key();

                return [
                    '_docId' => $adminObject->admin_uid,
                    'shard_key' => $adminObject->shard_key,
                    'shard_db' => ShardFacade::getDbName($adminObject->user_uid, $configKey),
                    'shard_table' => ShardFacade::getTableName($adminObject->user_uid, 'admins', $configKey),
                    'admin_uid' => $adminObject->admin_uid,
                    'user_uid' => $adminObject->user_uid,
                    'remember_token' => $adminObject->remember_token,
                    'account_status' => $adminObject->account_status,
                    'phone_area_code' => $adminObject->phone_area_code,
                    'phone' => $adminObject->phone,
                    'password' => $adminObject->password,
                    'account_name' => $adminObject->account_name,
                    'created_time' => $adminObject->created_time,
                    'updated_time' => $adminObject->updated_time,
                    'created_at' => $adminObject->created_at,
                    'updated_at' => $adminObject->updated_at,
                    'deleted_at' => $adminObject->deleted_at,
                    //userInfo
                    'id_number' => $userInfoObject->id_number,
                    'nick_name' => $userInfoObject->nick_name,
                    'real_name' => $userInfoObject->real_name,
                    'solar_birthday_at' => $userInfoObject->solar_birthday_at,
                    'solar_birthday_time' => $userInfoObject->solar_birthday_time,
                    'chinese_birthday_at' => $userInfoObject->chinese_birthday_at,
                    'chinese_birthday_time' => $userInfoObject->chinese_birthday_time,
                    'sex' => $userInfoObject->sex,
                    'introduction' => $userInfoObject->introduction,
                    //album
                    'ablum_uid' => $albumObject?->album_uid,
                    //avatar
                    'avatar' => $avatarUrl,
                    //cascader
                    'role_cascader_json' => $adminCascaderObject?->role_cascader_json,

                ];
            })->toArray();

            //p($esDataArray);

            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步管理员数据失败','$result' => $result], 'EsSyncAdminFacadeService', 'syncAdminError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => '批量同步管理员数据完成','total' => $total,'costTime' => $costTime], 'EsSyncUserFacadeService', 'syncAdmin');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有admins数据同步ES结束--2', 'info');
        }
    }
}
