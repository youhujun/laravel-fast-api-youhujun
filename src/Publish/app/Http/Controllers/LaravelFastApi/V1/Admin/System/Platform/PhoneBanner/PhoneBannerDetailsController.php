<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-28 11:51:00
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-05-12 23:09:06
 * @FilePath: \app\Http\Controllers\Admin\System\Platform\PhoneBanner\PhoneBannerDetailsController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\Platform\PhoneBanner;

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

use  App\Facade\LaravelFastApi\V1\Admin\System\Platform\PhoneBanner\AdminPhoneBannerDetailsFacade;

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
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'album_picture_id'=>['bail',new Required,new Numeric]
                ],
                [ ]
            );

           $validated = $validator->validated();

           if (!isset($validated['id'])) {
               throw new RuleException('RuleRequiredError', 'id');
           }

           if (!isset($validated['album_picture_id'])) {
               throw new RuleException('RuleRequiredError', 'album_picture_id');
           }

           //p($validated);die;

           $result = AdminPhoneBannerDetailsFacade::updatePhoneBannerPicture(f($validated),$admin);
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
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = checkId($request->input('id'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'redirect_url'=>['bail',new Required,new CheckString]
                ],
                [ ]
            );

           $validated = $validator->validated();

           if (!isset($validated['id'])) {
               throw new RuleException('RuleRequiredError', 'id');
           }

           if (!isset($validated['redirect_url'])) {
               throw new RuleException('RuleRequiredError', 'redirect_url');
           }

           //p($validated);die;

           $result = AdminPhoneBannerDetailsFacade::updatePhoneBannerUrl(f($validated),$admin);
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
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = checkId($request->input('id'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'sort'=>['bail',new Required,new Numeric]
                ],
                [ ]
            );

           $validated = $validator->validated();

           if (!isset($validated['id'])) {
               throw new RuleException('RuleRequiredError', 'id');
           }

           if (!isset($validated['sort'])) {
               throw new RuleException('RuleRequiredError', 'sort');
           }

           //p($validated);die;

           $result = AdminPhoneBannerDetailsFacade::updatePhoneBannerSort(f($validated),$admin);
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
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = checkId($request->input('id'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'note'=>['bail',new Required,new CheckString]
                ],
                [ ]
            );

           $validated = $validator->validated();

           if (!isset($validated['id'])) {
               throw new RuleException('RuleRequiredError', 'id');
           }

           if (!isset($validated['note'])) {
               throw new RuleException('RuleRequiredError', 'note');
           }

           //p($validated);die;

           $result = AdminPhoneBannerDetailsFacade::updatePhoneBannerBakInfo(f($validated),$admin);
        }

        return $result;
    }
}
