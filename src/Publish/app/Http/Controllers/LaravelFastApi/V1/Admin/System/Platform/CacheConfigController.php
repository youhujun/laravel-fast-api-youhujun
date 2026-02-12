<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-21 15:20:32
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-10 16:47:58
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\System\Platform\CacheConfigController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\Platform;

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

use App\Facades\LaravelFastApi\V1\Admin\System\Platform\AdminCacheConfigFacade;

class CacheConfigController extends Controller
{

    /**
     * 清除redis 缓存
     *
     * @param Request $request
     * @return void
     */
    public function cleanConfigCache(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $result = AdminCacheConfigFacade::cleanConfigCache();
        }

        return $result;
    }

    /**
     * 清除地区缓存
     *
     * @param Request $request
     * @return void
     */
    public function cleanRegionCache(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $result = AdminCacheConfigFacade::cleanRegionCache();
        }

        return $result;
    }

    /**
     * 清理角色缓存
     *
     * @param Request $request
     * @return void
     */
    public function cleanRoleCache(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $result = AdminCacheConfigFacade::cleanRoleCache();
        }

        return $result;
    }

    /**
     * 清理产品分类缓存
     *
     * @param Request $request
     * @return void
     */
    public function cleanGoodsClassCache(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $result = AdminCacheConfigFacade::cleanGoodsClassCache();
        }

        return $result;
    }

    /**
     * 清理文章分类缓存
     *
     * @return void
     */
    public function cleanCategoryCache(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $result = AdminCacheConfigFacade::cleanCategoryCache();
        }

        return $result;
    }

    /**
     * 清理标签分类缓存
     *
     * @param Request $request
     * @return void
     */
    public function cleanLabelCache(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $result = AdminCacheConfigFacade::cleanLabelCache();
        }

        return $result;
    }

    /**
     * 清理系统配置缓存
     *
     * @param Request $request
     * @return void
     */
    public function cleanSystemConfigCache(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $result = AdminCacheConfigFacade::cleanSystemConfigCache();
        }

        return $result;
    }

    /**
     * 清理权限路由缓存
     *
     * @param Request $request
     * @return void
     */
    public function cleanPermissionCache(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $result = AdminCacheConfigFacade::cleanPermissionCache();
        }

        return $result;
    }

    /**
     * 清理用户信息缓存
     *
     * @return void
     */
    public function cleanLoginUserInfoCache()
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $result = AdminCacheConfigFacade::cleanLoginUserInfoCache($admin);
        }

        return $result;
    }

}
