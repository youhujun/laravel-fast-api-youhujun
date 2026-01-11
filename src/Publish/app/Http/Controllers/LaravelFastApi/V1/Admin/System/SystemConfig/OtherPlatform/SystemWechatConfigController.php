<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-31 14:57:12
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-02 15:08:26
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform;

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

use App\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigFacade;

class SystemWechatConfigController extends Controller
{
	/**
     * 查询
     */
    public function getSystemWechatConfig(Request $request)
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
                    'currentPage'=>['bail','nullable',new Numeric],
                    'pageSize'=>['bail','nullable',new Numeric],
                    'sortType'=>['bail',new Required,new Numeric],
                    'type'=>['bail','nullable',new Numeric],
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

			if(count($validated))
			{
				$result = SystemWechatConfigFacade::getSystemWechatConfig(f($validated),$admin);
			}
        }

        return $result;
    }

    /**
     * 添加
     *
     * @param Request $request
     * @return void
     */
    public function addSystemWechatConfig(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'name'=>['bail','nullable',new CheckString],
                    'type'=>['bail',new Required,new Numeric],
                    'sort'=>['bail','nullable',new Numeric],
                    'appid'=>['bail',new Required,new CheckString,new CheckUnique('system_wechat_config','appid')],
                    'appsecret'=>['bail',new Required,new CheckString],
                    /* 'auth_redirect_url'=>['bail','nullable',new CheckString], */
                    'note'=>['bail','nullable',new CheckString],
                ],
                [ ]
            );

            $validated = $validator->validated();

            if (!isset($validated['type'])) {
                throw new RuleException('RuleRequiredError', 'type');
            }

            if (!isset($validated['appid'])) {
                throw new RuleException('RuleRequiredError', 'appid');
            }

            if (!isset($validated['appsecret'])) {
                throw new RuleException('RuleRequiredError', 'appsecret');
            }

			// p($validated);die;
			// SystemWechatConfigFacade::test();die;

            if(count($validated))
			{
				$result = SystemWechatConfigFacade::addSystemWechatConfig(f($validated),$admin);
			}
            
        }

        return $result;
    }

    /**
     * 修改
     *
     * @param Request $request
     * @return void
     */
    public function updateSystemWechatConfig(Request $request)
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
                    'name'=>['bail','nullable',new CheckString],
                    'type'=>['bail',new Required,new Numeric],
                    'sort'=>['bail','nullable',new Numeric],
                    'appid'=>['bail',new Required,new CheckString,new CheckUnique('system_wechat_config','appid',$id)],
                    'appsecret'=>['bail',new Required,new CheckString],
                   /*  'auth_redirect_url'=>['bail','nullable',new CheckString], */
                    'note'=>['bail','nullable',new CheckString],
                ],
                [ ]
            );

           $validated = $validator->validated();

           if (!isset($validated['id'])) {
               throw new RuleException('RuleRequiredError', 'id');
           }

           if (!isset($validated['type'])) {
               throw new RuleException('RuleRequiredError', 'type');
           }

           if (!isset($validated['appid'])) {
               throw new RuleException('RuleRequiredError', 'appid');
           }

           if (!isset($validated['appsecret'])) {
               throw new RuleException('RuleRequiredError', 'appsecret');
           }

		   //p($validated);die;

		   if(count($validated))
			{
				$result = SystemWechatConfigFacade::updateSystemWechatConfig(f($validated),$admin);
			}

          
        }

        return $result;
    }

    /**
     * 删除
     *
     * @param Request $request
     * @return void
     */
    public function deleteSystemWechatConfig(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'is_delete'=>['bail',new Required,new Numeric],
                ],
                [ ]
            );

            $validated = $validator->validated();

            if (!isset($validated['selectId'])) {
                throw new RuleException('RuleRequiredError', 'selectId');
            }

            if (!isset($validated['is_delete'])) {
                throw new RuleException('RuleRequiredError', 'is_delete');
            }

			if(count($validated))
			{
				 $result = SystemWechatConfigFacade::deleteSystemWechatConfig(f($validated),$admin);
			}
        }

        return $result;
    }

    /**
     * 批量删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteSystemWechatConfig(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'selectId'=>['bail',new Required,new CheckArray],
                    'is_delete'=>['bail',new Required,new Numeric],
                ],
                [ ]
            );

            $validated = $validator->validated();

            if (!isset($validated['selectId'])) {
                throw new RuleException('RuleRequiredError', 'selectId');
            }

            if (!isset($validated['is_delete'])) {
                throw new RuleException('RuleRequiredError', 'is_delete');
            }

			if(count($validated))
			{
				 $result = SystemWechatConfigFacade::multipleDeleteSystemWechatConfig(f($validated),$admin);
			}
        }

        return $result;
    }

}
