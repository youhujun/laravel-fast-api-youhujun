<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-24 22:30:33
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-06 13:00:21
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\System\Role\AdminRoleFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\System\Role;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Collection;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Services\Facade\Traits\V1\Es\EsAlwaysService;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\System\Role\AddRoleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Role\UpdateRoleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Role\MoveRoleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Role\DeleteRoleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Role\ResetRolePermissionDTO;
//Job
use App\Jobs\LaravelFastApi\V1\Admin\System\Role\EsAddRoleJob;
use App\Jobs\LaravelFastApi\V1\Admin\System\Role\EsUpdateRoleJob;
use App\Jobs\LaravelFastApi\V1\Admin\System\Role\EsDeleteRoleJob;
use App\Jobs\LaravelFastApi\V1\Admin\System\Role\EsMoveRoleJob;
use App\Jobs\LaravelFastApi\V1\Admin\System\Role\EsResetRolePermissionJob;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Role\Role;
use App\Models\LaravelFastApi\V1\System\Union\RolePermissionUnion;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Role\EsRoleResource;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\Role\RoleController
 * @see \App\Facades\LaravelFastApi\V1\Admin\System\Role\AdminRoleFacade
 */
class AdminRoleFacadeService
{
    use EsAlwaysService;
    public function test()
    {
        echo "AdminRoleFacadeService test";
    }

    public function __construct()
    {
        $esIndexName = config('common_es.indices.system.roles');
        $this->init((new Role()), $esIndexName, 'deep');
    }

    /**
     * 获取树形角色
     *
     * @return void
     */
    public function getTreeRole()
    {
        $result = code(config('admin_code.GetRoleError'));

        $treeRoleCollection = $this->getTreeData();

        $treeRoleCollection = $this->formatRolePermissionData($treeRoleCollection);

        $treeRoleCollection = EsRoleResource::collection($treeRoleCollection);

        $data['data'] = $treeRoleCollection;

        $result = code(['code' => 0,'msg' => '获取树形角色成功!'], $data);

        return  $result;
    }

