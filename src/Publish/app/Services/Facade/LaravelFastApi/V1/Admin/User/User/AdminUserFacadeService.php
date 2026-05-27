<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-12 14:58:02
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 17:15:09
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
//必用
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Utils\Snowflake\SnowflakeFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
//event
use App\Events\Common\V1\User\User\CommonUserRegisterEvent;
use App\Events\Common\V1\User\User\EsAddUserEvent;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\User\User\GetUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\AddUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\DisableUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\MultipleDisableUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\DeleteUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\MultipleDeleteUserDTO;
use App\DTOs\Contracts\V1\User\User\AddUserHandlerContractDTO;
use App\DTOs\Common\V1\User\User\BusinessRegisterUserDTO;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\System\Level\UserLevel;
use App\Models\LaravelFastApi\V1\Admin\Admin;
//Db
use App\Http\Resources\LaravelFastApi\V1\Db\Admin\User\UserResource;
use App\Http\Resources\LaravelFastApi\V1\Db\Admin\User\UserCollection;
//Es
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\EsUserResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\EsUserCollection;
use YouHuJun\Tool\App\Facades\V1\Excel\ExcelFacade;
use App\Contracts\LaravelFastApi\V1\Common\User\AddUserHandlerContract;
use App\Facades\Common\V1\User\User\CommonUserFacade;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserController
 * @see \App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserFacade
 */
class AdminUserFacadeService
{
    public function test()
    {
        echo "AdminUserFacadeService test";
    }

    protected static $sortMapArray = [
        '1' => ['created_time','asc'],
        '2' => ['created_time','desc'],
    ];

    protected static $searchItemMapArray = [
        'phone',
        'account_name',
        'nick_name',
        'real_name',
        'id_number'
    ];


    /**
     * 导出表格数据
     *
     * @param [type] $userList
     * @return void
     */
    protected function exportData($userList)
    {
        $cloumn = [['账号','手机号','昵称','姓名','身份证号','性别','生日','说明','注册时间']];

        $data = [];

        foreach ($userList as $key => $value) {
            $list = [];

            $list[] = isset($value->account_name) ?? $value->account_name;
            $list[] = isset($value->phone) ?? $value->phone;
            $list[] = isset($value->userInfo->nick_name) ?? $value->userInfo->nick_name ;
            $list[] = isset($value->userInfo->real_name) ?? $value->userInfo->real_name ;
            $list[] = isset($value->userInfo->id_number) ?? $value->userInfo->id_number ;

            if (isset($value->userInfo->sex)) {
                $list[] = $value->userInfo->sex == 1 ? '男' : '女';
            } else {
                $list[] = '未知';
            }

            $list[] = isset($value->userInfo->solar_birthday_at) ?? $value->userInfo->solar_birthday_at;
            $list[] = isset($value->userInfo->introduction) ?? $value->userInfo->introduction;
            $list[] = isset($value->created_at) ?? $value->created_at;

            $data[] =  $list;
        }

        $title = "用户表";

        ExcelFacade::exportExcelData($cloumn, $data, $title, 1);

        return $title;
    }

