<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-14 15:24:29
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-28 01:37:43
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\Service\Platform\PhoneBannerController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Platform;

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

use App\Facade\LaravelFastApi\V1\Admin\Service\Platform\AdminPhoneBannerFacade;

class PhoneBannerController extends Controller
{
    /**
     * 获取轮播图
     *
     * @param Request $request
     * @return void
     */
    public function getPhoneBanner(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {

             $validator = Validator::make(
                $request->all(),
                [
                    'currentPage'=>['bail','nullable',new Numeric],
                    'pageSize'=>['bail','nullable',new Numeric],
                    'sortType'=>['bail',new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['sortType'])) {
                throw new RuleException('RuleRequiredError', 'sortType');
            }

            //p($validated);die;

            $result = AdminPhoneBannerFacade::getPhoneBanner(f($validated),$admin);
        }

        return $result;
    }

    /**
     * 添加首页轮播图
     *
     * @param Request $request
     * @return void
     */
    public function addPhoneBanner(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'album_picture_id'=>['bail',new Required,new Numeric],
                    'redirect_url'=>['bail',  'nullable', new CheckString],
                    'note'=>['bail', 'nullable', new CheckString],
                    'sort'=>['bail', new Required, new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['album_picture_id'])) {
                throw new RuleException('RuleRequiredError', 'album_picture_id');
            }

            if (!isset($validated['sort'])) {
                throw new RuleException('RuleRequiredError', 'sort');
            }

            //p($validated);die;

            $result = AdminPhoneBannerFacade::addPhoneBanner(f($validated),$admin);
        }

        return $result;
    }

    /**
     * 修改轮播图
     * @param {Request} $request
     * @return {*}
     */
    public function updatePhoneBanner(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = checkId($request->input('id'));

        if (Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id' => ['bail', new Required, new Numeric],
                    'album_picture_id'=>['bail',new Required,new Numeric],
                    'redirect_url'=>['bail',  'nullable', new CheckString],
                    'note'=>['bail', 'nullable', new CheckString],
                    'sort'=>['bail', new Required, new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['album_picture_id'])) {
                throw new RuleException('RuleRequiredError', 'album_picture_id');
            }

            if (!isset($validated['sort'])) {
                throw new RuleException('RuleRequiredError', 'sort');
            }

            $result = AdminPhoneBannerFacade::updatePhoneBanner(f($validated), $admin);
        }

        return $result;

    }


    /**
     * 删除轮播图
     * @param {Request} $request
     * @return {*}
     */
    public function deletePhoneBanner(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id' => ['bail', new Required, new Numeric],
                    'is_delete' => ['bail', new Required, new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['is_delete'])) {
                throw new RuleException('RuleRequiredError', 'is_delete');
            }

            $result = AdminPhoneBannerFacade::deletePhoneBanner(f($validated), $admin);
        }

        return $result;
    }

	/**
	 * 批量删除
	 */
	public function multipleDeletePhoneBanner(Request $request)
	{
		$admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'selectId' => ['bail', new Required, new CheckArray],
                    'is_delete' => ['bail', new Required, new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['selectId'])) {
                throw new RuleException('RuleRequiredError', 'selectId');
            }

            if (!isset($validated['is_delete'])) {
                throw new RuleException('RuleRequiredError', 'is_delete');
            }

            $result = AdminPhoneBannerFacade::multipleDeletePhoneBanner(f($validated), $admin);
        }

        return $result;
	}
}
