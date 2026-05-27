<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-28 11:51:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-10 01:20:33
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\PhoneBannerDetailsController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner;

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
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\Details\UpdatePhoneBannerBakInfoDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\Details\UpdatePhoneBannerPictureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\Details\UpdatePhoneBannerUrlDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\Details\UpdatePhoneBannerSortDTO;
use App\Facades\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\AdminPhoneBannerDetailsFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\AdminPhoneBannerDetailsFacadeService
 */
class PhoneBannerDetailsController extends Controller
{
    /**
     * 修改轮播图图片
     *
     * @param Request $request
     * @return void
     */
    public function updatePhoneBannerPicture(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdatePhoneBannerPictureDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminPhoneBannerDetailsFacade::updatePhoneBannerPicture($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 修改轮播图跳转
     *
     * @param Request $request
     * @return void
     */
    public function updatePhoneBannerUrl(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = check_id($request->input('id'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdatePhoneBannerUrlDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminPhoneBannerDetailsFacade::updatePhoneBannerUrl($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 修改轮播图跳转
     *
     * @param Request $request
     * @return void
     */
    public function updatePhoneBannerSort(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = check_id($request->input('id'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdatePhoneBannerSortDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminPhoneBannerDetailsFacade::updatePhoneBannerSort($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 修改轮播图跳转
     *
     * @param Request $request
     * @return void
     */
    public function updatePhoneBannerBakInfo(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = check_id($request->input('id'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdatePhoneBannerBakInfoDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminPhoneBannerDetailsFacade::updatePhoneBannerBakInfo($requestDTO, $adminObject);
        }

        return $result;
    }
}
