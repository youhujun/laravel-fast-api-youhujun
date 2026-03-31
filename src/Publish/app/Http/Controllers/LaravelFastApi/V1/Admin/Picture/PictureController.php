<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-01 22:38:55
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-05 22:43:37
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\Picture\PictureController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Picture;

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
use App\Exceptions\Common\RuleException;
use App\Facades\LaravelFastApi\V1\Admin\Picture\AdminPictureFacade;

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
            $validator = Validator::make(
                $request->all(),
                [
                    'picture_id' => ['bail',new Required(),new Numeric()],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['picture_id'])) {
                throw new RuleException('RuleRequiredError', 'picture_id');
            }

            if (is_develop($adminObject) || isSuperAdmin($adminObject)) {
                $result = AdminPictureFacade::setCover($validated, $adminObject);
            } else {
                if (Gate::forUser($adminObject)->allows('set-cover', $validated['id'])) {
                    $result = AdminPictureFacade::setCover($validated, $adminObject);
                }
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
            $validator = Validator::make(
                $request->all(),
                [
                    'album_id' => ['bail',new Required(),new Numeric()],
                    'picture_id' => ['bail',new Required(),new Numeric()],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['album_id'])) {
                throw new RuleException('RuleRequiredError', 'album_id');
            }

            if (!isset($validated['pictureId'])) {
                throw new RuleException('RuleRequiredError', 'pictureId');
            }

            if (is_develop($adminObject) || isSuperAdmin($adminObject)) {
                $result = AdminPictureFacade::moveAlbum($validated, $adminObject);
            } else {
                if (Gate::forUser($adminObject)->allows('move-album', [$validated['album_id'],$validated['picture_id']])) {
                    $result = AdminPictureFacade::moveAlbum($validated, $adminObject);
                }
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
            $validator = Validator::make(
                $request->all(),
                [
                    'album_id' => ['bail',new Required(),new Numeric()],
                    'pictureId' => ['bail',new Required(),new CheckArray()]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['album_id'])) {
                throw new RuleException('RuleRequiredError', 'album_id');
            }

            if (!isset($validated['pictureId'])) {
                throw new RuleException('RuleRequiredError', 'pictureId');
            }

            if (is_develop($adminObject) || isSuperAdmin($adminObject)) {
                $result = AdminPictureFacade::moveMultipleAlbum($validated, $adminObject);
            } else {
                if (Gate::forUser($adminObject)->allows('move-multiple-album', [$validated['album_id'],$validated['pictureId']])) {
                    $result = AdminPictureFacade::moveMultipleAlbum($validated, $adminObject);
                }
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
            $validator = Validator::make(
                $request->all(),
                [
                    'picture_id' => ['bail',new Required(),new Numeric()],
                ],
                []
            );

            $validated = $validator->validated();

            if (is_develop($adminObject) || isSuper($adminObject)) {
                $result = AdminPictureFacade::deletePicture($validated, $adminObject);
            } else {
                if (Gate::forUser($adminObject)->allows('delete-picture', $validated['picture_id'])) {
                    $result = AdminPictureFacade::deletePicture($validated, $adminObject);
                }
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
            $validator = Validator::make(
                $request->all(),
                [
                    'pictureId' => ['bail',new Required(),new CheckArray()]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['album_id'])) {
                throw new RuleException('RuleRequiredError', 'album_id');
            }

            if (!isset($validated['pictureId'])) {
                throw new RuleException('RuleRequiredError', 'pictureId');
            }

            if (is_develop($adminObject) || isSuperAdmin($adminObject)) {
                $result = AdminPictureFacade::deleteMultiplePicture($validated, $adminObject);
            } else {
                if (Gate::forUser($adminObject)->allows('delete-multiple-picture', $validated['pictureId'])) {
                    $result = AdminPictureFacade::deleteMultiplePicture($validated, $adminObject);
                }
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
            $validator = Validator::make(
                $request->all(),
                [
                    'picture_id' => ['bail',new Required(),new Numeric()],
                    'picture_name' => ['bail',new Required(),new CheckString()]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['picture_id'])) {
                throw new RuleException('RuleRequiredError', 'picture_id');
            }

            if (is_develop($adminObject) || isSuperAdmin($adminObject)) {
                $result = AdminPictureFacade::updatePictureName($validated, $adminObject);
            } else {
                if (Gate::forUser($adminObject)->allows('update-picture-name', $validated['picture_id'])) {
                    $result = AdminPictureFacade::updatePictureName($validated, $adminObject);
                }
            }
        }

        return $result;
    }
}
