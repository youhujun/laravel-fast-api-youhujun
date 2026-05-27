<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 15:44:57
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-25 01:42:36
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Common\V1\User\User\CommonUserFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Services\Facade\Common\V1\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use App\Exceptions\Common\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\DTOs\Contracts\V1\User\User\AddUserHandlerContractDTO;
use App\DTOs\Common\V1\User\User\BusinessRegisterUserDTO;
//注册用户(添加用户)
use App\Contracts\LaravelFastApi\V1\Common\User\AddUserHandlerContract;
use App\Events\Common\V1\User\User\CommonUserRegisterEvent;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\System\Level\UserLevel;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\User\Union\UserSystemWechatConfigUnion;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;

/**
 * @see \App\Facades\Common\V1\User\User\CommonUserFacade
 */
class CommonUserFacadeService
{
    /* public function test()
    {
        echo "CommonUserFacadeService test";
    } */

    /**
      * 注册用户
      *
      * @param  [array] $param 传递的参数
      * @param  [User] $userObject 可选参数用户对象,可以从外部注册用户,然后传递过来,如果不传递,就在这里注册用户
      * @return [User] $userObject 返回注册成功的用户对象
      */
    public function registerUser(BusinessRegisterUserDTO $businessDTO, ?User $userObject = null): User
    {
        //执行注册
        DB::beginTransaction();

        //如果没有从外面传入用户,就最简注册
        if (!isset($userObject) || empty($userObject)) {
            $user_uid = get_snow_flake_id();

            $userLevelObject = UserLevel::where('level_code','V0')->get()->first();

            $insertDataArray = [
                'user_uid' => $user_uid,
                'phone' => isset($paramArray['phone']) ? $paramArray['phone'] : null,
                'password' => isset($paramArray['password']) ? Hash::make($paramArray['password']) : null,
                'level_id' => $userLevelObject->id,
                'real_auth_status' => 10,
                'account_status' => 1,
                'account_name' => \bin2hex(\random_bytes(4)),
                'auth_token' => Str::uuid()->toString(),
                'source' => isset($paramArray['source']) ? $paramArray['source'] : 0
            ];

            $userObject = ShardHelperFacade::createWithShard(User::class, $user_uid, $insertDataArray);

            if (!isset($userObject->biz_id)) {
                DB::rollBack();
                throw new CommonException('AddUserError');
            }
        }

        CommonUserRegisterEvent::dispatch($businessDTO,$userObject);

        //契约参数处理
        $businessContractDTO = new AddUserHandlerContractDTO();
        $businessContractDTO->userObject = $userObject;
        $businessContractDTO->source_user_uid = isset($businessDTO->source_user_uid) ? $businessDTO->source_user_uid : 0;
        $businessContractDTO->invite_id = isset($businessDTO->invite_id) ? $businessDTO->invite_id : 0;
        $businessContractDTO->invite_code = isset($businessDTO->invite_code) ? $businessDTO->invite_code : '';

        app(AddUserHandlerContract::class)->handle($businessContractDTO);

        //提交
        DB::commit();

        return $userObject;
    }


    /**
     * 获取用户头像URL
     *
     * 优先从Redis缓存获取，缓存未命中时则查询数据库：
     * 1. 查询用户头像配置，获取头像图片UID
     * 2. 查询用户相册，获取相册UID
     * 3. 根据图片UID和相册UID查询图片详情
     * 4. 根据图片存储类型（本地/云端）构建头像URL
     * 5. 若以上步骤均未获取到头像，则使用系统默认头像
     * 6. 将结果写入Redis缓存
     *
     * @param User $userObject 用户对象
     * @return string 头像URL
     */
    public function getUserAvatar(User $userObject): string
    {
        $user_uid = $userObject->biz_id;

        $userAvatarObject = UserAvatar::queryByShard($user_uid)->orderBy('created_time', 'desc')->first();

        //用户头像图片uid
        $avatar_album_picture_uid =  $userAvatarObject?->album_picture_uid;

        $albumObject = Album::queryByShard($user_uid)->first();
        //用户相册uid
        $user_alubm_uid = $albumObject?->biz_id;

        $redisKey = config('common_redis.user_avatar_url.key');
        $redisField = config('common_redis.user_avatar_url.field');

        //定义头像
        $avatarUrl = Redis::hget($redisKey, $redisField.$user_uid);

        if (!$avatarUrl) {
            if ($avatar_album_picture_uid && $user_alubm_uid) {
                $albumPictureObject = AlbumPicture::queryByShard($user_alubm_uid)->where('album_picture_uid', $avatar_album_picture_uid)->first();

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
            }

            //兜底
            if (!$avatarUrl) {
                $avatarUrl = $this->getUserSysatemAvatar();
            }

            Redis::hset($redisKey, $redisField.$user_uid, $avatarUrl);
        }

        return $avatarUrl;
    }

