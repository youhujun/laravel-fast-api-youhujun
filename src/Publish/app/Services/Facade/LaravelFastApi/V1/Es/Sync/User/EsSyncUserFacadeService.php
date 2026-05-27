<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-20 14:15:08
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-19 00:45:59
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Sync\User\EsSyncUserFacadeService.php
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
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserAmount;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\User\Info\UserCascader;

/**
 * @see \App\Facades\LaravelFastApi\V1\Es\Sync\User\EsSyncUserFacade
 */
class EsSyncUserFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsSyncUserFacadeService test";
    }

    public function __construct()
    {
    }

    /**
     * 执行用户数据同步
     */
    public function syncUser(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有users数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.user.users');

        User::queryByAllShard()
        ->select(['user_uid','shard_key','source_user_uid','parent_user_uid', 'account_status', 'invite_code','email','real_auth_status', 'level_id','account_name','phone','password','created_at', 'updated_at','deleted_at'])
        //->with(['userInfo'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $userCollection = $chunk;
            // 1. 先批量获取这批用户的所有ID
            $user_uid_array = $userCollection->pluck('user_uid')->toArray();

            // 2. 批量查询
            //用户信息
            $userInfoCollection = UserInfo::queryByAllShard()
                ->whereIn('user_uid', $user_uid_array)
                ->get()
                ->keyBy('user_uid');

            //用户相册与头像
            $userAlbumCollection = Album::queryByAllShard()
                ->whereIn('user_uid', $user_uid_array)
                //默认用户相册
                ->where('album_type', 20)
                ->where('is_default', 1)
                ->get()
                ->keyBy('user_uid');



            $userAvatarCollection = UserAvatar::queryByAllShard()
                ->whereIn('user_uid', $user_uid_array)
                ->get()
                ->keyBy('user_uid');

            $UserCascaderCollection = UserCascader::queryByAllShard()
                ->whereIn('user_uid', $user_uid_array)
                ->get()
                ->keyBy('user_uid');

            //获取所有的图片uid
            $albumPcitureUidArray = $userAvatarCollection->pluck('album_picture_uid')->toArray();

            $albumPictureCollection = AlbumPicture::queryByAllShard()
                ->whereIn('album_picture_uid', $albumPcitureUidArray)
                ->get()
                ->keyBy('album_picture_uid');

            $paramArray = [
                'userInfoCollection' => $userInfoCollection,
                'userAlbumCollection' => $userAlbumCollection,
                'userAvatarCollection' => $userAvatarCollection,
                'albumPictureCollection' => $albumPictureCollection,
                'UserCascaderCollection' => $UserCascaderCollection,
            ];

            //p($paramArray);

            $esDataArray = $userCollection->map(function ($userObject) use ($paramArray) {
                extract($paramArray);

                plog(['info' => 'es批量同步用户数据','userObject' => $userObject, 'userInfoCollection' => $userInfoCollection, 'userAlbumCollection' => $userAlbumCollection, 'userAvatarCollection' => $userAvatarCollection, 'albumPictureCollection' => $albumPictureCollection], 'EsSyncUserFacadeService', 'syncUser');

                $userInfoObject = $userInfoCollection->get($userObject->user_uid);
                $userAvatarObject = $userAvatarCollection->get($userObject->user_uid);
                $albumObject = $userAlbumCollection->get($userObject->user_uid);
                $albumPictureObject = $albumPictureCollection->get($userAvatarObject->album_picture_uid);
                $userCascaderObject = $UserCascaderCollection->get($userObject->user_uid);

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
                    'nick_name' => $userInfoObject->nick_name,
                    'real_name' => $userInfoObject->real_name,
                    'id_number' => $userInfoObject->id_number,
                    'sex' => $userInfoObject->sex,
                    'solar_birthday_at' => $userInfoObject->solar_birthday_at,
                    'solar_birthday_time' => $userInfoObject->solar_birthday_time,
                    'chinese_birthday_at' => $userInfoObject->chinese_birthday_at,
                    'chinese_birthday_time' => $userInfoObject->chinese_birthday_time,
                    'introduction' => $userInfoObject->introduction,
                    //album
                    'ablum_uid' => $albumObject->album_uid,
                    //avatar
                    'avatar' => $avatarUrl,
                    //cascader
                    'role_cascader_json' => $userCascaderObject?->role_cascader_json,

                ];
            })->toArray();

            //p($esDataArray);

            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步用户数据失败','$result' => $result], 'EsSyncUserFacadeService', 'syncUserError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => '批量同步用户数据完成','total' => $total,'costTime' => $costTime], 'EsSyncUserFacadeService', 'syncUser');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有users数据同步ES结束--2', 'info');
        }
    }

    /**
     * 执行用户账户同步
     */
    public function syncUserAomunt(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有user_amounts数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.user.user_amounts');

        UserAmount::queryByAllShard()
        ->select(['user_amount_uid','shard_key','user_uid','amount', 'bonus', 'prepare_bonus','coin','score', 'note','sort','created_at', 'updated_at','deleted_at'])
        //->with(['userInfo'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $userAmountCollection = $chunk;

            $esDataArray = $userAmountCollection->map(function ($userAmountObject) {
                $configKey = get_shard_config_key();

                return [
                    '_docId' => $userAmountObject->user_amount_uid,
                    'shard_key' => $userAmountObject->shard_key,
                    'shard_db' => ShardFacade::getDbName($userAmountObject->user_uid, $configKey),
                    'shard_table' => ShardFacade::getTableName($userAmountObject->user_uid, 'user_amounts', $configKey),
                    'user_uid' => $userAmountObject->user_uid,
                    'amount' => $userAmountObject->amount,
                    'bonus' => $userAmountObject->bonus,
                    'prepare_bonus' => $userAmountObject->prepare_bonus,
                    'coin' => $userAmountObject->coin,
                    'score' => $userAmountObject->score,
                    'note' => $userAmountObject->note,
                    'sort' => $userAmountObject->sort,
                    'created_time' => $userAmountObject->created_time,
                    'updated_time' => $userAmountObject->updated_time,
                    'created_at' => $userAmountObject->created_at,
                    'updated_at' => $userAmountObject->updated_at,
                    'deleted_at' => $userAmountObject->deleted_at
                ];
            })->toArray();

            //p($esDataArray);

            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步用户账户数据失败','$result' => $result], 'EsSyncUserFacadeService', 'syncUserAomuntError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => '批量同步用户账户数据完成','total' => $total,'costTime' => $costTime], 'EsSyncUserFacadeService', 'syncUserAomunt');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有user_amounts数据同步ES结束--2', 'info');
        }
    }
}
