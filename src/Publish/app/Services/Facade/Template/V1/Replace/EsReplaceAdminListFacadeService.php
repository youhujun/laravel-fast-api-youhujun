<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-01 16:43:51
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-10 22:55:05
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Template\V1\Replace\EsReplaceAdminListFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\Template\V1\Replace;

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
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\DTOs\Template\V1\Replace\GetReplaceDTO;
use App\DTOs\Template\V1\Replace\AddReplaceDTO;
use App\DTOs\Template\V1\Replace\DisableReplaceDTO;
use App\DTOs\Template\V1\Replace\DeleteReplaceDTO;
use App\DTOs\Template\V1\Replace\MultipleDisableReplaceDTO;
use App\DTOs\Template\V1\Replace\MultipleDeleteReplaceDTO;
use App\Models\LaravelFastApi\V1\Replace\Replace;
use App\Models\LaravelFastApi\V1\Admin\Admin;
//Db
use App\Http\Resources\Template\V1\Db\Replace\ReplaceResource;
use App\Http\Resources\Template\V1\Db\Replace\ReplaceCollection;
//Es
use App\Http\Resources\Template\V1\Es\Replace\EsReplaceResource;
use App\Http\Resources\Template\V1\Es\Replace\EsReplaceCollection;
use YouHuJun\Tool\App\Facades\V1\Excel\ExcelFacade;
use App\Contracts\Template\V1\Replace\DoReplaceHandlerContract;
use App\Jobs\Template\V1\Es\Replace\EsAddReplaceJob;
use App\Jobs\Template\V1\Es\Replace\EsUpdateReplaceJob;
use App\Jobs\Template\V1\Es\Replace\EsDisableReplaceJob;
use App\Jobs\Template\V1\Es\Replace\EsDeleteReplaceJob;

/**
 * @see \App\Http\Controllers\Template\V1\Replace\ReplceController
 * @see \App\Facades\Template\V1\Replace\EsReplaceFacade
 */
