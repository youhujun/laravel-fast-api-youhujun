<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-04 00:23:42
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-22 19:43:52
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\User\Admin\AdministratorFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\User\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\GetDefaultAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\FindAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\AddAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\GetAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\UpdateAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\DisableAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\MultipleDisableAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\DeleteAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\MultipleDeleteAdminDTO;
use App\Events\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent;
use App\Events\LaravelFastApi\V1\Admin\User\Admin\UpdateAdministratorEvent;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Admin\EsAdminResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Admin\EsAdminCollection;
use YouHuJun\Tool\App\Facades\V1\Excel\ExcelFacade;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\Admin\AdministratorController
 * @see \App\Facades\Admin\User\Admin\AdministratorFacade
 */
class AdministratorFacadeService
{
    public function test()
    {
        echo "AdministratorFacadeService test";
    }

    public static $sortMapArray = [
        1 => ['created_time', 'asc'],
        2 => ['created_time', 'desc'],
    ];

    protected static $searchItemMapArray = [
        'phone',
        'account_name',
        'nick_name',
        'real_name',
        'id_number'
    ];

    //定义 转换路径时的storage软连接
    protected static $storage = 'storage';

    /**
      * 导出表格数据
      *
      * @param [type] $userList
      * @return void
      */
    protected function exportData($userList)
    {
        $cloumn = [['账号', '手机号', '昵称', '姓名', '身份证号', '性别', '生日', '说明', '注册时间']];

        $data = [];

        foreach ($userList as $key => $value) {
            $list = [];

            $list[] = $value->user->account_name;
            $list[] = $value->user->phone;
            $list[] = $value->user->userInfo->nick_name;
            $list[] = $value->user->userInfo->real_name;
            $list[] = $value->user->userInfo->id_number;

            if ($value->user->userInfo->sex) {
                $list[] = $value->user->userInfo->sex == 1 ? '男' : '女';
            } else {
                $list[] = '未知';
            }

            $list[] =  $value->user->userInfo->solar_birthday_at;
            $list[] =  $value->user->userInfo->introduction;
            $list[] =  $value->user->created_at;

            $data[] =  $list;
        }

        $title = "管理员表";

        ExcelFacade::exportExcelData($cloumn, $data, $title, 1);

        return $title;
    }

