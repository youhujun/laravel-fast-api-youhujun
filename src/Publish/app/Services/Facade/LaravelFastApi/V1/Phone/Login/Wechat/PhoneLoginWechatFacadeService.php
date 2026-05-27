<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 00:42:52
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Phone\Login\Wechat\PhoneLoginWechatFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\Login\Wechat;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Exceptions\Phone\CommonException;
use App\Events\Phone\CommonEvent;
use App\Events\LaravelFastApi\V1\Phone\User\UserLoginEvent;
use App\Events\Es\V1\User\User\EsUserUpdateEvent;
use App\Events\Common\V1\User\User\EsAddUserEvent;
//Model
use App\Models\LaravelFastApi\V1\System\Platform\SystemWechatConfig;
use App\Models\LaravelFastApi\V1\User\Platform\UserWechatUnionid;
use App\Models\LaravelFastApi\V1\User\Union\UserSystemWechatConfigUnion;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
//resource
use App\Http\Resources\LaravelFastApi\V1\Es\Phone\User\User\UserLoginResource;
use App\Facades\Common\V1\Login\CommonLoginFacade;
use App\Facades\Common\V1\User\User\CommonUserFacade;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\Login\Wechat\LoginController
 * @see \App\Facades\LaravelFastApi\V1\Phone\Login\Wechat\PhoneLoginWechatFacade
 */
class PhoneLoginWechatFacadeService
{
    public function test()
    {
        echo "PhoneLoginWechatFacadeService test";
    }

    /**
    * 通过微信官方接口登录或注册用户。
    *
    * 此方法首先检查用户是否存在，如果用户不存在，则根据提供的 unionid 或 openid 注册新用户。
    * 如果用户已存在，则将微信的 openid 绑定到用户账户上。最后，执行用户登录操作。
    *
    * @param \Illuminate\Support\Collection $collection 包含认证结果和用户信息的集合。
    * @return array 返回登录结果，包括状态码和消息。
    */
    public function loginAndRegisterWithOfficial(Collection $collection)
    {
        plog(['loginAndRegisterWithOfficial' => $collection], 'LoginAndRegister', 'LoginAndRegister');

        $authResultArray = $collection->get('authResultArray');
        $wechatOfficialObject = $collection->get('wechatOfficialObject');

        //plog(['authResultArray'=>$authResultObject],'LoginAndRegister','authResultArray');
        //plog(['wechatOfficialObject'=>$wechatOfficial],'LoginAndRegister','wechatOfficialObject');

        //先定义用户uid容器
        $user_uid = null;

        $openid = $authResultArray['openid'];
        $system_wechat_config_id = $wechatOfficialObject->id;

        $propertyCollection = collect([
            'access_token' => $authResultArray['access_token'],
            'expires_in' => $authResultArray['expires_in'],
            'refresh_token' => $authResultArray['refresh_token'],
            'scope' => $authResultArray['scope'],
            'type' => 30
        ]);

        $userInfoColection = null;

        if (isset($authResultArray['userinfo']) && isset($authResultArray['userinfo']['openid'])) {
            $userInfoColection = collect([
                'nickname' => $authResultArray['userinfo']['nickname'],
                'sex' => $authResultArray['userinfo']['sex'],
                'headimgurl' => $authResultArray['userinfo']['headimgurl']
            ]);
        }

        // 如果有unionid
        // 4. 修改：判断数组是否有 unionid 键（而非对象属性）
        if (isset($authResultArray['unionid'])) {
            $unionid = $authResultArray['unionid'];

            // 通过Unionid检测用户是否存在
            $checkUnionIdResult = $this->checkHasUserByUnionid($unionid);

            // 不存在就注册用户
            if (!$checkUnionIdResult) {
                // 注册用户
                $paramArray['source'] = 60;
                $userObject = CommonUserFacade::registerUser($paramArray);

                $user_uid = $userObject->user_uid;

                $this->bindUserWechatUnionid($user_uid, $unionid);

                $this->bindUserWechatOpenid($user_uid, $openid, $system_wechat_config_id, $propertyCollection);
            } else {
                // 检测openid
                $checkOpenIdResult = $this->checkHasUserByOpenid($openid, $system_wechat_config_id);

                // 如果没有就需要绑定
                if (!$checkOpenIdResult) {
                    $indexName = config('common_es.indices.union.user_wechat_unionids');

                    $esUserWechatUnionidObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('unionid', $unionid)->get()->first();

                    $user_uid = $esUserWechatUnionidObject->user_uid;

                    $this->bindUserWechatOpenid($user_uid, $openid, $system_wechat_config_id, $propertyCollection);
                }
            }
        } else {
            // 没有直接检测 openid
            $checkOpenIdResult = $this->checkHasUserByOpenid($openid, $system_wechat_config_id);

            // 如果没有就注册
            if (!$checkOpenIdResult) {
                // 注册用户
                $paramArray['source'] = 50;
                $userObject = CommonUserFacade::registerUser($paramArray);

                $user_uid = $userObject->biz_id;

                $this->bindUserWechatOpenid($user_uid, $openid, $system_wechat_config_id, $propertyCollection);
            } else {
                //如果有直接查询
                $indexName = config('common_es.indices.union.user_system_wechat_config_unions');

                $esUserWechatOpenidObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('openid', $openid)->where('system_wechat_config_id', $system_wechat_config_id)->get()->first();

                if (!$esUserWechatOpenidObject) {
                    throw new CommonException('ServiceBusyError');
                }

                $user_uid = $esUserWechatOpenidObject->user_uid;

                $userObject = User::queryByShard($user_uid)->where('user_uid', $user_uid)->first();

                if (!isset($userObject->biz_id)) {
                    throw new CommonException('ThatDataNotExistsError');
                }
            }
        }

        $this->bindWeChatUserInfo($userObject, $userInfoColection);

        // 执行登录
        $this->loginUserByOpenid($user_uid, $openid, $system_wechat_config_id);

        $data['data'] = new UserLoginResource($userObject);

        $result = code(['code' => 0,'msg' => 'server login success!'], $data);

        return $result;
    }

