<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-28 08:06:57
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-26 21:48:27
 * @FilePath: \app\Http\Controllers\Admin\Service\Group\LabelController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
use App\Rules\Pub\LetterNumberUnderLine;

use App\Rules\LaravelFastApi\V1\Admin\Common\TreeDeep;

use App\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminLabelFacade;

class LabelController extends Controller
{
   /**
     * 获取树形地址
     *
     * @return void
     */
    public function getTreeLabel()
    {
        $user = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($user)->allows('admin-role'))
        {
             $result = AdminLabelFacade::getTreeLabel();
        }

        return  $result;
    }

     /**
     * 添加顶级/下级标签
     *
     * @param Request $request
     * @return void
     */
    public function addLabel(Request $request)
    {
        $user = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($user)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'parent_id'=>['bail',new Required,new Numeric],
                    'deep' => ['bail',new Required,new TreeDeep],
                    'sort' => ['bail',new Required,new Numeric],
                    'label_name'=>['bail',new Required,new CheckString],
                    'label_code'=>['bail','nullable',new CheckString],
                    'note'=>['bail','nullable',new CheckString],
                    'label_picture_id'=>['bail','nullable',new Numeric],
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

            if (!isset($validated['sort'])) {
                throw new RuleException('RuleRequiredError', 'sort');
            }

            if (!isset($validated['label_name'])) {
                throw new RuleException('RuleRequiredError', 'label_name');
            }

            $result =AdminLabelFacade::addLabel(f($validated),$user);
        }

        return $result;
    }

    /**
     * 更新标签
     *
     * @param Request $request
     * @return void
     */
    public function updateLabel(Request $request)
    {
        $user = Auth::guard('admin_token')->user();

        $result = code(config('admin_code.AdminAuthError'));

        $id = checkId($request->input('id'));

        if(Gate::forUser($user)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'parent_id'=>['bail',new Required,new Numeric],
                    'deep' => ['bail',new Required,new TreeDeep],
                    'sort' => ['bail',new Required,new Numeric],
                    'label_name'=>['bail',new Required,new CheckString,],
                    'label_code'=>['bail','nullable',new CheckString],
                    'note'=>['bail','nullable',new CheckString],
                    'label_picture_id'=>['bail','nullable',new Numeric],
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

            if (!isset($validated['sort'])) {
                throw new RuleException('RuleRequiredError', 'sort');
            }

            if (!isset($validated['label_name'])) {
                throw new RuleException('RuleRequiredError', 'label_name');
            }

            $result = AdminLabelFacade::updateLabel(f($validated),$user);
        }
        return $result;
    }

    /**
     * 树形控件移动
     *
     * @param Request $request
     * @return void
     */
    public function moveLabel(Request $request)
    {
        $user = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($user)->allows('super-role'))
        {

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

            $result = AdminLabelFacade::moveLabel(f($validated), $user);
        }

        return $result;
    }

    /**
     * 删除标签
     *
     * @param Request $request
     * @return void
     */
    public function deleteLabel(Request $request)
    {
        $user = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($user)->allows('admin-role'))
        {
           $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'is_delete'=>['bail',new Required,new Numeric],
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

            //p($validated);die;
            $result = AdminLabelFacade::deleteLabel(f($validated),$user);

        }

        return $result;
    }
}
