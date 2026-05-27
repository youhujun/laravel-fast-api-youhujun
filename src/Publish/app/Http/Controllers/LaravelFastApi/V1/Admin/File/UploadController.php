<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 18:51:17
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-20 12:37:44
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\File\UploadController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\File;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckArray;
use App\Rules\Pub\FormatTime;
use App\Rules\Pub\CheckUnique;
use App\Rules\Pub\ChineseCodeNumberLine;
use App\Rules\Pub\FileType;
use App\Exceptions\Common\RuleException;
use App\Rules\LaravelFastApi\V1\Admin\File\Action;
use App\Rules\LaravelFastApi\V1\Admin\File\UseType;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadConfigFileDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadFileDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadSinglePicutureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadMultiplePicutureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadUserAvatarDTO;
use App\DTOs\LaravelFastApi\V1\Admin\File\UploadResetPictureDTO;
use App\Facades\LaravelFastApi\V1\Admin\File\AdminUploadFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\File\AdminUploadFacadeService
 */
class UploadController extends Controller
{
    /**
    * Undocumented function  上传配置文件
    *
    * @param Request $request
    * @return void
    */
    public function uploadConfigFile(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $uploadConfigFileDTO = (new UploadConfigFileDTO())->validate($request->all());

            // p($uploadConfigFileDTO);
            // die;

            if ($request->hasFile('file')) {
                $file = $request->file('file');

                $result = AdminUploadFacade::uploadConfigFile($adminObject,$uploadConfigFileDTO,$file);
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
    public function uploadFile(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $uploadFileDTO = (new UploadFileDTO())->validate($request->all());

            if ($uploadFileDTO) {
                if ($request->hasFile('file')) {
                    $file = $request->file('file');

                    $result = AdminUploadFacade::uploadFile($adminObject, $uploadFileDTO, $file);
                }
            }
        }

        return $result;
    }

    /**
     * 上传单图
     *
     * @param Request $request
     * @return void
     */
    public function uploadSinglePicture(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $uploadSinglePicutureDTO = (new UploadSinglePicutureDTO())->validate($request->all());

            if ($uploadSinglePicutureDTO) {
                if ($request->hasFile('picture')) {
                    $picture = $request->file('picture');

                    $result = AdminUploadFacade::uploadSinglePicture($adminObject, $uploadSinglePicutureDTO, $picture);
                }
            }
        }

        return $result;
    }

    /**
     * 上传多图
     *
     * @param Request $request
     * @return void
     */
    public function uploadMultiplePicture(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $uploadMultiplePicutureDTO = (new UploadMultiplePicutureDTO())->validate($request->all());

            if ($uploadMultiplePicutureDTO) {
                if ($request->hasFile('picture')) {
                    $pictures = $request->file('picture');

                    $result = AdminUploadFacade::uploadMultiplePicture($adminObject, $uploadMultiplePicutureDTO, $pictures);
                }
            }
        }

        return $result;
    }

    /**
     * 上传用户头像
     *
     * @param Request $request
     * @return void
     */
    public function uploadUserAvatar(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $uploadUserAvatarDTO = (new UploadUserAvatarDTO())->validate($request->all());

            //p($uploadUserAvatarDTO);die;

            if ($uploadUserAvatarDTO) {
                if ($request->hasFile('picture')) {
                    $picture = $request->file('picture');

                    $result = AdminUploadFacade::uploadUserAvatar($adminObject, $uploadUserAvatarDTO, $picture);
                }
            }
        }

        return $result;
    }


    /**
    * 上传替换图片
    *
    * @param Request $request
    * @return void
    */
    public function uploadResetPicture(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $uploadResetPictureDTO = (new UploadResetPictureDTO())->validate($request->all());

           //p($uploadResetPictureDTO);die;

            if ($uploadResetPictureDTO) {

                $result = code(\config('admin_code.UploadFileTypeError'));
                
                if ($request->hasFile('picture')) {
                    $picture = $request->file('picture');

                    $result = AdminUploadFacade::uploadResetPicture($adminObject, $uploadResetPictureDTO, $picture);
                }
            }
        }

        return $result;
    }
}