    /**
     * 登录后绑定微信
     *
     * @param  Collection $collection
     * @param  User       $userObject
     */
    public function bindUserByOfficial(Collection $collection, User $userObject)
    {
        $authResultArray = $collection->get('authResultArray');
        $wechatOfficialObject = $collection->get('wechatOfficialObject');

        //先定义用户uid容器
        $user_uid = null;

        $openid = $authResultArray['openid'];

        $system_wechat_config_id = $wechatOfficialObject->id;

        $propertyCollection = collect([
            'access_token' => $authResultArray['access_token'],
            'expires_in' => $authResultArray['expires_in'],
            'refresh_token' => $authResultArray['refresh_token'],
            'scope' => $authResultArray['scope'],
            'type' => 30
        ]);

        $userInfoColection = null;

        if (isset($authResultArray['userinfo']) && isset($authResultArray['userinfo']['openid'])) {
            $userInfoColection = collect([
                'nickname' => $authResultArray['userinfo']['nickname'],
                'sex' => $authResultArray['userinfo']['sex'],
                'headimgurl' => $authResultArray['userinfo']['headimgurl']
            ]);
        }

        // 如果有unionid
        // 4. 修改：判断数组是否有 unionid 键（而非对象属性）
        if (isset($authResultArray['unionid'])) {
            $unionid = $authResultArray['unionid'];

            // 通过Unionid检测用户是否存在
            $checkUnionIdResult = $this->checkHasUserByUnionid($unionid);

            // 不存在就绑定
            if (!$checkUnionIdResult) {
                $user_uid = $userObject->user_uid;

                $this->bindUserWechatUnionid($user_uid, $unionid);

                $this->bindUserWechatOpenid($user_uid, $openid, $system_wechat_config_id, $propertyCollection);
            } else {
                // 检测openid
                $checkOpenIdResult = $this->checkHasUserByOpenid($openid, $system_wechat_config_id);

                // 如果没有就需要绑定
                if (!$checkOpenIdResult) {
                    $indexName = config('common_es.indices.union.user_wechat_unionids');

                    $esUserWechatUnionidObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('unionid', $unionid)->get()->first();

                    $user_uid = $esUserWechatUnionidObject->user_uid;

                    $this->bindUserWechatOpenid($user_uid, $openid, $system_wechat_config_id, $propertyCollection);
                }
            }
        } else {
            // 没有直接检测 openid
            $checkOpenIdResult = $this->checkHasUserByOpenid($openid, $system_wechat_config_id);

            // 如果没有就绑定
            if (!$checkOpenIdResult) {
                $this->bindUserWechatOpenid($user_uid, $openid, $system_wechat_config_id, $propertyCollection);
            } else {
                //如果有直接查询
                $indexName = config('common_es.indices.union.user_system_wechat_config_unions');

                $esUserWechatOpenidObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('openid', $openid)->where('system_wechat_config_id', $system_wechat_config_id)->get()->first();

                if (!$esUserWechatOpenidObject) {
                    throw new CommonException('ServiceBusyError');
                }
            }
        }

        $this->bindWeChatUserInfo($userObject, $userInfoColection);

        $data['data'] = new UserLoginResource($userObject);

        $result = code(['code' => 0,'msg' => 'server bind success!'], $data);

        return $result;
    }