class EsReplaceAdminListFacadeService
{
    public function test()
    {
        echo "EsReplaceFacadeService test";
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
    * 批量导入数据
    *
    * @param UploadFileLog $uploadFileLog
    * @return void
    */
    public function importData($path)
    {
        $result = 0;

        $exists = Storage::disk('public')->exists($path);

        if ($exists) {
            ExcelFacade::initReadExcel(storage_path(self::$storage_public_path.$path));

            ExcelFacade::setWorkSheet(0);

            $excelData = ExcelFacade::getDataByRow();

            array_shift($excelData);

            $insertDataArray = [];

            foreach ($excelData as $key => $value) {
                $insertDataArray[] =
                [
                    'replace_name' => $value[0],
                    'replacecode' => empty($value[1]) ? null : $value[1],
                    'is_default' => empty($value[2]) ? 0 : $value[2],
                    'sort' => empty($value[3]) ? 100 : $value[3]
                ];
            }

            DB::beginTransaction();

            $maxBefore = Replce::max('id');
            $result = Replce::insert($insertDataArray);
            $maxAfter = Replce::max('id');

            // 区间 ID 就是这批插入的数据
            $id_array = range($maxBefore + 1, $maxAfter);

            if(!$result){
                plog(['error'=>'替换导入数据失败','$result'=>$result,'$insertDataArray'=>$insertDataArray,'$path'=>$path],'EsReplaceAdminListFacadeService','importDataError');

                DB::rollBack();
            }

            $replaceCollection = Replce::whereIn('id',$id_array)->get();

            $indexName = config('common_es.indices.system.banks');

            $insertDataArray = [];

            foreach ($bankCollection as $replaceObject) {
                $insertDataArray[] = [
                    '_docId' => $replaceObject->id,
                    'id' => $replaceObject->id,
                    'replace_name' => $replaceObject->replace_name,
                    'replace_code' => $replaceObject->replace_code,
                    'is_default' => $replaceObject->is_default,
                    'sort' => $replaceObject->sort,
                    'created_time' => $replaceObject->created_time,
                    'updated_time' => $replaceObject->updated_time,
                    'created_at' => $replaceObject->created_at,
                    'updated_at' => $replaceObject->updated_at,
                    'deleted_at' => $replaceObject->deleted_at,
                ];
            }

            $esResult = EsFacade::batchActDoc($indexName, $insertDataArray);

            if (!isset($esResult['code']) || $esResult['code'] !== 0) {
                $result = 0;
                plog(['error' => 'es批量添加替换配置失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$path'=>$path], 'EsReplaceAdminListFacadeService', 'importData');

                DB::rollBack();
            }

             DB::commit();
        }

        return $result;
    }


    /**
     * 导出表格数据
     *
     * @param [type] $replaceList
     * @return void
     */
    protected function exportData($replaceList)
    {
        $cloumn = [['账号','手机号','昵称','姓名','身份证号','性别','生日','说明','注册时间']];

        $dataArray = [];

        foreach ($replaceList as $key => $value) {
            $listArray = [];

            $listArray[] = isset($value->account_name) ?? $value->account_name;
            $listArray[] = isset($value->phone) ?? $value->phone;
            $listArray[] = isset($value->replaceInfo->nick_name) ?? $value->replaceInfo->nick_name ;
            $listArray[] = isset($value->replaceInfo->real_name) ?? $value->replaceInfo->real_name ;
            $listArray[] = isset($value->replaceInfo->id_number) ?? $value->replaceInfo->id_number ;

            if (isset($value->replaceInfo->sex)) {
                $listArray[] = $value->replaceInfo->sex == 1 ? '男' : '女';
            } else {
                $listArray[] = '未知';
            }

            $listArray[] = isset($value->replaceInfo->solar_birthday_at) ?? $value->replaceInfo->solar_birthday_at;
            $listArray[] = isset($value->replaceInfo->introduction) ?? $value->replaceInfo->introduction;
            $listArray[] = isset($value->created_at) ?? $value->created_at;

            $dataArray[] =  $listArray;
        }

        $title = "替换表".date('YmdHis');

        $path = self::$storage_public_path."Replace";

        $savePath = storage_path($path);

        ExcelFacade::exportExcelData($cloumn, $dataArray, $title, $savePath);

        return $title;
    }

    /**
     * es查询用户
     *
     * @param  Admin      $adminObject
     * @param  GetReplaceDTO $getReplaceDTO
     */
    public function getReplace(Admin $adminObject, GetReplaceDTO $requestDTO)
    {
        $validated = $requestDTO->toArray();
        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.replaces');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

        //是否删除
        if (!isset($requestDTO->is_delete) || !$requestDTO->is_delete) {
            $esQuery->whereNull('deleted_at');
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

        $esQuery->orderBy('id','desc');

        //排序
        if (isset($requestDTO->sortType)) {
            $sortType = $requestDTO->sortType;

            $esQuery->orderBy(self::$sortMapArray[$sortType][0], self::$sortMapArray[$sortType][1]);
        }

        // 导出逻辑（完全按你的真实业务）

        $download = null;

        if (isset($requestDTO->isExport) && $requestDTO->isExport == 1) {
            if ($requestDTO->exportType) {
                // 本页导出
                if ($requestDTO->exportType == 1) {
                    // 直接用已配置的 esQuery get()
                    $exportColelction = $esQuery->page($currentPage, $perPage)->get();
                    $title = $this->exportData($exportColelction); // 直接下载，中断，不回头
                }

                // 全部导出
                if ($requestDTO->exportType == 2) {
                    // 不带分页，get() 自动用 10000 兜底
                    $exportColelction = $esQuery->get();
                    $title = $this->exportData($exportColelction); // 直接下载，中断
                }

                $exists = Storage::disk('public')->exists("excel/{$title}.xlsx");

                if ($exists) {
                    $download = asset("storage/excel/{$title}.xlsx");
                }

                if($download){
                    $result = ['code' => 0,'msg' => '获取替换列表成功!','download'=>$download];
                }else{
                    $result = ['code' => 10000,'msg' => '获取替换列表失败!'];

                }
                return $result;
            }
        }

        // 执行分页查询（返回Laravel标准分页对象）
        $replacePaginator = $esQuery->page($currentPage, $perPage)->paginate();

        // 统计实名认证待审核数量（复用ES统计方法）
        $replaceApplayRealAuthNumber = EsQueryFacade::index($indexName)
        ->where('real_auth_status', 20)
        ->count();

        //p($replaceList);
        $result = new EsReplaceCollection($replacePaginator, ['code' => 0,'msg' => '获取用户列表成功!'], $download, $replaceApplayRealAuthNumber);

        return $result;
    }

    /**
     * 添加用户
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function addReplace(Admin $adminObject, AddReplaceDTO $addReplaceDTO)
    {
        $result = code(config('admin_code.AddReplaceError'));

        $validated = $addReplaceDTO->toArray();

        DB::beginTransaction();

        $replace_uid =  get_snow_flake_id();

        Replace::bindShardBusinessId($replace_uid);

        $replaceObject = Replace::create([
            'replace_uid' => $replace_uid,
            'source_replace_uid' => isset($validated['source_replace_uid']) ? $validated['source_replace_uid'] : 0,
            'parent_replace_uid' => 0,
            'revision' => 0,
            'phone' => $validated['phone'],
            'password' => Hash::make('abc321'),
            'account_status' => 1,
            'real_auth_status' => 10,
            'level_id' => 1,
            'source' => 0,
            'remember_token' => null,
            'auth_token' => Str::uuid()->toString(),
            'account_name' => \bin2hex(\random_bytes(4)),
            'phone_area_code' => '+86',
            'email' => null,
        ]);

        if (!isset($replaceObject->biz_id)) {
            throw new CommonException('AddReplaceError');
        }

        $eventParamArray = ['replaceObject' => $replaceObject,'adminObject' => $adminObject,'validated' => $validated,'isTransation' => 1];

        //同步用户数
        EsAddReplaceJob::dispatch($replaceObject)->delay(now()->addSeconds(3));

        //契约业务
        $handleParamArray = ['replaceObject' => $replaceObject,'adminObject' => $adminObject,'validated' => $validated];

        app(DoReplaceHandlerContract::class)->handle($handleParamArray);

        $eventResult = CommonEvent::dispatch($adminObject, $validated, 'AddReplace');

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
    public function disableReplace(Admin $adminObject, DisableReplaceDTO  $disableReplaceDTO)
    {
        $result = code(config('admin_code.DisableReplaceError'));

        $validated = $disableReplaceDTO->toArray();

        $replace_uid = $validated['replace_uid'];
        $account_status = $validated['account_status'];

        $checkResult = $this->checkIsSystemReplaceByReplaceUid($replace_uid);

        if ($checkResult) {
            throw new CommonException('DisableSystemReplaceError');
        }

        $replaceObject = Replace::queryByShard($replace_uid)->where('account_status', 1)->first();

        if (!isset($replaceObject->biz_id)) {
            throw new CommonException('ReplaceNotExistError');
        }

        $updateDataArray = ['account_status' => $account_status];

        $replaceUpdateResult = $replaceObject->updateWithShard($updateDataArray);

        if (!$replaceUpdateResult) {
            throw new CommonException('DisableReplaceError');
        }

        EsDisableReplaceJob::dispatch($replaceObject)->delay(now()->addSeconds(3));

        CommonEvent::dispatch($adminObject, $validated, 'DisableReplace');

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
    public function multipleDisableReplace(Admin $adminObject, MultipleDeleteReplaceDTO $multipleDeleteReplaceDTO)
    {
        $result = code(config('admin_code.MultipleDisableReplaceError'));

        $validated = $multipleDeleteReplaceDTO->toArray();

        if (isset($validated['select_replace_uid_array']) && count($validated['select_replace_uid_array'])) {
            //是否包含系统用户
            $replace_uid_array = $validated['select_replace_uid_array'];
            $account_status = $validated['account_status'];

            $checkResult = $this->checkIsSystemReplaceByReplaceUidArray($replace_uid_array);

            if ($checkResult) {
                throw new CommonException('MultipleDisableSystemReplaceError');
            }

            // 批量禁用必须遍历！因为分库分表！
            foreach ($replace_uid_array as $replace_uid) {
                // 必须只查状态=1的用户才能禁用
                $replaceObject = Replace::queryByShard($replace_uid)
                    ->where('account_status', 1)
                    ->first();

                if (!$replaceObject) {
                    // 跳过不存在/已禁用的
                    continue;
                }
                // 用你写好的 分片+乐观锁 更新方法
                $replaceObject->updateWithShard([
                    'account_status' => $account_status,
                ]);

                // 异步同步 ES
                EsDisableReplaceJob::dispatch($replaceObject)->delay(now()->addSeconds(3));
            }

            CommonEvent::dispatch($adminObject, $validated, 'MultipleDisableReplace');

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
    public function deleteReplace(Admin $adminObject, DeleteReplaceDTO $deleteReplaceDTO)
    {
        $result = code(config('admin_code.DeleteReplaceError'));

        $validated = $deleteReplaceDTO->toArray();

        $replace_uid = $validated['replace_uid'];

        $checkResult = $this->checkIsSystemReplaceByReplaceUid($replace_uid);

        if ($checkResult) {
            throw new CommonException('DeleteSystemReplaceError');
        }

        $replaceObject = Replace::queryByShard($replace_uid)->where('deleted_at', null)->first();

        if (!isset($replaceObject->biz_id)) {
            throw new CommonException('ReplaceNotExistError');
        }

        $updateDataArray = ['deleted_at' => date('Y-m-d H:i:s')];

        $replaceUpdateResult = $replaceObject->updateWithShard($updateDataArray);

        if (!$replaceUpdateResult) {
            throw new CommonException('DeleteReplaceError');
        }

        EsDeleteReplaceJob::dispatch($replaceObject)->delay(now()->addSeconds(3));

        CommonEvent::dispatch($adminObject, $validated, 'DeleteReplace');

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
    public function multipleDeleteReplace(Admin $adminObject, MultipleDeleteReplaceDTO $multipleDeleteReplaceDTO)
    {
        $result = code(config('admin_code.MultipleDeleteReplaceError'));

        $validated = $multipleDeleteReplaceDTO->toArray();

        if (isset($validated['select_replace_uid_array']) && count($validated['select_replace_uid_array'])) {
            //是否包含系统用户
            $replace_uid_array = $validated['select_replace_uid_array'];

            $checkResult = $this->checkIsSystemReplaceByReplaceUidArray($replace_uid_array);

            if ($checkResult) {
                throw new CommonException('MultipleDeleteSystemReplaceError');
            }

            // 批量禁用必须遍历！因为分库分表！
            foreach ($replace_uid_array as $replace_uid) {
                // 必须只查状态=1的用户才能禁用
                $replaceObject = Replace::queryByShard($replace_uid)
                    ->where('deleted_at', null)
                    ->first();

                if (!$replaceObject) {
                    // 跳过不存在/已禁用的
                    continue;
                }
                // 用你写好的 分片+乐观锁 更新方法
                $replaceObject->updateWithShard([
                    'deleted_at' => date('Y-m-d H:i:s')
                ]);

                // 异步同步 ES
                EsDeleteReplaceJob::dispatch($replaceObject)->delay(now()->addSeconds(3));
            }

            CommonEvent::dispatch($adminObject, $validated, 'MultipleDeleteReplace');

            $result = code(['code' => 0,'msg' => '批量删除用户成功!']);
        }

        return $result;
    }

    /**
     * 通过用户id检测是否是系统用户
     *
     * @param  [type] $replace_uid
     */
    protected function checkIsSystemReplaceByReplaceUid(string $replace_uid = '0'): bool
    {
        $result = false;

        $indexNname = config('common_es.indices.replaces');

        $esResult = EsFacade::findDoc($indexNname, $replace_uid);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            throw new CommonException('EsReplaceFindError');
        }

        $replaceArray = $esResult['data'];

        $account_name = $replaceArray['account_name'];

        $systemReplaceAccountNameArray  = get_system_replace_account_name();

        if (in_array($account_name, $systemReplaceAccountNameArray)) {
            $result  = true;
        }

        return $result;
    }

    /**
     * 通过用户id数组检测是否含有系统和用户
     *
     * @param  array $replace_id_array
     */
    protected function checkIsSystemReplaceByReplaceUidArray(array $replace_uid_array = []): bool
    {
        $checkResult = 0;

        $indexName = config('common_es.indices.replaces');

        $queryArray = [
            'bool' => [
                'must' => [
                    // whereIn replace_uid
                    [
                        'terms' => [
                            'replace_uid' => $replace_uid_array
                        ]
                    ]
                ],
                // 自动过滤已软删除的数据（deleted_at IS NULL）
                'must_not' => [
                    [
                        'exists' => [
                            'field' => 'deleted_at'
                        ]
                    ]
                ]
            ]
        ];

        $esResult = EsFacade::searchDoc($indexName, $queryArray);

        $total = 0;

        $dataArray = [];
        $dataPreArray = [];

        if (isset($esResult['data']['hits']['total']['value'])) {
            $total = $esResult['data']['hits']['total']['value'];
        }

        if ($total) {
            $dataPreArray = $esResult['data']['hits']['hits'];
        }

        foreach ($dataPreArray as $dataPre) {
            $dataArray[] = $dataPre['_source'];
        }

        if (!$total || $total != count($replace_uid_array)) {
            throw new CommonException('EsReplaceSearchError');
        }

        $systemReplaceAccountNameArray = get_system_replace_account_name();

        foreach ($dataArray as $replaceArray) {
            $account_name = $replaceArray['account_name'] ?? '';
            if (in_array($account_name, $systemReplaceAccountNameArray)) {
                $checkResult++;
            }
        }

        return $checkResult;
    }
}
