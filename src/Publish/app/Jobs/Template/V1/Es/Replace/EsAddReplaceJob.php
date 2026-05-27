<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-01 17:29:24
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-01 23:26:02
 * @FilePath: \youhu-laravel-api-12\app\Jobs\Template\V1\Es\Replace\EsAddReplaceJob.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Jobs\Template\V1\Es\Replace;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Middleware\RateLimited;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;

class EsAddReplaceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected User $userObject;
    /**
     * 任务尝试次数
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 3;

    /**
     * 任务失败前允许的最大异常数
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * 如果任务的模型不再存在，则删除该任务
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;
    /**
     * Create a new job instance.
     */
    public function __construct(User $userObject)
    {
        $this->userObject = $userObject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userObject = $this->userObject;

        $indexName = config('common_es.indices.user.users');

        $configKey = get_shard_config_key();

        $userInfoObject = UserInfo::queryByShard($userObject->biz_id)->where('user_uid', $userObject->biz_id)->first();

        $albumObject = Album::queryByShard($userObject->biz_id)->where('user_uid', $userObject->biz_id)->where('album_type', 20)->where('is_default', 1)->first();

        $albumPictureObject = AlbumPicture::queryByShard($albumObject?->biz_id)->where('user_uid', $userObject->biz_id)->first();

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

        $dataArray = [
            '_docId' => $userObject->user_uid,
            'shard_db' => ShardFacade::getDbName($userObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($userObject->user_uid, 'users', $configKey),
            'user_uid' => $userObject->user_uid,
            'phone' => $userObject->phone,
            'email' => $userObject->email,
            'account_name' => $userObject->account_name,
            'account_status' => $userObject->account_status,
            'invite_code' => $userObject->invite_code,
            'real_auth_status' => $userObject->real_auth_status,
            'level_id' => $userObject->level_id,
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
        ];

        $result = EsFacade::createDoc($indexName, $dataArray, $userObject->biz_id);

        plog(['info' => 'es用户数据完成','$result' => $result], 'AsyncUserJob', 'handle');

        if (!isset($result['code']) || $result['code'] != 0) {
            plog(['error' => 'es用户数据失败','$result' => $result], 'AsyncUserJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