    /**
     * 通过uninodid检测用户是否存在
     */
    public function checkHasUserByUnionid(string $unionid)
    {
        $indexName = config('common_es.indices.union.user_wechat_unionids');
        //检测结果
        $checkUnionIdResult = false;

        $esUserWechatUnionidCount = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('unionid', $unionid)->get()->count();

        //检测unionid是否存在
        if ($esUserWechatUnionidCount) {
            $checkUnionIdResult = true;
        }

        return $checkUnionIdResult;
    }

    /**
     * 检测用户是否有openid
     *
     * @param  [type] $openid
     */
    public function checkHasUserByOpenid(string $openid, string $system_wechat_config_id)
    {
        $indexName = config('common_es.indices.union.user_system_wechat_config_unions');
        //检测结果
        $checkOpenIdResult = false;

        //检测openid是否存在
        $esUserWechatOpenidCount = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('openid', $openid)->where('system_wechat_config_id', $system_wechat_config_id)->get()->count();

        if ($esUserWechatOpenidCount) {
            $checkOpenIdResult = true;
        }

        return $checkOpenIdResult;
    }


    /**
     * 绑定用户的微信 UnionID
     *
     * @param int $user_uid 用户的 ID
     * @param string $unionid 用户的微信 UnionID
     * @throws CommonException 如果绑定失败，则抛出异常
     */
    protected function bindUserWechatUnionid(string $user_uid, string $unionid)
    {
        $insertDataArray = [
            'user_wechat_unionid_uid' => get_snow_flake_id(),
            'user_uid' => $user_uid,
            'unionid' => $unionid,
            'sort' => 100
        ];

        $userWechatUnionidObject = ShardHelperFacade::createWithShard(UserWechatUnionid::class, $user_uid, $insertDataArray);

        if (!isset($userWechatUnionidObject->biz_id)) {
            throw new CommonException('BindUserWechatUnionidError');
        }

        $indexName = config('common_es.indices.union.user_wechat_unionids');

        $configKey = get_shard_config_key();

        $insertDataArray = [
            '_docId' => $userWechatUnionidObject->biz_id,
            'user_wechat_unionid_uid' => $userWechatUnionidObject->biz_id,
            'user_uid' => $userWechatUnionidObject->user_uid,
            'shard_key' => $userWechatUnionidObject->shard_key,
            'shard_db' => ShardFacade::getDbName($userWechatUnionidObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($userWechatUnionidObject->user_uid, 'user_system_wechat_config_unions', $configKey),
            'unionid' => $userWechatUnionidObject->unionid,
            'sort' => $userWechatUnionidObject->sort,
            'created_at' => $userWechatUnionidObject->created_at,
            'updated_at' => $userWechatUnionidObject->updated_at,
            'created_time' => $userWechatUnionidObject->created_time,
            'updated_time' => $userWechatUnionidObject->updated_time,
            'deleted_at' => $userWechatUnionidObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $userWechatUnionidObject->biz_id);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es绑定用户微信unionid失败','$userWechatUnionidObject' => $userWechatUnionidObject,'$esResult' => $esResult], 'PhoneLoginWechatFacadeService', 'bindUserWechatUnionidError');
        }
    }


