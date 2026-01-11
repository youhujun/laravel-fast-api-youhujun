<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-26 10:41:09
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-25 10:38:36
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group\GoodsClassController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group;

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

use App\Rules\LaravelFastApi\V1\Admin\Common\TreeDeep;

use App\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminGoodsClassFacade;

class GoodsClassController extends Controller
{
	 /**
     * 获取产品分类
     *
     * @return void
     */
    public function getTreeGoodsClass()
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        //p('here');die;

        if(Gate::forUser($admin)->allows('admin-role'))
        {
             $result = AdminGoodsClassFacade::getTreeGoodsClass();
        }

        return $result;
    }

     /**
     * 添加产品分类
     *
     * @param Request $request
     * @return void
     */
    public function addGoodsClass(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'parent_id'=>['bail',new Required,new Numeric],
                    'deep' => ['bail',new Required,new TreeDeep],
                    'sort' => ['bail',new Numeric],
                    'rate' => ['bail','nullable',new Numeric],
                    'goods_class_name'=>['bail',new Required,new CheckString,new CheckBetween(2,30)],
                    'goods_class_code'=>['bail','nullable',new CheckString,new CheckBetween(2,30),new CheckUnique('goods_class','goods_class_code')],
                    'is_certificate'=>['bail','nullable',new Numeric],
                    'certificate_number'=>['bail','nullable',new Numeric],
                    'note'=>['bail','nullable',new CheckString,new CheckBetween(0,30),new ChineseCodeNumberLine],
                    'goods_class_picture_id'=>['bail','nullable',new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['parent_id'])) {
                throw new RuleException('RuleRequiredError', 'parent_id');
            }

            if (!isset($validated['deep'])) {
                throw new RuleException('RuleRequiredError', 'deep');
            }

            if (!isset($validated['goods_class_name'])) {
                throw new RuleException('RuleRequiredError', 'goods_class_name');
            }

            //p($validated);die;

            $result = AdminGoodsClassFacade::addGoodsClass(f($validated),$admin);
        }

        return $result;
    }

    /**
     * 更新产品分类
     *
     * @param Request $request
     * @return void
     */
    public function updateGoodsClass(Request $request)
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
                    'parent_id'=>['bail',new Required,new Numeric],
                    'deep' => ['bail',new Required,new TreeDeep],
                    'sort' => ['bail',new Numeric],
                    'rate' => ['bail','nullable',new Numeric],
                    'goods_class_name'=>['bail',new Required,new CheckString,new CheckBetween(2,30)],
                    'goods_class_code'=>['bail','nullable',new CheckString,new CheckBetween(2,30),new CheckUnique('goods_class','goods_class_code')],
                    'is_certificate'=>['bail','nullable',new Numeric],
                    'certificate_number'=>['bail','nullable',new Numeric],
                    'note'=>['bail','nullable',new CheckString,new CheckBetween(0,30),new ChineseCodeNumberLine],
                    'goods_class_picture_id'=>['bail','nullable',new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['parent_id'])) {
                throw new RuleException('RuleRequiredError', 'parent_id');
            }

            if (!isset($validated['deep'])) {
                throw new RuleException('RuleRequiredError', 'deep');
            }

            if (!isset($validated['goods_class_name'])) {
                throw new RuleException('RuleRequiredError', 'goods_class_name');
            }

            $result = AdminGoodsClassFacade::updateGoodsClass(f($validated),$admin);
        }
        return $result;
    }

    /**
     * 树形控件移动
     *
     * @param Request $request
     * @return void
     */
    public function moveGoodsClass(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('super-role')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'parent_id'=>['bail',new Required,new Numeric],
                    'dropType' => ['bail',new Required,new TreeDeep],
                    'sort' => ['bail',new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['parent_id'])) {
                throw new RuleException('RuleRequiredError', 'parent_id');
            }

            if (!isset($validated['dropType'])) {
                throw new RuleException('RuleRequiredError', 'dropType');
            }

            if (!isset($validated['sort'])) {
                throw new RuleException('RuleRequiredError', 'sort');
            }

            $result = AdminGoodsClassFacade::moveGoodsClass(f($validated), $admin);
        }

        return $result;
    }

    /**
     * 删除产品分类
     *
     * @param Request $request
     * @return void
     */
    public function deleteGoodsClass(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('super-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'is_delete'=>['bail',new Required,new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            $result = AdminGoodsClassFacade::deleteGoodsClass($validated,$admin);
        }

        return $result;
    }

	public function getSingleGoodsClass(Request $request)
	{
		 $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('super-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'goods_class_id'=>['bail',new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            $result = AdminGoodsClassFacade::getSingleGoodsClass($validated,$admin);
        }

        return $result;
	}
}
