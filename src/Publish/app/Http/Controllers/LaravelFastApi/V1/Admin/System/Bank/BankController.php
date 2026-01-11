<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-28 08:06:57
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-06-01 01:39:52
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\System\Bank\BankController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\Bank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Rules\Pub\CheckArray;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\ChineseCodeNumberLine;
use App\Exceptions\Common\RuleException;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\Required;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckUnique;

use App\Facade\LaravelFastApi\V1\Admin\System\Bank\AdminBankFacade;

class BankController extends Controller
{
     //获取默认用户选项
    public function defaultBank(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
           $result =  AdminBankFacade::defaultBank();
        }

        return $result;
    }

    /**
     * 查找银行列表
     *
     * @param Request $request
     * @return void
     */
    public function findBank(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'find'=>['bail',new Required],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['find'])) {
                throw new RuleException('RuleRequiredError', 'find');
            }

            $result = AdminBankFacade::findBank(f($validated));
        }

        return $result;
    }

    //获取银行列表
    public function getBank(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
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

            if (!isset($validated['sortType'])) {
                throw new RuleException('RuleRequiredError', 'sortType');
            }

            $result = AdminBankFacade::getBank(f($validated));
        }

        return $result;
    }

    /**
     * 添加银行
     *
     * @param AddBankRequest $request
     * @return void
     */
    public function addBank(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'bank_name'=>['bail',new Required,new ChineseCodeNumberLine, new CheckUnique('bank','bank_name','except','id')],
                    'bank_code'=>['nullable', new CheckUnique('bank','bank_code','except','id')],
                    'is_default'=>['bail',new Required,new Numeric],
                    'sort'=>['bail',new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['bank_name'])) {
                throw new RuleException('RuleRequiredError', 'bank_name');
            }

            if (!isset($validated['is_default'])) {
                throw new RuleException('RuleRequiredError', 'is_default');
            }

            if (!isset($validated['sort'])) {
                throw new RuleException('RuleRequiredError', 'sort');
            }

            $result = AdminBankFacade::addBank(f($validated),$admin);


        }

        return $result;
    }

    /**
     * 修改银行
     *
     * @param UpdateBankRequest $request
     * @return void
     */
    public function updateBank(Request $request)
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
                    'bank_name'=>['bail',new Required,new ChineseCodeNumberLine, new CheckUnique('bank','bank_name',$id)],
                    'bank_code'=>['nullable', new CheckUnique('bank','bank_code',$id)],
                    'is_default'=>['bail',new Required,new Numeric],
                    'sort'=>['bail',new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['bank_name'])) {
                throw new RuleException('RuleRequiredError', 'bank_name');
            }

            if (!isset($validated['is_default'])) {
                throw new RuleException('RuleRequiredError', 'is_default');
            }

            if (!isset($validated['sort'])) {
                throw new RuleException('RuleRequiredError', 'sort');
            }

            $result = AdminBankFacade::updateBank(f($validated),$admin);
        }

        return $result;
    }


    /**
     * 删除银行
     *
     * @param Request $request
     * @return void
     */
    public function deleteBank(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            $result = AdminBankFacade::deleteBank(f($validated),$admin);
        }

        return $result;
    }

    /**
     * 批量删除银行
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteBank(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
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

            $result = AdminBankFacade::multipleDeleteBank(f($validated),$admin);
        }

        return $result;
    }
}