    protected function formatRolePermissionData($treeRoleCollection)
    {
        $indexName = config('common_es.indices.union.role_permission_unions');

        foreach ($treeRoleCollection as &$role) {
            $esQuery = EsQueryFacade::index($indexName);

            $esQuery->whereNull('deleted_at');

            $max_size = config('common_es.max_result_window');

            $rolePermissionUnionCollection = $esQuery->where('role_id', $role->id)->limit($max_size)->get();

            $permission_array = $rolePermissionUnionCollection->pluck('permission_id')->toArray();

            $role->permission_array = $permission_array;
        }

        return $treeRoleCollection;
    }
    /**
     * 添加角色
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function addRole(AddRoleDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddRoleError'));

        $validated = $requestDTO->toArray();

        $roleObject = new Role();

        foreach ($validated as $key => $value) {
            if (\is_null($value)) {
                $value = "";
            }
            $roleObject->$key = $value;
        }

        $roleObject->switch = 1;
        $roleObject->created_time = time();
        $roleObject->created_at = time();

        $roleResult = $roleObject->save();

        if (!$roleResult) {
            throw new CommonException('AddRoleError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'AddRole');

        $esIndexName = config('common_es.indices.system.roles');

        $insertDataArray = [
            '_docId' => $roleObject->id,
            'id' => $roleObject->id,
            'parent_id' => $roleObject->parent_id,
            'deep' => $roleObject->deep,
            'type' => $roleObject->type,
            'is_system' => $roleObject->is_system,
            'switch' => $roleObject->switch,
            'role_name' => $roleObject->role_name,
            'logic_name' => $roleObject->logic_name,
            'sort' => $roleObject->sort,
            'created_time' => $roleObject->created_time,
            'updated_time' => $roleObject->updated_time,
            'created_at' => $roleObject->created_at,
            'updated_at' => $roleObject->updated_at,
            'deleted_at' => $roleObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($esIndexName, $insertDataArray, $roleObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es添加角色失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$roleObject' => $roleObject,'$adminObject' => $adminObject], 'AdminRoleFacadeService', 'handleError');
            throw new CommonException('EsAddRoleError');
        }

        $result = code(['code' => 0,'msg' => '添加角色成功!']);

        return $result;
    }

    /**
     * 更新角色
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function updateRole(UpdateRoleDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateRoleError'));

        $validated = $requestDTO->toArray();

        //p($validated);die;

        $roleObject = Role::find($validated['id']);

        if (!$roleObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];
        $updateDataArray = [];

        $where[] = ['revision','=',$roleObject->revision];

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

        $updateDataArray['updated_time'] = time();

        $updateDataArray['updated_at']  =  date('Y-m-d H:i:s', time());
        $updateDataArray['revision']  = $roleObject->revision + 1;

        $roleResult = Role::where($where)->update($updateDataArray);

        if (!$roleResult) {
            throw new CommonException('UpdateRoleError');
        }

        $roleObject = $roleObject->fresh();

        $esIndexName = config('common_es.indices.system.roles');

        $updateDataArray = [
            'type' => $roleObject->type,
            'is_system' => $roleObject->is_system,
            'switch' => $roleObject->switch,
            'role_name' => $roleObject->role_name,
            'logic_name' => $roleObject->logic_name,
            'sort' => $roleObject->sort,
            'updated_time' => $roleObject->updated_time,
            'updated_at' => $roleObject->updated_at,
        ];

        $esResult = EsFacade::updateDoc($esIndexName, $roleObject->id, $updateDataArray);

        //plog(['error' => 'es更新角色','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$roleObject' => $roleObject,'$adminObject' => $adminObject], 'EsUpdateRoleJob', 'handle');

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新角色失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$roleObject' => $roleObject,'$adminObject' => $adminObject], 'AdminRoleFacadeService', 'handleError');
            throw new CommonException('EsUpdateRoleError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'UpdateRole');


        $result = code(['code' => 0,'msg' => '更新角色成功!']);

        return $result;
    }

    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function moveRole(MoveRoleDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MoveRoleError'));

        $validated = $requestDTO->toArray();

        $roleObject = Role::find($validated['id']);

        $roleRevision = $roleObject->revision;

        $oldDeep = $roleObject->deep;

        $parentDeep = 1;

        if ($validated['parent_id']) {
            $parentRole = Role::find($validated['parent_id']);

            $parentDeep = $parentRole->deep  + 1;
        }

        if (self::$dropType[$validated['dropType']] == 10) {
            $roleUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'deep' => $parentDeep,
                'revision' => $roleRevision + 1
            ];
        }

        if (self::$dropType[$validated['dropType']] == 20) {
            $roleUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'sort' => $validated['sort'],
                'deep' => $parentDeep,
                'revision' => $roleRevision + 1
            ];
        }

        if (self::$dropType[$validated['dropType']] == 30) {
            $roleUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'sort' => $validated['sort'],
                'deep' => $parentDeep,
                'revision' => $roleRevision + 1
            ];
        }

        $roleWhere = [['id','=',$validated['id']],['revision','=',$roleRevision]];

        //更新配置项
        $roleResult = Role::where($roleWhere)->update($roleUpdate);

        if (!$roleResult) {
            throw new CommonException('MoveRoleError');
        }

        CommonEvent::dispatch($adminObject, $validated, 'MoveRole');

        //修改子级deep
        $deepNumber = $parentDeep - $oldDeep;

        $updateDeepResult = $this->updateChildrenDeep($roleObject->id, $deepNumber);

        if (!$updateDeepResult) {
            throw new CommonException('MoveRoleChildrenDeepError');
        }

        $esIndexName = config('common_es.indices.system.roles');

        $queryArray = [
            'match_all' => new \stdClass()
        ];

        $deleteEsResult = EsFacade::deleteByQuery($esIndexName, $queryArray);


        if (!isset($deleteEsResult['code']) || $deleteEsResult['code'] != 0) {
            plog(['error' => 'EsMoveRoleJobError','$deleteEsResult' => $deleteEsResult,'$roleObject' => $roleObject,'$adminObject' => $adminObject], 'AdminRoleFacadeService', 'handleError');
            throw new CommonException('EsMoveRoleError');
        }

        \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncRole();

        $result = code(['code' => 0,'msg' => '移动角色成功!']);

        return $result;
    }

    /**
     * 删除角色
     *
     * @param [type] $id
     * @param [type] $userObject
     * @return void
     */
    public function deleteRole(DeleteRoleDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteRoleError'));

        $validated = $requestDTO->toArray();

        $id = $validated['id'];

        //p($validated);die;

        //系统角色不允许删除
        $esRoleIndexName = config('common_es.indices.system.roles');

        $system_role_id_array = EsQueryFacade::index($esRoleIndexName)->where('is_system', 1)->get()->pluck('id')->toArray();

        if (in_array($id, $system_role_id_array)) {
            throw new CommonException('DeleteSystemRoleError');
        }

        //查看是否有用户具有该角色
        $esUserRoleUnionName = config('common_es.indices.union.user_role_unions');

        $useCount = EsQueryFacade::index($esUserRoleUnionName)->where('role_id', $id)->get()->count();

        if ($useCount) {
            throw new CommonException('DeleteNoUserRoleError');
        }

        //查看是否有子类
        $roleObject = Role::where('parent_id', $id)->get();

        $count = $roleObject->count();

        if ($count) {
            throw new CommonException('DeleteNoRoleError');
        }

        $delRoleObject = Role::find($id);

        if (!$delRoleObject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $delRoleObject->deleted_at = date('Y-m-d H:i:s', time());

        $delRoleResult =  $delRoleObject->save();

        if (!$delRoleResult) {
            throw new CommonException('DeleteNoUserRoleError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'DeleteRole');


        $esIndexName = config('common_es.indices.system.roles');

        $updateDataArray = [
            'deleted_at' => $delRoleObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($esIndexName, $delRoleObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es删除角色失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$roleObject' => $delRoleObject,'$adminObject' => $adminObject], 'AdminRoleFacadeService', 'handleError');
            throw new CommonException('EsDeleteRoleError');
        }

        $result = code(['code' => 0,'msg' => '删除角色成功!']);

        return $result;
    }

    /**
     * 重置更新权限
     *
     * @param [type] $validated
     * @param [type] $userObject
     * @return void
     */
    public function resetRolePermission(ResetRolePermissionDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.ResetRolePermissionError'));

        $validated = $requestDTO->toArray();

        $role_id = $validated['role_id'];
        $beforePermissin = isset($validated['before_permission']) ? $validated['before_permission'] : [];
        $afterPermissin = isset($validated['after_permission']) ? $validated['after_permission'] : [];

        //p($validated);die;

        $deleteResult = 1;
        //先清空之前的权限
        if (\is_array($beforePermissin) && count($beforePermissin)) {
            $deleteResult = RolePermissionUnion::where('role_id', $role_id)->whereIn('permission_id', $beforePermissin)->forceDelete();
        }

        if (!$deleteResult) {
            throw new CommonException('DeleteBeforeRolePermissionError');
        }

        $insertResult = 1;

        if (\is_array($afterPermissin) && count($afterPermissin)) {
            $insertData = [];

            $insertData = array_map(function ($v) use ($role_id) {
                return  ['role_id' => $role_id,'permission_id' => $v,'created_time' => time(),'created_at' => date('Y-m-d H:i:s', time())];
            }, $afterPermissin);

            $insertResult = RolePermissionUnion::insert($insertData);
        }

        if (!$insertResult) {
            throw new CommonException('InsertAfterRolePermissionError');
        }


        $logData = ['role_id' => $role_id,'$beforePermissin' => $beforePermissin,'$afterPermissin' => $afterPermissin];

        CommonEvent::dispatch($adminObject, $logData, 'ResetRolePermission');

        $indexName = config('common_es.indices.union.role_permission_unions');

        $queryArray = [
            'match_all' => new \stdClass()
        ];

        $deleteEsResult = EsFacade::deleteByQuery($indexName, $queryArray);

        if (!isset($deleteEsResult['code']) || $deleteEsResult['code'] != 0) {
            plog(['error' => 'EsResetRolePermissionJobError','$deleteEsResult' => $deleteEsResult,'$requestDTO' => $requestDTO,'$adminObject' => $adminObject], 'AdminRoleFacadeService', 'handleError');
            throw new CommonException('EsResetRolePermissionError');

        }

        \App\Facades\LaravelFastApi\V1\Es\Sync\Union\EsSyncUnionFacade::syncRolePermissionUnions();

        $result = code(['code' => 0,'msg' => '角色重置权限成功!']);

        return $result;
    }
}
