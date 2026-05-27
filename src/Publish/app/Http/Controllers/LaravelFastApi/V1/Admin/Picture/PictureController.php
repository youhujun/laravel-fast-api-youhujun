<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-01 22:38:55
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-20 12:34:16
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\Picture\PictureController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Picture;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\SetCoverDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\moveAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\MoveMultipleAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\DeletePictureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\DeleteMultiplePictureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Pciture\UpdatePictureNameDTO;
use App\Facades\LaravelFastApi\V1\Admin\Picture\AdminPictureFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Picture\AdminPictureFacadeService
 */
class PictureController extends Controller
{
    /**
     * 设为封面
     *
     * @param Request $request
     * @return void
     */
    public function setCover(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new SetCoverDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            /**
             * @see \App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy
             */
            if (Gate::forUser($adminObject)->allows('set-cover', $requestDTO)) {
                $result = AdminPictureFacade::setCover($requestDTO, $adminObject);
            }
        }
        return $result;
    }

    /**
    * 转移相册
    *
    * @param Request $request
    * @return void
    */
    public function moveAlbum(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new moveAlbumDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            /**
             * @see \App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy
             */
            if (Gate::forUser($adminObject)->allows('move-album', $requestDTO)) {
                $result = AdminPictureFacade::moveAlbum($requestDTO, $adminObject);
            }
        }
        return $result;
    }

    /**
     * 批量转移相册
     *
     * @param Request $request
     * @return void
     */
    public function moveMultipleAlbum(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MoveMultipleAlbumDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            /**
             * @see \App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy
             */
            if (Gate::forUser($adminObject)->allows('move-multiple-album', $requestDTO)) {
                $result = AdminPictureFacade::moveMultipleAlbum($requestDTO, $adminObject);
            }
        }
        return $result;
    }



    /**
     * 删除图片
     *
     * @param Request $request
     * @return void
     */
    public function deletePicture(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeletePictureDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            /**
             * @see \App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy
             */
            if (Gate::forUser($adminObject)->allows('delete-picture', $requestDTO)) {
                $result = AdminPictureFacade::deletePicture($requestDTO, $adminObject);
            }
        }

        return $result;
    }


    /**
     * 批量删除图片
     *
     * @param Request $request
     * @return void
     */
    public function deleteMultiplePicture(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteMultiplePictureDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            /**
             * @see \App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy
             */
            if (Gate::forUser($adminObject)->allows('delete-multiple-picture', $requestDTO)) {
                $result = AdminPictureFacade::deleteMultiplePicture($requestDTO, $adminObject);
            }
        }

        return $result;
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function updatePictureName(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdatePictureNameDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            /**
             * @see \App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy
             */
            if (Gate::forUser($adminObject)->allows('update-picture-name', $requestDTO)) {
                $result = AdminPictureFacade::updatePictureName($requestDTO, $adminObject);
            }
        }

        return $result;
    }
}