    /**
     * 绑定用户的微信 OpenID 到系统配置中
     *
     * @param string $user_uid 用户 ID
     * @param string $openid 微信 OpenID
     * @param string $system_wechat_config_id 系统微信配置 ID
     * @param Collection $propertyCollection 属性集合，包含微信用户的相关信息
     *
     * @throws CommonException 如果绑定失败，抛出异常
     */
    protected function bindUserWechatOpenid(string $user_uid, string $openid, string $system_wechat_config_id, Collection $propertyCollection)
    {
        $userSystemWechatConfigUnionObject = new UserSystemWechatConfigUnion();

        $insertDataArray = [
            'user_system_wechat_config_union_uid' => get_snow_flake_id(),
            'user_uid' => $user_uid,
            'openid' => $openid,
            'system_wechat_config_id' => $system_wechat_config_id,
            'verified_at' => date('Y-m-d H:i:s', time()),
            'verified_time' => time(),
            'type' => $propertyCollection->get('type') ? $propertyCollection->get('type') : 0,
            'session_key' => $propertyCollection->get('session_key') ? $propertyCollection->get('session_key') : '',
            'access_token' => $propertyCollection->get('access_token') ? $propertyCollection->get('access_token') : '',
            'expires_in' => $propertyCollection->get('expires_in') ? $propertyCollection->get('expires_in') : 0,
            'refresh_token' => $propertyCollection->get('refresh_token') ? $propertyCollection->get('refresh_token') : '',
            'scope' => $propertyCollection->get('scope') ? $propertyCollection->get('scope') : '',
            'is_snapshotuser' => $propertyCollection->get('is_snapshotuser') ? $propertyCollection->get('is_snapshotuser') : ''
        ];

        $userSystemWechatConfigUnionObject = ShardHelperFacade::createWithShard(UserSystemWechatConfigUnion::class, $user_uid, $insertDataArray);

        if (!isset($userSystemWechatConfigUnionObject->biz_id)) {
            throw new CommonException('BindUserWechatOpendidError');
        }

        $indexName = config('common_es.indices.union.user_system_wechat_config_unions');

        $configKey = get_shard_config_key();

        $insertDataArray = [
            '_docId' => $userSystemWechatConfigUnionObject->biz_id,
            'user_system_wechat_config_union_uid' => $userSystemWechatConfigUnionObject->biz_id,
            'user_uid' => $userSystemWechatConfigUnionObject->user_uid,
            'shard_key' => $userSystemWechatConfigUnionObject->shard_key,
            'shard_db' => ShardFacade::getDbName($userSystemWechatConfigUnionObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($userSystemWechatConfigUnionObject->user_uid, 'user_system_wechat_config_unions', $configKey),
            'openid' => $userSystemWechatConfigUnionObject->openid,
            'system_wechat_config_id' => $userSystemWechatConfigUnionObject->system_wechat_config_id,
            'verified_at' => $userSystemWechatConfigUnionObject->verified_at,
            'verified_time' => $userSystemWechatConfigUnionObject->verified_time,
            'type' => $userSystemWechatConfigUnionObject->type,
            'session_key' => $userSystemWechatConfigUnionObject->session_key,
            'access_token' => $userSystemWechatConfigUnionObject->access_token,
            'expires_in' => $userSystemWechatConfigUnionObject->expires_in,
            'refresh_token' => $userSystemWechatConfigUnionObject->refresh_token,
            'scope' => $userSystemWechatConfigUnionObject->scope,
            'is_snapshotuser' => $userSystemWechatConfigUnionObject->is_snapshotuser,
            'created_at' => $userSystemWechatConfigUnionObject->created_at,
            'updated_at' => $userSystemWechatConfigUnionObject->updated_at,
            'created_time' => $userSystemWechatConfigUnionObject->created_time,
            'updated_time' => $userSystemWechatConfigUnionObject->updated_time,
            'deleted_at' => $userSystemWechatConfigUnionObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $userSystemWechatConfigUnionObject->biz_id);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es绑定用户微信openid失败','$userSystemWechatConfigUnionObject' => $userSystemWechatConfigUnionObject,'$esResult' => $esResult], 'PhoneLoginWechatFacadeService', 'bindUserWechatOpenidError');

            throw new CommonException('EsBindUserWechatOpendidError');
        }
    }