    /**
     * es查询用户
     *
     * @param  Admin      $adminObject
     * @param  GetUserDTO $getUserDTO
     */
    public function getUser(Admin $adminObject, GetUserDTO $requestDTO)
    {
        $validated = $requestDTO->toArray();

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.user.users');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

        // 2. 适配业务筛选条件
        // 账号状态筛选
        if (isset($requestDTO->account_status)) {
            $esQuery->where('account_status', $requestDTO->account_status);
        }
        // 实名认证状态筛选
        if (isset($requestDTO->real_auth_status) && $requestDTO->real_auth_status > 0) {
            $esQuery->where('real_auth_status', $requestDTO->real_auth_status);
        }
        // 模糊搜索（手机号/姓名等）
        if (isset($requestDTO->findSelectIndex) && isset($requestDTO->find) && !empty($requestDTO->find)) {
            $findIndex = $requestDTO->findSelectIndex;
            $findValue = $requestDTO->find;
            $searchField = self::$searchItemMapArray[$findIndex] ?? '';
            if ($searchField) {
                $esQuery->whereLike($searchField, $findValue);
            }
        }
        // 时间范围
        if (isset($requestDTO->timeRange) && \count($requestDTO->timeRange)) {
            $startTime = strtotime($requestDTO->timeRange[0]);
            $endTime = strtotime($requestDTO->timeRange[1]);
            $esQuery->whereBetween('created_time', [$startTime, $endTime]);
        }

        //排序
        if (isset($requestDTO->sortType)) {
            $sortType = $requestDTO->sortType;

            $esQuery->orderBy(self::$sortMapArray[$sortType][0], self::$sortMapArray[$sortType][1]);
        }

        // 导出逻辑（完全按你的真实业务）

        $download = null;
        if (isset($validated['isExport']) && $validated['isExport'] == 1) {
            if (isset($validated['exportType'])) {
                // 本页导出
                if ($validated['exportType'] == 10) {
                    // 直接用已配置的 esQuery get()
                    $userExportList = $esQuery->page($currentPage, $perPage)->get();
                    $this->exportData($userExportList); // 直接下载，中断，不回头
                }

                // 全部导出
                if ($validated['exportType'] == 20) {
                    // 不带分页，get() 自动用 10000 兜底
                    $userExportList = $esQuery->get();
                    $this->exportData($userExportList); // 直接下载，中断
                }
            }
        }

        // 执行分页查询（返回Laravel标准分页对象）
        $userPaginateCollection = $esQuery->page($currentPage, $perPage)->paginate();

        // 统计实名认证待审核数量（复用ES统计方法）
        $userApplayRealAuthNumber = EsQueryFacade::index($indexName)
        ->where('real_auth_status', 20)
        ->count();

        //p($userList);
        $result = new EsUserCollection($userPaginateCollection, ['code' => 0,'msg' => '获取用户列表成功!','applyNumber'=>$userApplayRealAuthNumber], $download);

        return $result;
    }

    /**
     * 添加用户
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function addUser(Admin $adminObject, AddUserDTO $reuqestDTO)
    {
        $result = code(config('admin_code.AddUserError'));

        DB::beginTransaction();

        $user_uid = get_snow_flake_id();

        User::bindShardBusinessId($user_uid);

        $userLevelObject = UserLevel::where('level_code','V0')->get()->first();

        $userObject = User::create([
            'user_uid' => $user_uid,
            'source_user_uid' => isset($reuqestDTO->source_user_uid) ? $reuqestDTO->source_user_uid : 0,
            'parent_user_uid' => 0,
            'revision' => 0,
            'phone' => $reuqestDTO->phone,
            'password' => Hash::make('abc321'),
            'account_status' => 1,
            'real_auth_status' => 10,
            'level_id' => $userLevelObject->id,
            'source' => 0,
            'remember_token' => null,
            'auth_token' => Str::uuid()->toString(),
            'account_name' => \bin2hex(\random_bytes(4)),
            'phone_area_code' => '+86',
            'email' => null,
        ]);

        if (!isset($userObject->biz_id)) {
            throw new CommonException('AddUserError');
        }

        //处理传递参数
        $businessDTO = new BusinessRegisterUserDTO();

        $businessDTO->source_user_uid = isset($requestDTO->source_user_uid)?$requestDTO->source_user_uid:0;
        $businessDTO->$userObject = $userObject;
        $businessDTO->phone = isset($requestDTO->phone)?$requestDTO->phone:'';
        $businessDTO->password = isset($requestDTO->password)?$requestDTO->password:'';
        $businessDTO->nick_name = isset($requestDTO->nick_name)?$requestDTO->nick_name:'';
        $businessDTO->sex = isset($requestDTO->sex)?$requestDTO->sex:0;
        $businessDTO->source = isset($requestDTO->source)?$requestDTO->source:0;

        /**
         * @see \App\Services\Facade\Common\V1\User\User\CommonUserFacadeService
         */
        CommonUserFacade::registerUser($businessDTO, $userObject);

        //es添加用户
        EsAddUserEvent::dispatch($userObject,true);

        CommonEvent::dispatch($adminObject, $reuqestDTO, 'AddUser');

        DB::commit();

