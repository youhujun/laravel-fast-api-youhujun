<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-28 08:06:57
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-08 17:50:54
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\System\Bank\BankController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\Bank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\DTOs\LaravelFastApi\V1\Admin\System\Bank\FindBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Bank\AddBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Bank\GetBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Bank\UpdateBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Bank\DeleteBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Bank\MultipleDeleteBankDTO;
use App\Facades\LaravelFastApi\V1\Admin\System\Bank\AdminBankFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\System\Bank\AdminBankFacadeService
 */
class BankController extends Controller
{
    //获取默认用户选项
    public function defaultBank(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
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
        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new FindBankDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminBankFacade::findBank($requestDTO);
        }

        return $result;
    }

    //获取银行列表
    public function getBank(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetBankDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminBankFacade::getBank($requestDTO);
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddBankDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminBankFacade::addBank($requestDTO, $adminObject);
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = check_id($request->input('id'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateBankDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminBankFacade::updateBank($requestDTO, $adminObject);
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteBankDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminBankFacade::deleteBank($requestDTO, $adminObject);
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDeleteBankDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminBankFacade::multipleDeleteBank($requestDTO, $adminObject);
        }

        return $result;
    }
}
