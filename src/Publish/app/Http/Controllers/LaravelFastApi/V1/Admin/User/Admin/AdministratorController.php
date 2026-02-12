<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-12 14:54:26
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-05 02:10:08
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\User\Admin\AdministratorController.php
 */


namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\Admin;

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

use App\Rules\Common\Phone;

use App\Facades\LaravelFastApi\V1\Admin\User\Admin\AdministratorFacade;

class AdministratorController extends Controller
{

    /**
     * 获取所有管理用户
     *
     * @param Request $request
     * @return void
     */
    public function getDefaultAdmin(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $result = AdministratorFacade::getDefaultAdmin();
        }

        return $result;
    }

    /**
     * 查找管理员
     *
     * @return void
     *
     */
    public function findAdmin(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
             $validator = Validator::make(
                $request->all(),
                [
                    'find'=>['bail','nullable',new CheckString]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['user_id'])) {
                throw new RuleException('RuleRequiredError', 'user_id');
            }

            if (!isset($validated['phone'])) {
                throw new RuleException('RuleRequiredError', 'phone');
            }

            //p($validated);die;

            $result = AdministratorFacade::findAdmin($validated,$admin);
        }

        return $result;
    }



    //获取管理员列表
    public function getAdmin(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('super-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'find'=>['bail','nullable',new CheckString],
                    'findSelectIndex'=>['bail','nullable',new Numeric],
                    'timeRange'=>['bail','nullable',new CheckArray],
                    'switch'=>['bail','nullable',new Numeric],
                    'sortType'=>['bail',new Required,new Numeric],
                    'currentPage'=>['bail','nullable',new Numeric],
                    'pageSize'=>['bail','nullable',new Numeric],
                    'isExport'=>['bail','nullable',new Numeric],
                    'exportType'=>['bail','nullable',new Numeric],
                    'status'=>['bail','nullable',new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['user_id'])) {
                throw new RuleException('RuleRequiredError', 'user_id');
            }

            if (!isset($validated['phone'])) {
                throw new RuleException('RuleRequiredError', 'phone');
            }

            //p($validated);die;

            $result = AdministratorFacade::getAdmin(f($validated));
        }

        return $result;
    }

    /**
     * 添加用户
     *
     * @param AddAdminRequest $request
     * @return void
     */
    public function addAdmin(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('super-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'user_id'=>['bail',new Required,new Numeric,new CheckUnique('admin','user_id')],
					'phone' => ['bail', new Required, new CheckString, new Phone, new CheckUnique('admin', 'phone')],
                    'role'=>['bail','nullable',new CheckArray],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['user_id'])) {
                throw new RuleException('RuleRequiredError', 'user_id');
            }

            if (!isset($validated['phone'])) {
                throw new RuleException('RuleRequiredError', 'phone');
            }

            //p($validated);die;

            $result = AdministratorFacade::addAdmin(f($validated),$admin);

        }

        return $result;
    }

    /**
     * 修改管理员
     *
     * @param UpdateAdminRequest $request
     * @return void
     */
    public function updateAdmin(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = $request->input('id');

        if(Gate::forUser($admin)->allows('super-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>[new Required,new Numeric],
                    'user_id'=>[new Required,new Numeric,new CheckUnique('admin','user_id',checkId($id))],
					'phone' => ['bail', new Required, new CheckString, new Phone, new CheckUnique('admin', 'phone',checkId($id))],
                    'role'=>['bail','nullable',new CheckArray]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['user_id'])) {
                throw new RuleException('RuleRequiredError', 'user_id');
            }

            if (!isset($validated['phone'])) {
                throw new RuleException('RuleRequiredError', 'phone');
            }

            //p($validated);die;

            $result = AdministratorFacade::updateAdmin(f($validated),$admin);

        }

        return $result;
    }

    /**
     * 禁用用户
     *
     * @param Request $request
     * @return void
     */
    public function disableAdmin(Request $request)
    {

        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('super-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>[new Required,new Numeric],
                    'switch'=>[new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['switch'])) {
                throw new RuleException('RuleRequiredError', 'switch');
            }

            // p($validated);die;

            $result = AdministratorFacade::disableAdmin($validated,$admin);
        }

        return $result;
    }

    /**
     * 批量禁用用户
     *
     * @param Request $request
     * @return void
     */
    public function multipleDisableAdmin(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('super-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'selectId'=>['bail',new Required,new CheckArray],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['selectId'])) {
                throw new RuleException('RuleRequiredError', 'selectId');
            }

            $result = AdministratorFacade::multipleDisableAdmin($validated,$admin);
        }

        return $result;
    }

    /**
     * 删除用户
     *
     * @param Request $request
     * @return void
     */
    public function deleteAdmin(Request $request)
    {

        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('super-role'))
        {
             $validator = Validator::make(
                $request->all(),
                [
                    'id'=>[new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['switch'])) {
                throw new RuleException('RuleRequiredError', 'switch');
            }

            // p($validated);die;

            $result = AdministratorFacade::deleteAdmin($validated,$admin);
        }

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteAdmin(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('super-role'))
        {

            $validator = Validator::make(
                $request->all(),
               [
                  'selectId'=>['bail',new Required,new CheckArray],
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['selectId'])) {
               throw new RuleException('RuleRequiredError', 'selectId');
           }

            $result = AdministratorFacade::multipleDeleteAdmin($validated,$admin);
        }

        return $result;
    }
}
