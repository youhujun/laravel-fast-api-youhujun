<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-25 03:40:14
 * @FilePath: \youhu-laravel-api-12\app\Policies\LaravelFastApi\V1\Admin\Picture\AlbumPolicy.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Policies\LaravelFastApi\V1\Admin\Picture;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Exceptions\Admin\CommonException;

class AlbumPolicy
{
    //use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 更新相册
     *
     * @param Admin $adminObject
     * @param Album $album
     * @return void
     */
    public function update(Admin $adminObject, string|int $album_uid)
    {
        $result = $this->common($adminObject, $album_uid);

        return $result;
    }

    /**
     * 删除相册
     *
     * @param Admin $adminObject
     * @param [type] $id
     * @return void
     */
    public function delete(Admin $adminObject, string|int $album_uid)
    {
        $result = $this->common($adminObject, $album_uid);

        return $result;
    }

    /**
     * 查看相册图片
     *
     * @param Admin $adminObject
     * @param [type] $valdiated
     * @return void
     */
    public function getAlbumPicture(Admin $adminObject, string|int $album_uid)
    {
        $result = $this->common($adminObject, $album_uid);

        return $result;
    }


    /**
     * 授权共同处理逻辑
     *
     * @param Admin $adminObject
     * @param [type] $valdiated
     * @return void
     */
    protected function common(Admin $adminObject, string|int $album_uid)
    {
        $result = true;

        //如果是开发者,超级管理员,相册管理员就直接放过
        if (is_develop($adminObject) || is_super($adminObject) || is_album_admin($adminObject)) {
            return true;
        }


        $indexName = config('common_es.indices.album.albums');

        $esAlbumObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('album_uid', $album_uid)->get()->first();

        //降级熔断
        if (!isset($esAlbumObject->album_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        if ($adminObject->biz_id !== $esAlbumObject->admin_uid) {
            $result = false;
        }

        return $result;
    }
}