    /**
     * 获取用户系统头像
     *
     * 此方法从相册中查找用户头像。如果未找到头像，则抛出异常。
     * 如果找到的头像类型不是 10，则返回头像的 URL；否则，返回存储路径的完整 URL。
     *
     * @return string|null 用户头像的 URL，若未找到则返回 null
     * @throws CommonException 当未找到相册图片对象时抛出
     */
    private function getUserSysatemAvatar(): string
    {
        $redisKey = config('common_redis.system_avatar_url.key');
        $redisField = config('common_redis.system_avatar_url.field');

        $avatarUrl = Redis::hget($redisKey, $redisField);

        if (!$avatarUrl) {
            $albumPictureObject = ShardHelperFacade::queryAllShards(
                AlbumPicture::class,
                function ($query) {
                    $query->where('picture_tag', 'avatar');
                },
                'picture_tag',
                ['avatar']
            )->first();

            $ablum_picture_pciture_type = $albumPictureObject?->picture_type;

            //本地存储
            if ($ablum_picture_pciture_type == 10) {
                $avatarUrl = asset('/storage'.$albumPictureObject->picture_path.$albumPictureObject->picture_file);
            }
            //云端存储
            if ($ablum_picture_pciture_type == 20) {
                $avatarUrl = $albumPictureObject->picture_url;
            }

            if (!$avatarUrl) {
                plog(['error' => '未配置系统默认头像','$albumPictureObject' => $albumPictureObject], 'CommonUserFacadeService', 'GetUserSysatemAvatarError');
                throw new CommonException('GetUserSysatemAvatarError');
            }

            Redis::hset($redisKey, $redisField, $avatarUrl);
        }

        return $avatarUrl;
    }

    /**
     * 获取用户的 OpenID
     *
     * @param object $userObject 用户对象，包含用户的基本信息
     * @return mixed string|null 返回用户的 OpenID，如果未找到则返回 null
     */
    public function getUserOpenid(User $userObject, int $openid_type = 10): mixed
    {
        $openid = null;

        $wechatIndexName = config('common_es.indices.union.user_system_wechat_config_unions');

        //es用户和微信配置对象
        $esUserWechatCofnigObject = null;

        //微信
        if (in_array($openid_type, [10,20,30])) {
            $esUserWechatCofnigObject = EsQueryFacade::index($wechatIndexName)->whereNull('deleted_at')->where('user_uid', $userObject->biz_id)->where('type', $openid_type)->get()->first();
        }

        if ($esUserWechatCofnigObject) {
            if (isset($esUserWechatCofnigObject?->openid) && $esUserWechatCofnigObject?->openid) {
                $openid = $esUserWechatCofnigObject->openid;
            }
        }

        return $openid;
    }
    /**
     * 获取用户的UnionID
     *
     * @param  User  $userObject
     * @return mixed
     */
    public function getUserUnionid(User $userObject): mixed
    {
        $unionid = null;

        //先从es查询
        $unionidIndexName = config('common_es.indices.union.user_wechat_unionids');

        $esUserWechatUnionidObject = EsQueryFacade::index($unionidIndexName)->whereNull('deleted_at')->where('user_uid', $userObject->user_uid)->get()->first();

        if (isset($userWechatUnionidObject->unionid)) {
            $unionid = $userSystemWechatConfigUnionObject->unionid;
        }

        return $unionid;
    }


    /**
     * 获取用户角色ID数组
     *
     * @param object $userObject 用户对象，包含用户的ID
     * @return array 返回与用户关联的角色ID数组
     */
    public function getUserRoleIdArray(User $userObject): array
    {
        $indexName = config('common_es.indices.union.user_role_unions');

        $esUserRoleUnionCollection = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid', $userObject->biz_id)->limit(100)->get();


        $role_id_array = [];

        foreach ($esUserRoleUnionCollection as $key => $esUserRoleUnionObject) {
            $role_id_array[] = $esUserRoleUnionObject->role_id;
        }

        return $role_id_array;
    }
}
