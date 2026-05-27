<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-31 23:31:19
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-06 17:09:41
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\System\Bank\AdminBankFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\System\Bank;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\System\Bank\FindBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Bank\AddBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Bank\GetBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Bank\UpdateBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Bank\DeleteBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Bank\MultipleDeleteBankDTO;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Module\Bank;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Bank\EsBankResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Bank\EsBankCollection;
use YouHuJun\Tool\App\Facades\V1\Excel\ExcelFacade;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\Bank\BankController
 * @see \App\Facades\LaravelFastApi\V1\Admin\System\Bank\AdminBankFacade
 */
class AdminBankFacadeService
{
    public function test()
    {
        echo "AdminBankFacadeService test";
    }

    protected static $sortMapArray = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];

    protected static $searchItemMapArray = [
        'bank_name',
        'bank_code'
    ];

    protected static $storage_public_path = DIRECTORY_SEPARATOR.'app'. DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;


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
                    'bank_name' => $value[0],
                    'bank_code' => empty($value[1]) ? null : $value[1],
                    'is_default' => empty($value[2]) ? 0 : $value[2],
                    'sort' => empty($value[3]) ? 100 : $value[3]
                ];
            }

            DB::beginTransaction();

            $maxBefore = Bank::max('id');
            $result = Bank::insert($insertDataArray);
            $maxAfter = Bank::max('id');

            // 区间 ID 就是这批插入的数据
            $id_array = range($maxBefore + 1, $maxAfter);

            if(!$result){
                plog(['error'=>'银行导入数据失败','$result'=>$result,'$insertDataArray'=>$insertDataArray,'$path'=>$path],'AdminBankFacadeService','importDataError');

                DB::rollBack();
            }

            $bankCollection = Bank::whereIn('id',$id_array)->get();

            $indexName = config('common_es.indices.system.banks');

            $insertDataArray = [];

            foreach ($bankCollection as $bankObject) {
                $insertDataArray[] = [
                    '_docId' => $bankObject->id,
                    'id' => $bankObject->id,
                    'bank_name' => $bankObject->bank_name,
                    'bank_code' => $bankObject->bank_code,
                    'is_default' => $bankObject->is_default,
                    'sort' => $bankObject->sort,
                    'created_time' => $bankObject->created_time,
                    'updated_time' => $bankObject->updated_time,
                    'created_at' => $bankObject->created_at,
                    'updated_at' => $bankObject->updated_at,
                    'deleted_at' => $bankObject->deleted_at,
                ];
            }

            $esResult = EsFacade::batchActDoc($indexName, $insertDataArray);

            if (!isset($esResult['code']) || $esResult['code'] !== 0) {
                $result = 0;
                plog(['error' => 'es批量添加银行配置失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$path'=>$path], 'AdminBankFacadeService', 'importData');

                DB::rollBack();
            }

             DB::commit();
        }

        return $result;
    }

    /**
     * 导出表格数据
     *
     * @param [type] $bankList
     * @return void
     */
    protected function exportData($exportColelction)
    {
        $cloumn = [['银行民称','银行编码','是否常用','添加时间']];

        $dataArray = [];

        foreach ($exportColelction as $key => $valueObject) {
            $listArray = [];

            $listArray[] = $valueObject->bank_name;
            $listArray[] = $valueObject->bank_code;
            $listArray[] = $valueObject->is_default == 1 ? '是' : '否';
            $listArray[] = $valueObject->created_at;

            $dataArray[] =  $listArray;
        }

        $title = "银行信息表-".date('YmdHis');

        $path = self::$storage_public_path."Excel";

        $savePath = storage_path($path);

        ExcelFacade::exportExcelData($cloumn, $dataArray, $title, $savePath);

        return $title;
    }

    /**
     * 获取默认的用户选项
     *
     * @return void
     */
    public function defaultBank()
    {
        $result = code(config('admin_code.GetDefaultBankError'));

        $indexName = config('common_es.indices.system.banks');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);
        $esQuery->whereNull('deleted_at');
        $esQuery->where('is_default', 1);
        $esQuery->orderBy('id', 'asc');

        $bankColelction = $esQuery->limit(10)->get();

        $result = new EsBankCollection($bankColelction, ['code' => 0,'msg' => '获取默认银行列表成功!']);

        return $result;
    }

    /**
     * 查找用户
     *
     * @param [type] $find
     * @return void
     */
    public function findBank(FindBankDTO $requestDTO)
    {
        $result = code(config('admin_code.FindBankError'));

        $validated = $requestDTO->toArray();

        $indexName = config('common_es.indices.system.banks');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);
        $esQuery->whereNull('deleted_at');
        $esQuery->orWhereLike('bank_name', $validated['find']);
        $esQuery->orWhereLike('bank_code', $validated['find']);
        $esQuery->orderBy('id', 'asc');

        $max_size = config('common_es.max_result_window');

        $bankColelction = $esQuery->limit($max_size)->get();

        $result = new EsBankCollection($bankColelction, ['code' => 0,'msg' => '查找银行列表成功!']);

        return $result;
    }


    /**
     * 查询
     *
     * @param [type] $validated
     * @return void
     */
    public function getBank(GetBankDTO $requestDTO)
    {
        $result = code(config('admin_code.GetBankError'));

        $validated = $requestDTO->toArray();

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.system.banks');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);
        $esQuery->whereNull('deleted_at');

       
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
                    $result = ['code' => 0,'msg' => '获取银行列表成功!','download'=>$download];
                }else{
                    $result = ['code' => 10000,'msg' => '获取银行列表失败!'];

                }
                return $result;
            }
        }

        $bankPaginator = $esQuery->page($currentPage, $perPage)->paginate();

        if (\optional($bankPaginator)) {
            $result = new EsBankCollection($bankPaginator, ['code' => 0,'msg' => '获取银行列表成功!'], $download);
        }

        return  $result;
    }

    /**
     * 添加
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function addBank(AddBankDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddBankError'));

        $validated = $requestDTO->toArray();

        $bankObject = new Bank();

        foreach ($validated as $key => $value) {
            if (\is_null($value)) {
                $value = "";
            }
            $bankObject->$key = $value;
        }

        $bankObject->created_at = time();
        $bankObject->created_time = time();
        $bankResult = $bankObject->save();

        if (!$bankResult) {
            throw new CommonException('AddBankError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'AddBank');

        $indexName = config('common_es.indices.system.banks');

        $insertDataArray = [
            '_docId' => $bankObject->id,
            'id' => $bankObject->id,
            'bank_name' => $bankObject->bank_name,
            'bank_code' => $bankObject->bank_code,
            'is_default' => $bankObject->is_default,
            'sort' => $bankObject->sort,
            'created_time' => $bankObject->created_time,
            'updated_time' => $bankObject->updated_time,
            'created_at' => $bankObject->created_at,
            'updated_at' => $bankObject->updated_at,
            'deleted_at' => $bankObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $bankObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加银行配置失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$bankObject' => $bankObject,'$adminObject' => $adminObject], 'AdminBankFacadeService', 'handleError');
            throw new CommonException('EsAddBankError');
        }

        $result = code(['code' => 0,'msg' => '添加银行成功!']);

        return $result;
    }


    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updateBank(UpdateBankDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateBankError'));

        $validated = $requestDTO->toArray();
        $bankObject = Bank::find($validated['id']);

        if (!$bankObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$bankObject ->revision];

        foreach ($validated as $key => $value) {
            if ($key === 'id') {
                $where[] = ['id','=',$value];
                continue;
            }

            if (\is_null($value)) {
                $value = "";
            }

            $updateDataArray[$key] = $value;
        }

        $updateDataArray['revision'] = $bankObject ->revision + 1;

        $updateDataArray['updated_time'] = time();

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        //更新管理员
        $bankResult = Bank::where($where)->update($updateDataArray);

        if (!$bankResult) {
            throw new CommonException('UpdateBankError');
        }

        $bankObject = $bankObject->fresh();

        CommonEvent::dispatch($adminObject, $validated, 'UpdateBank');

        $indexName = config('common_es.indices.system.banks');

        $updateDataArray = [
            '_docId' => $bankObject->id,
            'id' => $bankObject->id,
            'bank_name' => $bankObject->bank_name,
            'bank_code' => $bankObject->bank_code,
            'is_default' => $bankObject->is_default,
            'sort' => $bankObject->sort,
            'created_time' => $bankObject->created_time,
            'updated_time' => $bankObject->updated_time,
            'created_at' => $bankObject->created_at,
            'updated_at' => $bankObject->updated_at,
            'deleted_at' => $bankObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($indexName, $bankObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新银行配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$bankObject' => $bankObject,'$adminObject' => $adminObject], 'AdminBankFacadeService', 'handleError');
            throw new CommonException('EsAddBankError');
        }

        $result = code(['code' => 0,'msg' => '更新银行成功!']);


        return $result;
    }

    /**
     * 删除
     *
     * @param [type] $id
     * @param [type] $adminObject
     * @return void
     */
    public function deleteBank(DeleteBankDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteBankError'));
        $validated = $requestDTO->toArray();
        $id = $validated['id'];

        $bankObject = Bank::find($id);

        if (!$bankObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $bankObject->deleted_at = date('Y-m-d H:i:s', time());

        $bankResult =  $bankObject->save();

        if (!$bankResult) {
            throw new CommonException('DeleteBankError');
        }

        CommonEvent::dispatch($adminObject, $id, 'DeleteBank');

        $indexName = config('common_es.indices.system.banks');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s', time())
        ];

        $esResult = EsFacade::updateDoc($indexName, $bankObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新银行配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$bankObject' => $bankObject,'$adminObject' => $adminObject], 'AdminBankFacadeService', 'handleError');
            throw new CommonException('EsAddBankError');
        }

        $result = code(['code' => 0,'msg' => '删除银行成功!']);

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function multipleDeleteBank(MultipleDeleteBankDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MultipleDeleteBankError'));

        $validated = $requestDTO->toArray();

        $deleteResult = Bank::whereIn('id', $validated['select_id_array'])->delete();

        if (!$deleteResult) {
            throw new CommonException('MultipleDeleteBankError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'MultipleDeleteBank');

        $indexName = config('common_es.indices.system.banks');

        $select_id_array = $requestDTO->select_id_array;

        $bankCollection = Bank::withTrashed()->whereIn('id', $select_id_array)->get();

        $updateDataArray = [];

        foreach ($bankCollection as $bankObject) {
            $updateDataArray[] = [
                '_docId' => $bankObject->id,
                'id' => $bankObject->id,
                'bank_name' => $bankObject->bank_name,
                'bank_code' => $bankObject->bank_code,
                'is_default' => $bankObject->is_default,
                'sort' => $bankObject->sort,
                'created_time' => $bankObject->created_time,
                'updated_time' => $bankObject->updated_time,
                'created_at' => $bankObject->created_at,
                'updated_at' => $bankObject->updated_at,
                'deleted_at' => $bankObject->deleted_at,
            ];
        }

        $esResult = EsFacade::batchActDoc($indexName, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es批量更新银行配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$bankObject' => $bankObject,'$adminObject' => $adminObject], 'AdminBankFacadeService', 'handleError');
            throw new CommonException('EsAddBankError');
        }

        $result = code(['code' => 0,'msg' => '批量删除银行成功!']);


        return $result;
    }
}
