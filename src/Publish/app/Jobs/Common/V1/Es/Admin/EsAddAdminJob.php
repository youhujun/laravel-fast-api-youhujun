<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-04 00:28:15
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-21 01:43:30
 * @FilePath: \youhu-laravel-api-12\app\Jobs\Common\V1\Es\Admin\EsAddAdminJob.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Jobs\Common\V1\Es\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Middleware\RateLimited;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;

class EsAddAdminJob implements ShouldQueue
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



        $adminObject = Admin::queryByShard($userObject->biz_id)->where('user_uid', $userObject->biz_id)->first();

        if (!isset($adminObject->biz_id)) {
            plog(['error' => '查找管理员失败','$userObject' => $userObject], 'EsAddAdminJob', 'handleError');
        }

        $indexName = config('common_es.indices.user.admins');

        $configKey = get_shard_config_key();

        $userInfoObject = UserInfo::queryByShard($userObject->biz_id)->where('user_uid', $userObject->biz_id)->first();

        $albumObject = Album::queryByShard($userObject->biz_id)->where('admin_uid', $adminObject->biz_id)->where('album_type', 10)->where('is_default', 1)->first();

        $albumPictureObject = null;

        if ($albumObject) {
            $albumPictureObject = AlbumPicture::queryByShard($albumObject->biz_id)->where('user_uid', $userObject->biz_id)->first();
        }

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
            'chinese_birthday_at' => $userInfoObject->chinese_birthday_at,
            'sex' => $userInfoObject->sex,
            'introduction' => $userInfoObject->introduction,
            //album
            'ablum_uid' => $albumObject->album_uid,
            //avatar
            'avatar' => $avatarUrl,
        ];

        $result = EsFacade::createDoc($indexName, $dataArray, $adminObject->biz_id);

        plog(['info' => 'es添加管理员完成','$result' => $result], 'EsAddAdminJob', 'handle');

        if (!isset($result['code']) || $result['code'] != 0) {
            plog(['error' => 'es添加管理员失败','$result' => $result], 'EsAddAdminJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