    /**
     * 绑定微信用户信息到系统用户。
     *
     * @param User $userObject 系统用户对象。
     * @param Collection $userInfoColection 微信用户信息集合。
     *
     * @throws CommonException 如果绑定用户信息或头像失败，将抛出异常。
     */
    protected function bindWeChatUserInfo(User $userObject, ?Collection $userInfoColection = null)
    {
        if ($userInfoColection && $userInfoColection->count()) {
            $user_uid = $userObject->user_uid;
            //先处理用户详情
            $userInfoObject = UserInfo::queryByShard($user_uid)->where('user_uid', $user_uid)->first();

            $sexMapArray = ['0' => 0,'1' => 10,'2' => 20];

            $sexIndex = $userInfoColection->get('sex') ? $userInfoColection->get('sex') : 0;

            $sex = $sexMapArray[$sexIndex];

            $nick_name =  $userInfoColection->get('nickname') ? $userInfoColection->get('nickname') : '';

            if (!$userInfoObject) {
                $userInfoInsertDataArray = [
                    'user_info_uid' => get_snow_flake_id(),
                    'user_uid' => $user_uid,
                    'nick_name' => $nick_name,
                    'sex' => $sex,
                ];

                $userInfoObject = ShardHelperFacade::createWithShard(UserInfo::class, $user_uid, $userInfoInsertDataArray);

                if (!isset($userInfoObject->biz_id)) {
                    throw new CommonException('BindUserWechatUserInfoError');
                }
            } else {
                $updateUserInfoDataArray = [
                    'nick_name' => $nick_name,
                    'sex' => $sex,
                ];

                $updateUserInfoResult = $userInfoObject->updateWithShard($updateUserInfoDataArray);

                if (!$updateUserInfoResult) {
                    throw new CommonException('UpateUserWechatUserInfoError');
                }
            }

            //再处理用户头像
            //先查相册
            $userAlbumObject = Album::queryByShard($user_uid)->where('user_uid', $user_uid)->where('album_type', 20)->where('is_default', 1)->first();

            if (!isset($userAlbumObject->biz_id)) {
                $userAlbumInsertDataArray = [
                    'album_uid' => get_snow_flake_id(),
                    'user_uid' => $user_uid,
                    'album_type' => 20,
                    'album_name' => $userObject->account_name ?? '默认相册',
                    'album_description' => $userObject->account_name ?? '默认相册',
                    'album_picture_uid' => get_cover_album_picture_uid(),
                    'is_default' => 1,
                    'sort' => 100,
                ];

                $userAlbumObject = ShardHelperFacade::createWithShard(Album::class, $user_uid, $userAlbumInsertDataArray);

                $indexName = config('common_es.indices.album.albums');

                $configKey = get_shard_config_key();

                $dataArray = [
                    '_docId' =>  $userAlbumObject->album_uid,
                    'shard_key'=>$userAlbumObject->shard_key,
                    'user_uid' => $userAlbumObject->user_uid,
                    'album_uid' => $userAlbumObject->album_uid,
                    'shard_db' => ShardFacade::getDbName($userAlbumObject->user_uid, $configKey),
                    'shard_table' => ShardFacade::getTableName($userAlbumObject->user_uid, 'albums', $configKey),
                    'is_default' => $userAlbumObject->is_default,
                    'album_name' => $userAlbumObject->album_name,
                    'album_description' => $userAlbumObject->album_description,
                    'sort' => $userAlbumObject->sort,
                    'cover_album_picture_uid' => $userAlbumObject->cover_album_picture_uid,
                    'album_type' => $userAlbumObject->album_type,

                ];

                $esResult = EsFacade::createDoc($indexName, $dataArray, $dataArray['album_uid']);

                if (!isset($esResult['code']) || $esResult['code'] != 0) {
                    plog(['error' => 'es添加用户相册失败','$userAlbumObject'=>$userAlbumObject,'$esResult' => $esResult], 'PhoneLoginWechatFacadeService', 'bindWeChatUserInfoError');
                    throw new CommonException('EsBindUserWechatUserInfoError');
                }
            }

            //创建头像
            $album_uid = $userAlbumObject->biz_id;

            $albumInsertDataArray = [
                'album_picture_uid' => get_snow_flake_id(),
                'user_uid' => $user_uid,
                'album_uid' => $album_uid,
                'picture_type' => 20,
                'picture_tag' => 'avatar',
                'picture_spec' => '300x200',
                'picture_name' => $userInfoObject->nick_name,
                'picture_url' => $userInfoColection->get('headimgurl') ? $userInfoColection->get('headimgurl') : '',
            ];

            $albumPictureObject = ShardHelperFacade::createWithShard(AlbumPicture::class, $album_uid, $albumInsertDataArray);

            if (!isset($albumPictureObject->biz_id)) {
                throw new CommonException('BindUserWechatAlbumPictureError');
            }

            $albumIndexName = config('common_es.indices.album.album_pictures');

            $configKey = get_shard_config_key();

            $insertDataArray = [
                '_docId' => $albumPictureObject->biz_id,
                'album_picture_uid' => $albumPictureObject->biz_id,
                'shard_key' => $albumPictureObject->shard_key,
                'shard_db' => ShardFacade::getDbName($albumPictureObject->user_uid, $configKey),
                'shard_table' => ShardFacade::getTableName($albumPictureObject->user_uid, 'album_pictures', $configKey),
                'user_uid' => $albumPictureObject->user_uid,
                'admin_uid' => $albumPictureObject->user_uid,
                'album_uid' => $albumPictureObject->album_uid,
                'picture_name' => $albumPictureObject->picture_name,
                'picture_tag' => $albumPictureObject->picture_tag,
                'picture_path' => $albumPictureObject->picture_path,
                'picture_file' => $albumPictureObject->picture_file,
                'picture_size' => $albumPictureObject->picture_size,
                'picture_spec' => $albumPictureObject->picture_spec,
                'picture_type' => $albumPictureObject->picture_type,
                'created_at' => $albumPictureObject->created_at,
                'created_time' => $albumPictureObject->created_time,
                'updated_at' => $albumPictureObject->updated_at,
                'updated_time' => $albumPictureObject->updated_time,
                'deleted_at' => $albumPictureObject->deleted_at,
            ];

            $esAlbumPictureResult = EsFacade::createDoc($albumIndexName, $insertDataArray, $albumPictureObject->biz_id);

            if (!isset($esAlbumPictureResult['code']) || $esAlbumPictureResult['code'] != 0) {
                plog(['error' => 'es同步相册图片数据失败','$albumPictureObject' => $albumPictureObject,'$esAlbumPictureResult' => $esAlbumPictureResult], 'PhoneLoginWechatFacadeService', 'bindWeChatUserInfoError');

                throw new CommonException('EsSaveUserAvatarError');
            }

            $userAvatarInsertDataArray = [
                'user_avatar_uid' => get_snow_flake_id(),
                'user_uid' => $user_uid,
                'album_picture_uid' => $albumPictureObject->biz_id,
                'is_default' => 1
            ];

            $userAvatarObject = ShardHelperFacade::createWithShard(UserAvatar::class, $user_uid, $userAvatarInsertDataArray);

            if (!isset($userAvatarResult->biz_id)) {
                throw new CommonException('BindUserWechatUserAvatarError');
            }

            //先查询es有没有
            $indexName = config('common_es.indices.user.users');

            $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid', $user_uid)->get()->first();

            $userObject = $userObject->fresh();
            //如果没有添加,否则更新
            if (!isset($esUserObject->id)) {
              
                EsAddUserEvent::dispatch($userObject);
            } else {
                EsUserUpdateEvent::dispatch($userObject);
            }
        }
    }

    /**
    * 通过openid登录用户
    *
    * @param  [$user_uid] $user_uid
    * @param  [string] $openid
    * @param  [string] $system_wechat_config_id
    */
    protected function loginUserByOpenid(string $user_uid, string $openid, string $system_wechat_config_id)
    {
        if (!$user_uid) {
            throw new CommonException('UserUidIsEmpty');
        }

        $userObject = User::queryByShard('user_uid')->where('user_uid', $user_uid)->first();

        if (isset($userObject->biz_id)) {
            Auth::setDefaultDriver('phone');

            $remember = true;

            Auth::login($userObject, $remember);

            //验证现在这个用户是否是登录状态
            CommonLoginFacade::checkResetLogin($userObject);

            UserLoginEvent::dispatch($userObject);

            $userObject = $userObject->fresh();

            EsUserUpdateEvent::dispatch($userObject);
        }
    }
}
