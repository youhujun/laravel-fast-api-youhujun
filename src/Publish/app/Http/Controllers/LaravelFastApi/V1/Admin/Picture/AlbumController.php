<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-01 22:38:55
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-25 03:41:15
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\Picture\AlbumController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Picture;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\GetDefaultAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\FindAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\AddAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\GetAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\UpdateAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\DeleteAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\GetAlbumPictureDTO;
use App\Facades\LaravelFastApi\V1\Admin\Picture\AdminAlbumFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Picture\AdminAlbumFacadeService
 */
class AlbumController extends Controller
{
    /**
     * 获取默认相册
     *
     * @param Request $request
     * @return void
     */
    public function getDefaultAlbum(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetDefaultAlbumDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            $result =  AdminAlbumFacade::getDefaultAlbum($requestDTO, $adminObject);
        }

        return $result;
    }


    /**
     * 查找相册
     *
     * @param Request $request
     * @return void
     */
    public function findAlbum(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new FindAlbumDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            $result =  AdminAlbumFacade::findAlbum($requestDTO, $adminObject);
        }

        return $result;
    }
    /**
     * 获取相册
     *
     * @param AlbumRequest $request
     * @return void
     */
    public function getAlbum(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetAlbumDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            $result = AdminAlbumFacade::getAlbum($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 添加相册
     *
     * @param Request $request
     * @return void
     */
    public function addAlbum(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.apiAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddAlbumDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            $result = AdminAlbumFacade::addAlbum($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 更新相册
     *
     * @param Request $request
     * @return void
     */
    public function updateAlbum(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.apiAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateAlbumDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            /**
             * @see \App\Policies\LaravelFastApi\V1\Admin\Picture\AlbumPolicy
             */
            if (Gate::forUser($adminObject)->allows('update-album', $requestDTO->album_uid)) {
                $result = AdminAlbumFacade::updateAlbum($requestDTO, $adminObject);
            }
        }
        return $result;
    }

    /**
     * 删除相册
     *
     * @param Request $request
     * @return void
     */
    public function deleteAlbum(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.apiAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteAlbumDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            /**
             * @see \App\Policies\LaravelFastApi\V1\Admin\Picture\AlbumPolicy
             */
            if (Gate::forUser($adminObject)->allows('delete-album', $requestDTO->album_uid)) {
                $result = AdminAlbumFacade::deleteAlbum($requestDTO, $adminObject);
            }
        }
        return $result;
    }

    /**
     * 查询相册
     *
     * @param Request $request
     * @return void
     */
    public function getAlbumPicture(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(config('admin_code.apiAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetAlbumPictureDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            /**
             * @see \App\Policies\LaravelFastApi\V1\Admin\Picture\AlbumPolicy
             */
            if (Gate::forUser($adminObject)->allows('get-album-picture', $requestDTO->album_uid)) {
                $result = AdminAlbumFacade::getAlbumPicture($requestDTO, $adminObject);
            }
        }

        return $result;
    }
}
