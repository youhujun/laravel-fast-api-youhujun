<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-16 13:54:49
 * @FilePath: \app\Policies\LaravelFastApi\V1\Admin\Picture\AlbumPolicy.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Policies\LaravelFastApi\V1\Admin\Picture;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\Picture\Album;

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
     * @param Admin $admin
     * @param Album $album
     * @return void
     */
    public function update($admin,$valdiated)
    {
       $result = $this->common($admin,$valdiated);

        return $result;
    }

    /**
     * 删除相册
     *
     * @param Admin $admin
     * @param [type] $id
     * @return void
     */
    public function delete($admin,$valdiated)
    {

        $result = $this->common($admin,$valdiated);

        return $result;
    }

    /**
     * 查看相册图片
     *
     * @param Admin $admin
     * @param [type] $valdiated
     * @return void
     */
    public function getAlbumPicture($admin,$valdiated)
    {
        $result = $this->common($admin,$valdiated);

        return $result;
    }


    /**
     * 授权共同处理逻辑
     *
     * @param Admin $admin
     * @param [type] $valdiated
     * @return void
     */
    protected function common($admin,$valdiated)
    {
        $result = true;

		//p($valdiated);die;

        $album_id = $valdiated['albumId'];

        $album = Album::find($album_id);

        if($admin->id !== $album->admin_id)
        {
            $result = false;
        }

        return $result;
    }


}