        $result = code(['code' => 0,'msg' => '添加用户成功!']);

        return $result;
    }


    /**
     * 禁用用户
     *
     * @param [type] $id
     * @param [type] $adminObject
     * @return void
     */
    public function disableUser(Admin $adminObject, DisableUserDTO  $disableUserDTO)
    {
        $result = code(config('admin_code.DisableUserError'));

        $validated = $disableUserDTO->toArray();

        $user_uid = $validated['user_uid'];
        $account_status = $validated['account_status'];

        $checkResult = $this->checkIsSystemUserByUserUid($user_uid);

        if ($checkResult) {
            throw new CommonException('DisableSystemUserError');
        }

        $userObject = User::queryByShard($user_uid)->where('account_status', !$account_status)->first();

        if (!isset($userObject->biz_id)) {
            throw new CommonException('UserNotExistError');
        }

        $updateDataArray = ['account_status' => $account_status];

        $userUpdateResult = $userObject->updateWithShard($updateDataArray);

        if (!$userUpdateResult) {
            throw new CommonException('DisableUserError');
        }

        $indexName = config('common_es.indices.user.users');

        $updateDataArray = [
            'account_status' => 0,
        ];

        $esResult = EsFacade::updateDoc($indexName, $userObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['info' => 'EsDisableUserJobError','$esResult' => $esResult], 'AdminUserFacadeService', 'handleError');

            throw new CommonException('EsDisableUserError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'DisableUser');

        $result = code(['code' => 0,'msg' => '禁用用户成功!']);

        return $result;
    }



    /**
     * 批量禁用用户
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function multipleDisableUser(Admin $adminObject, MultipleDisableUserDTO $multipleDeleteUserDTO)
    {
        $result = code(config('admin_code.MultipleDisableUserError'));

        $validated = $multipleDeleteUserDTO->toArray();

        if (isset($validated['select_uid_array']) && count($validated['select_uid_array'])) {
            //是否包含系统用户
            $user_uid_array = $validated['select_uid_array'];
            $account_status = $validated['account_status'];

            $checkResult = $this->checkIsSystemUserByUserUidArray($user_uid_array);

            if ($checkResult) {
                throw new CommonException('MultipleDisableSystemUserError');
            }

            // 批量禁用必须遍历！因为分库分表！
            foreach ($user_uid_array as $user_uid) {
                // 必须只查状态=1的用户才能禁用
                $userObject = User::queryByShard($user_uid)
                    ->where('account_status', !$account_status)
                    ->first();

                if (!$userObject) {
                    // 跳过不存在/已禁用的
                    continue;
                }
                // 用你写好的 分片+乐观锁 更新方法
                $userObject->updateWithShard([
                    'account_status' => $account_status,
                ]);

                $indexName = config('common_es.indices.user.users');

                $updateDataArray = [
                    'account_status' => 0,
                ];

                $esResult = EsFacade::updateDoc($indexName, $userObject->biz_id, $updateDataArray);


                if (!isset($esResult['code']) || $esResult['code'] != 0) {
                    plog(['info' => 'EsDisableUserJobError','$esResult' => $esResult], 'AdminUserFacadeService', 'handleError');

                    throw new CommonException('EsDisableUserError');
                }
            }

            CommonEvent::dispatch($adminObject, $validated, 'MultipleDisableUser');

            $result = code(['code' => 0,'msg' => '批量禁用用户成功!']);
        }

        return $result;
    }

    /**
     * 删除用户
     *
     * @param [type] $id
     * @param [type] $adminObject
     * @return void
     */
    public function deleteUser(Admin $adminObject, DeleteUserDTO $deleteUserDTO)
    {
        $result = code(config('admin_code.DeleteUserError'));

        $validated = $deleteUserDTO->toArray();

        $user_uid = $validated['user_uid'];

        $checkResult = $this->checkIsSystemUserByUserUid($user_uid);

        if ($checkResult) {
            throw new CommonException('DeleteSystemUserError');
        }

        $userObject = User::queryByShard($user_uid)->where('deleted_at', null)->first();

        if (!isset($userObject->biz_id)) {
            throw new CommonException('UserNotExistError');
        }

        $updateDataArray = ['deleted_at' => date('Y-m-d H:i:s')];

        $userUpdateResult = $userObject->updateWithShard($updateDataArray);

        if (!$userUpdateResult) {
            throw new CommonException('DeleteUserError');
        }

        $indexName = config('common_es.indices.user.users');

        $updateDataArray = ['deleted_at' => date('Y-m-d H:i:s')];

        $esResult = EsFacade::updateDoc($indexName, $userObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['info' => 'EsDeleteUserJobError','$esResult' => $esResult], 'AdminUserFacadeService', 'handleError');

            throw new CommonException('EsDeleteUserError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'DeleteUser');

        $result = code(['code' => 0,'msg' => '删除用户成功!']);

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function multipleDeleteUser(Admin $adminObject, MultipleDeleteUserDTO $multipleDeleteUserDTO)
    {
        $result = code(config('admin_code.MultipleDeleteUserError'));

        $validated = $multipleDeleteUserDTO->toArray();

        if (isset($validated['select_uid_array']) && count($validated['select_uid_array'])) {
            //是否包含系统用户
            $user_uid_array = $validated['select_uid_array'];

            $checkResult = $this->checkIsSystemUserByUserUidArray($user_uid_array);

            if ($checkResult) {
                throw new CommonException('MultipleDeleteSystemUserError');
            }

            // 批量禁用必须遍历！因为分库分表！
            foreach ($user_uid_array as $user_uid) {
                // 必须只查状态=1的用户才能禁用
                $userObject = User::queryByShard($user_uid)
                    ->where('deleted_at', null)
                    ->first();

                if (!$userObject) {
                    // 跳过不存在/已禁用的
                    continue;
                }
                // 用你写好的 分片+乐观锁 更新方法
                $userObject->updateWithShard([
                    'deleted_at' => date('Y-m-d H:i:s')
                ]);

                $indexName = config('common_es.indices.user.users');

                $updateDataArray = ['deleted_at' => date('Y-m-d H:i:s')];

                $esResult = EsFacade::updateDoc($indexName, $userObject->biz_id, $updateDataArray);

                if (!isset($esResult['code']) || $esResult['code'] != 0) {
                    plog(['info' => 'EsDeleteUserJobError','$esResult' => $esResult], 'AdminUserFacadeService', 'handleError');

                    throw new CommonException('EsDeleteUserError');
                }
            }

            CommonEvent::dispatch($adminObject, $validated, 'MultipleDeleteUser');

            $result = code(['code' => 0,'msg' => '批量删除用户成功!']);
        }

        return $result;
    }

    /**
     * 通过用户id检测是否是系统用户
     *
     * @param  [type] $user_uid
     */
    protected function checkIsSystemUserByUserUid(string $user_uid = '0'): bool
    {
        $result = false;

        $indexNname = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($indexNname)->whereNull('deleted_at')->where('user_uid', $user_uid)->get()->first();

        if (!isset($esUserObject->user_uid) ) {
            throw new CommonException('EsUserFindError');
        }

        $account_name = $esUserObject->account_name;

        $systemUserAccountNameArray  = get_system_user_account_name();

        if (in_array($account_name, $systemUserAccountNameArray)) {
            $result  = true;
        }

        return $result;
    }

    /**
     * 通过用户id数组检测是否含有系统和用户
     *
     * @param  array $user_uid_array
     */
    protected function checkIsSystemUserByUserUidArray(array $user_uid_array = []): bool
    {
        $checkResult = 0;

        $indexName = config('common_es.indices.user.users');

        $esUserColelction = EsQueryFacade::index($indexName)->whereNull('deleted_at')->whereIn('user_uid', $user_uid_array)->get();

        $count = $esUserColelction->count();

        if (!$count || $count != count($user_uid_array)) {
            throw new CommonException('EsUserSearchError');
        }

        $systemUserAccountNameArray = get_system_user_account_name();

        foreach ($esUserColelction as $esUserObject) {

            $account_name = $esUserObject->account_name;
            
            if (in_array($account_name, $systemUserAccountNameArray)) {
                $checkResult++;
            }
        }

        return $checkResult;
    }
}
