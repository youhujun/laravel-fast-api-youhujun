<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 18:51:17
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-03-05 14:40:01
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfigController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig;

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
use App\Rules\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfigVoiceSaveType;

use App\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminVoiceConfigFacade;

class VoiceConfigController extends Controller
{

    /**
     * 查询
     */
    public function getVoiceConfig(Request $request)
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
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['sortType'])) {
                throw new RuleException('RuleRequiredError', 'sortType');
            }

            // p($validated);die;

            $result = AdminVoiceConfigFacade::getVoiceConfig(f($validated),$admin);
        }

        return $result;
    }

    /**
     * 添加
     *
     * @param Request $request
     * @return void
     */
    public function addVoiceConfig(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    "voice_title" =>['bail',new Required,new CheckString],
                    "channle_name" =>['bail',new Required,new CheckString],
                    "channle_event" =>['bail',new Required,new CheckString,new CheckUnique('system_voice_config','channle_event')],
                    "voice_save_type" =>['bail',new Required,new Numeric,new VoiceConfigVoiceSaveType($request->all())],
                    "voice_url" =>['bail','nullable',new CheckString],
                    "voice_path" =>['bail','nullable',new CheckString],
                    "note" =>['bail',new Required,new CheckString]
                ],
                [ ]
            );

            $validated = $validator->validated();

            if (!isset($validated['voice_title'])) {
                throw new RuleException('RuleRequiredError', 'voice_title');
            }

            if (!isset($validated['channle_name'])) {
                throw new RuleException('RuleRequiredError', 'channle_name');
            }

            if (!isset($validated['channle_event'])) {
                throw new RuleException('RuleRequiredError', 'channle_event');
            }

            if (!isset($validated['voice_save_type'])) {
                throw new RuleException('RuleRequiredError', 'voice_save_type');
            }

            if (!isset($validated['note'])) {
                throw new RuleException('RuleRequiredError', 'note');
            }

            //p($validated);die;

            $result = AdminVoiceConfigFacade::addVoiceConfig(f($validated),$admin);
        }

        return $result;
    }

    /**
     * 修改
     *
     * @param Request $request
     * @return void
     */
    public function updateVoiceConfig(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = checkId($request->input('id'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
           $validator = Validator::make(
                $request->all(),
                [
                    "id"=>['bail',new Required,new Numeric],
                    "voice_title" =>['bail',new Required,new CheckString],
                    "channle_name" =>['bail',new Required,new CheckString],
                    "channle_event" =>['bail',new Required,new CheckString,new CheckUnique('system_voice_config','channle_event',$id)],
					"voice_save_type" =>['bail',new Required,new Numeric],
                    "voice_url" =>['bail','nullable',new CheckString],
                    "voice_path" =>['bail','nullable',new CheckString],
                    "note" =>['bail',new Required,new CheckString]
                ],
                [ ]
            );

           $validated = $validator->validated();

           if (!isset($validated['id'])) {
               throw new RuleException('RuleRequiredError', 'id');
           }

           if (!isset($validated['voice_title'])) {
               throw new RuleException('RuleRequiredError', 'voice_title');
           }

           if (!isset($validated['channle_name'])) {
               throw new RuleException('RuleRequiredError', 'channle_name');
           }

           if (!isset($validated['channle_event'])) {
               throw new RuleException('RuleRequiredError', 'channle_event');
           }

           if (!isset($validated['voice_save_type'])) {
               throw new RuleException('RuleRequiredError', 'voice_save_type');
           }

           if (!isset($validated['note'])) {
               throw new RuleException('RuleRequiredError', 'note');
           }

           //p($validated);die;

           $result = AdminVoiceConfigFacade::updateVoiceConfig(f($validated),$admin);
        }

        return $result;
    }


    /**
     * 删除
     *
     * @param Request $request
     * @return void
     */
    public function deleteVoiceConfig(Request $request)
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
                [ ]
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            //p($validated);die;

            $result = AdminVoiceConfigFacade::deleteVoiceConfig(f($validated),$admin);
        }

        return $result;
    }

    /**
     * 批量删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteVoiceConfig(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'selectId'=>['bail',new Required,new CheckArray]
                ],
                [ ]
            );

            $validated = $validator->validated();

            if (!isset($validated['selectId'])) {
                throw new RuleException('RuleRequiredError', 'selectId');
            }

            //p($validated);die;

            $result = AdminVoiceConfigFacade::multipleDeleteVoiceConfig(f($validated),$admin);
        }

        return $result;
    }

    
}