    /**
     * 查询用户
     *
     * @param [type] $validated
     * @return void
     */
    public function getAdmin(GetAdminDTO $requestDTO)
    {
        $result = code(config('admin_code.GetAdminError'));

        $validated = $requestDTO->toArray();

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.user.admins');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

        // 2. 适配业务筛选条件
        // 账号状态筛选
        if (isset($requestDTO->account_status)) {
            $esQuery->where('account_status', $requestDTO->account_status);
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


        $adminPaginator = $esQuery->page($currentPage, $perPage)->paginate();

        $download = null;
        //p($userList);die;
        EsAdminResource::SetShowControlType(10);

        if (\optional($adminPaginator)) {
            $result = new EsAdminCollection($adminPaginator, ['code' => 0,'msg' => '获取管理员成功!'], $download);
        }

        return  $result;
    }

    /**
     * 添加用户
     *
     * @param [type] $validated
     * @param [type] $loginAdminObject
     * @return void
     */
    public function addAdmin(AddAdminDTO $requestDTO, Admin $loginAdminObject)
    {
        $result = code(config('admin_code.AddAdminError'));

        //p($requestDTO);die;

        $validated = $requestDTO->toArray();

        $role_cascader_id_array = $requestDTO->role_cascader_id_array;

        $role_id_array = get_cascader_array($role_cascader_id_array);

        $userIndexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($userIndexName)->where('user_uid', $requestDTO->user_uid)->get()->first();

        //降级熔断
        if (!isset($esUserObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $insertAdminDataArray = [
            'account_status' => 1,
            'user_uid' => $esUserObject->user_uid,
            'phone_area_code' => '+86',
            'password' => $esUserObject->password ?? Hash::make('abc321'),
            'phone' =>$requestDTO->phone ?? $esUserObject->phone,
            'account_name' => $esUserObject->account_name ?? '',
            'email' => $esUserObject->email ?? ''
        ];

        $adminObject = ShardHelperFacade::createWithShard(Admin::class, $esUserObject->user_uid, $insertAdminDataArray);

        if (!isset($adminObject->biz_id)) {
            throw new CommonException('AddAdminError');
        }

        AddAdministratorEvent::dispatch($loginAdminObject, $adminObject, $requestDTO);

        $adminIndexName = config('common_es.indices.user.admins');

        $userIndexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($userIndexName)->where('user_uid', $adminObject->user_uid)->get()->first();

        $adminAlbumObject = Album::queryByShard($adminObject->user_uid)->where('album_type', 10)->where('admin_uid', $adminObject->admin_uid)->where('is_default', 1)->first();

        $configKey = get_shard_config_key();

        $adminObject = $adminObject->fresh();

        $insertDataArray = [
            '_docId' => $adminObject->admin_uid,
            'shard_key' => $adminObject->shard_key,
            'shard_db' => ShardFacade::getDbName($adminObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($adminObject->user_uid, 'admins', $configKey),
            'admin_uid' => $adminObject->admin_uid,
            'user_uid' => $adminObject->user_uid,
            'remember_token' => $adminObject->remember_token,
            'account_status' => $adminObject->account_status,
            'phone_area_code' => $adminObject->phone_area_code,
            'phone' => $adminObject->phone,
            'password' => $adminObject->password,
            'account_name' => $adminObject->account_name,
            'created_time' => $adminObject->created_time,
            'updated_time' => $adminObject->updated_time,
            'created_at' => $adminObject->created_at,
            'updated_at' => $adminObject->updated_at,
            'deleted_at' => $adminObject->deleted_at,
            //userInfo
            'id_number' => $esUserObject?->id_number,
            'nick_name' => $esUserObject?->nick_name,
            'real_name' => $esUserObject?->real_name,
            'solar_birthday_at' => $esUserObject?->solar_birthday_at,
            'chinese_birthday_at' => $esUserObject?->chinese_birthday_at,
            'sex' => $esUserObject?->sex,
            'introduction' => $esUserObject?->introduction,
            //album
            'ablum_uid' => $adminAlbumObject?->biz_id,
            //avatar
            'avatar' => $esUserObject?->avatar,
            //cascader
            'role_cascader_json'=>json_encode($role_cascader_id_array)
        ];

        $esResult = EsFacade::createDoc($adminIndexName, $insertDataArray, $adminObject->admin_uid);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加管理员失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$adminObject' => $adminObject,'$loginAdminObject' => $loginAdminObject], 'AdministratorFacadeService', 'addAdminError');
            throw new CommonException('EsAddAdminError');
        }

        CommonEvent::dispatch($loginAdminObject, $validated, 'AddAdmin');

        $result = code(['code' => 0,'msg' => '添加管理员成功!']);

        return $result;
    }


    /**
     * 更新管理员
     *
     * @param [type] $validated
     * @param [type] $loginAdminObject
     * @return void
     */
    public function updateAdmin(UpdateAdminDTO $requestDTO, Admin $loginAdminObject)
    {
        $result = code(config('admin_code.UpdateAdminError'));

        $role_cascader_id_array = $requestDTO->role_cascader_id_array;

        $role_id_array = get_cascader_array($role_cascader_id_array);

        $validated = $requestDTO->toArray();

        $indexName = config('common_es.indices.user.admins');

        $esUpdateAdminObject = EsQueryFacade::index($indexName)->where('admin_uid', $requestDTO->admin_uid)->get()->first();

        if (!$esUpdateAdminObject) {
            throw new CommonException('ThisDataNotExistsError');
        }


        $updateDataArray = [];

        $updateDataArray = [
            'phone' => $requestDTO->phone,
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_time'=>time(),
        ];

        $adminObject = Admin::queryByShard($esUpdateAdminObject->user_uid)->first();

        //更新管理员
        $updateAdminResult = $adminObject->updateWithShard($updateDataArray);

        if (!$updateAdminResult) {
            throw new CommonException('UpdateAdminError');
        }

        $adminObject = $adminObject->fresh();

        UpdateAdministratorEvent::dispatch($loginAdminObject, $adminObject, $requestDTO);

        $indexName = config('common_es.indices.user.admins');

        $updateDataArray = [
            'phone' => $requestDTO->phone,
            'role_cascader_json'=>json_encode($role_cascader_id_array),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_time'=>time(),
        ];

        $esResult = EsFacade::updateDoc($indexName, $adminObject->admin_uid, $updateDataArray);

         if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新管理员失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$adminObject' => $adminObject,'$loginAdminObject' => $loginAdminObject], 'AdministratorFacadeService', 'updateAdminError');
            throw new CommonException('EsUpdateAdminError');
        }

        CommonEvent::dispatch($loginAdminObject, $requestDTO, 'UpdateAdmin');

        $result = code(['code' => 0,'msg' => '更新管理员成功!']);

        return $result;
    }

    /**
     * 禁用用户
     *
     * @param [type] $id
     * @param [type] $loginAdminObject
     * @return void
     */
    public function disableAdmin(DisableAdminDTO $requestDTO, Admin $loginAdminObject)
    {
        $result = code(config('admin_code.DisableAdminError'));

        $validated = $requestDTO->toArray();

        $account_status = $requestDTO->account_status;

        $indexName = config('common_es.indices.user.admins');

        //如果禁用
        if (!$account_status) {
            //系统管理员不可以禁用
            $systemAdminUidArray = EsQueryFacade::index($indexName)->orWhere('account_name', 'develop')->orWhere('account_name', 'super')->orWhere('account_name', 'admin')->get()->pluck('admin_uid')->toArray();

            //检测
            $checkResult = in_array($requestDTO->admin_uid, $systemAdminUidArray);

            if ($checkResult) {
                throw new CommonException('DisableSystemAdminError');
            }
        }

        $adminObject = EsQueryFacade::index($indexName)->where('admin_uid', $requestDTO->admin_uid)->get()->first();

        if (!$adminObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $disableAdminObject = Admin::queryByShard($adminObject->user_uid)->first();

        $updateDataArray = [
            'account_status' => $requestDTO->account_status,
        ];

        $disableAdminResult = $disableAdminObject->updateWithShard($updateDataArray);

        if (!$disableAdminResult) {
            throw new CommonException('DisableAdminError');
        }

        $disableAdminObject = $disableAdminObject->fresh();

        $indexName = config('common_es.indices.user.admins');

        $updateDataArray = [
            'account_status' => $disableAdminObject->account_status,
            'updated_time' => $disableAdminObject->updated_time,
            'updated_at' => $disableAdminObject->updated_at,
        ];

        $esResult = EsFacade::updateDoc($indexName, $disableAdminObject->admin_uid, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es禁用管理员失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$disableAdminObject' => $disableAdminObject,'$loginAdminObject' => $loginAdminObject], 'AdministratorFacadeService', 'disableAdminError');
            throw new CommonException('EsDisableAdminError');
        }

        CommonEvent::dispatch($loginAdminObject, $requestDTO, 'DisableAdmin');

        $result = code(['code' => 0,'msg' => '禁用管理员成功!']);

        return $result;
    }



    /**
     * 批量禁用管理员
     *
     * @param [MultipleDisableAdminDTO] $requestDTO
     * @param [Admin] $loginAdminObject
     * @return void
     */
    public function multipleDisableAdmin(MultipleDisableAdminDTO $requestDTO, Admin $loginAdminObject)
    {
        $result = code(config('admin_code.MultipleDisableAdminError'));

        $validated = $requestDTO->toArray();

        $select_uid_array =$requestDTO->select_uid_array;
        $account_status = $requestDTO->account_status;

        $indexName = config('common_es.indices.user.admins');

        //如果禁用
        if (!$account_status) {
            //系统管理员不可以禁用
            $systemAdminUidArray = EsQueryFacade::index($indexName)->whereIn('account_name', ['develop', 'super', 'admin'])->get()->pluck('admin_uid')->toArray();

            //取交集
            $intersection = array_intersect($systemAdminUidArray, $select_uid_array);

            if (!empty($intersection)) {
                throw new CommonException('MultipleDisableSystemAdminError');
            }
        }

        if (count($select_uid_array)) {
            $admin_user_uid_aray = EsQueryFacade::index($indexName)->whereIn('admin_uid', $select_uid_array)->get()->pluck('user_uid')->toArray();
            foreach ($admin_user_uid_aray as $user_uid) {
                $adminObject = Admin::queryByShard($user_uid)->where('user_uid', $user_uid)->where('account_status', !$account_status)->first();
                if (!$adminObject) {
                    // 跳过不存在/已禁用的
                    continue;
                }
                $adminObject->updateWithShard([
                    'account_status' => $account_status,
                ]);

                $indexName = config('common_es.indices.user.admins');

                $adminObject = $adminObject->fresh();

                $updateDataArray = [
                    'account_status' => $adminObject->account_status,
                    'updated_time' => $adminObject->updated_time,
                    'updated_at' => $adminObject->updated_at,
                ];

                $esResult = EsFacade::updateDoc($indexName, $adminObject->admin_uid, $updateDataArray);

                if (!isset($esResult['code']) || $esResult['code'] !== 0) {
                    plog(['error' => 'es禁用管理员失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$adminObject' => $adminObject,'$loginAdminObject' => $loginAdminObject], 'AdministratorFacadeService', 'multipleDisableAdminError');
                    continue;
                }
            }
        }

        CommonEvent::dispatch($loginAdminObject, $validated, 'MultipleDisableAdmin');

        $result = code(['code' => 0,'msg' => '批量禁用管理员成功!']);

        return $result;
    }

    /**
     * 删除管理员
     *
     * @param [DeleteAdminDTO] $requestDTO
     * @param [Admin] $loginAdminObject
     * @return void
     */
    public function deleteAdmin(DeleteAdminDTO $requestDTO, Admin $loginAdminObject)
    {
        $result = code(config('admin_code.DeleteAdminError'));

        $validated = $requestDTO->toArray();

        $indexName = config('common_es.indices.user.admins');

        //系统管理员不可以删除
        $systemAdminUidArray = EsQueryFacade::index($indexName)->whereIn('account_name', ['develop', 'super', 'admin'])->get()->pluck('admin_uid')->toArray();

        //检测
        $checkResult = in_array($requestDTO->admin_uid, $systemAdminUidArray);

        if ($checkResult) {
            throw new CommonException('DeleteSystemAdminError');
        }

        $adminObject = EsQueryFacade::index($indexName)->where('admin_uid', $requestDTO->admin_uid)->get()->first();

        if (!$adminObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $deleteAdminObject = Admin::queryByShard($adminObject->user_uid)->first();

        $updateDataArray = [
            'deleted_at' => \date('Y-m-d H:i:s', time()),
        ];

        $deleteAdminResult = $deleteAdminObject->updateWithShard($updateDataArray);

        if (!$deleteAdminResult) {
            throw new CommonException('DeleteAdminError');
        }

        $updateDataArray = [
            'deleted_at' => \date('Y-m-d H:i:s', time()),
            'updated_time' => $adminObject->updated_time,
            'updated_at' => $adminObject->updated_at,
        ];

        $esResult = EsFacade::updateDoc($indexName, $adminObject->admin_uid, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es删除管理员失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$adminObject' => $adminObject,'$loginAdminObject' => $loginAdminObject], 'AdministratorFacadeService', 'deleteAdminError');
            throw new CommonException('EsDleteAdminError');
        }

        CommonEvent::dispatch($loginAdminObject, $validated, 'DeleteAdmin');

        $result = code(['code' => 0,'msg' => '删除管理员成功!']);

        return $result;
    }

    /**
     * 批量删除管理员
     *
     * @param [MultipleDeleteAdminDTO] $requestDTO
     * @param [Admin] $loginAdminObject
     * @return void
     */
    public function multipleDeleteAdmin(MultipleDeleteAdminDTO $requestDTO, Admin $loginAdminObject)
    {
        $result = code(config('admin_code.MultipleDeleteAdminError'));

        $validated = $requestDTO->toArray();

        $select_uid_array = $requestDTO->select_uid_array;

        $indexName = config('common_es.indices.user.admins');

        //系统管理员不可以删除
        $systemAdminUidArray = EsQueryFacade::index($indexName)->orWhere('account_name', 'develop')->orWhere('account_name', 'super')->orWhere('account_name', 'admin')->get()->pluck('admin_uid')->toArray();

        //取交集
        $intersection = array_intersect($systemAdminUidArray, $select_uid_array);

        if (!empty($intersection)) {
            throw new CommonException('MultipleDeleteSystemAdminError');
        }

        if (count($select_uid_array)) {
            $admin_user_uid_aray = EsQueryFacade::index($indexName)->whereIn('admin_uid', $select_uid_array)->get()->pluck('user_uid')->toArray();
            foreach ($admin_user_uid_aray as $user_uid) {
                $adminObject = Admin::queryByShard($user_uid)->where('user_uid', $user_uid)->first();
                if (!$adminObject) {
                    // 跳过不存在/已禁用的
                    continue;
                }
                $adminObject->updateWithShard([
                    'deleted_at' => \date('Y-m-d H:i:s', time()),
                ]);

                 $updateDataArray = [
                    'deleted_at' => \date('Y-m-d H:i:s', time()),
                    'updated_time' => $adminObject->updated_time,
                    'updated_at' => $adminObject->updated_at,
                ];

                $esResult = EsFacade::updateDoc($indexName, $adminObject->admin_uid, $updateDataArray);

                if (!isset($esResult['code']) || $esResult['code'] !== 0) {
                    plog(['error' => 'es删除管理员失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$adminObject' => $adminObject,'$loginAdminObject' => $loginAdminObject], 'AdministratorFacadeService', 'handleError');
                    continue;
                }
            }
        }

        CommonEvent::dispatch($loginAdminObject, $validated, 'MultipleDeleteUser');

        $result = code(['code' => 0,'msg' => '批量删除管理员成功!']);

        return $result;
    }


    /**
     * 获取所有的后台管理员用户
     *
     * @return void
     */
    public function getDefaultAdmin(GetDefaultAdminDTO $requestDTO)
    {
        $result = code(config('admin_code.GetDefaultAdminerError'));

        $account_status = isset($requestDTO->account_status)?$requestDTO->account_status:1;

        $indexName = config('common_es.indices.user.admins');

        $adminCollection = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('account_status', $account_status)->limit(10)->orderBy('created_time', 'asc')->get();

        if (!optional($adminCollection)) {
            throw new CommonException('getDefaultAdminerError');
        }

        $data['data'] = EsAdminResource::collection($adminCollection);

        $result = code(['code' => 0,'msg' => '获取默认管理员成功!'], $data);

        return  $result;
    }


    /**
     * 查找管理员
     *
     * @param [type] $validated
     * @param [type] $loginAdminObject
     * @return void
     */
    public function findAdmin(FindAdminDTO $requestDTO, Admin $loginAdminObject)
    {
        $result = code(config('admin_code.FindAdminerError'));

        $indexName = config('common_es.indices.user.admins');

        $max_size = config('common_es.max_result_window');

        $adminCollection = EsQueryFacade::index($indexName)
        ->orWhereLike('account_name', $requestDTO->find)
        ->orWhereLike('phone', $requestDTO->find)
        ->limit($max_size)
        ->orderBy('created_time', 'asc')
        ->get()
        ->filter(fn ($item) => empty($item->deleted_at))
        ->values();

        if (!optional($adminCollection)) {
            throw new CommonException('FindAdminerError');
        }

        $data['data'] = EsAdminResource::collection($adminCollection);

        $result = code(['code' => 0,'msg' => '查找管理员成功!'], $data);

        return  $result;
    }
}
