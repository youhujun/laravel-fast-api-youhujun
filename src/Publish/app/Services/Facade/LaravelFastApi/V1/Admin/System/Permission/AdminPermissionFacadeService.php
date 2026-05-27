<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-05 21:54:15
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-04 15:00:32
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\System\Permission\AdminPermissionFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\System\Permission;

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
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\AddMenuDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\UpdateMenuDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\DeleteMenuDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\MoveMenuDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\SwitchMenuDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\GetChildrenOptionsDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\GetSingleMenuFormDTO;
//事件
use App\Events\LaravelFastApi\V1\Admin\System\Permission\AddMenuEvent;
use App\Events\LaravelFastApi\V1\Admin\System\Permission\UpdateMenuEvent;
use App\Events\LaravelFastApi\V1\Admin\System\Permission\MoveMenuEvent;
use App\Events\LaravelFastApi\V1\Admin\System\Permission\DeleteMenuEvent;
use App\Events\LaravelFastApi\V1\Admin\System\Permission\SwitchMenuEvent;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Permission\Permission;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Permission\EsPermissionResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Permission\EsMenuResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Permission\EsMenuOptionsResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Permission\EsSingleMenuFormResource;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\Permission\PermissionController
 * @see \App\Facades\LaravelFastApi\V1\Admin\System\Permission\AdminPermissionFacade
 */
class AdminPermissionFacadeService
{
    use EsAlwaysService;
    public function test()
    {
        echo "AdminPermissionFacadeService test";
    }
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $esIndexName = config('common_es.indices.system.permissions');
        $this->init((new Permission()), $esIndexName, 'deep');
    }

    //映射数据库key
    protected $mapKey = ['id' => 'id','parent_id' => 'parent_id','deep' => 'deep','type' => 'type','route_name' => 'route_name','route_path' => 'route_path','component' => 'component','perm' => 'permission_tag','hidden' => 'hidden','always_show' => 'always_show','switch' => 'switch','sort' => 'sort','icon' => 'meta_icon','title' => 'meta_title','cache' => 'meta_no_cache','affix' => 'meta_affix','breadcrumb' => 'meta_breadcrumb','active_menu' => 'meta_active_menu','params' => 'params','redirect' => 'redirect'];

    /**
     * 获取树形权限菜单
     *
     * @return void
     * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Login\LoginAfterController
     */
    public function getTreePermission(Admin $adminObject)
    {
        // 1. 获取树形权限（已经是带 children 的树）
        $permissionTree = $this->getTreeData();

        // 2. 一次性查出所有关联 + 角色，内存拼装，避免 N+1
        $maxSize = config('common_es.max_result_window');

        // 所有角色权限关联
        $rolePermissionIndex = config('common_es.indices.union.role_permission_unions');
        $rolePermissions = EsQueryFacade::index($rolePermissionIndex)
            ->limit($maxSize)
            ->get();

        // 所有角色
        $roleIndex = config('common_es.indices.system.roles');
        $roles = EsQueryFacade::index($roleIndex)
            ->limit($maxSize)
            ->get()
            ->keyBy('id');

        // 3. 递归给每个 permission 挂上 roles
        $this->loadRolesForPermissionTree($permissionTree, $rolePermissions, $roles);

        // 4. 资源格式化（不再查库）
        $data = EsPermissionResource::collection($permissionTree);

        return code(['code' => 0, 'msg'  => '获取树形路由成功!'], ['data' => $data]);
    }

    /**
     * 递归给权限树挂载角色（内存操作，0 查询）
     */
    protected function loadRolesForPermissionTree(Collection $tree, Collection $rolePermissions, Collection $roles)
    {
        foreach ($tree as $perm) {
            // 当前权限对应的所有 role_id
            $roleIds = $rolePermissions
                ->where('permission_id', $perm->id)
                ->pluck('role_id');

            // 取出角色标识
            $perm->rolesLogicName = $roles
                ->whereIn('id', $roleIds)
                ->pluck('logic_name')
                ->toArray();

            // 递归处理子节点
            if (isset($perm->children) && $perm->children->isNotEmpty()) {
                $this->loadRolesForPermissionTree($perm->children, $rolePermissions, $roles);
            }
        }
    }

    /**
     * 获取树形菜单
     *
     * @param  Admin $adminObject
     */
    public function getTreeMenu(Admin $adminObject)
    {
        $treePermission = $this->getTreeData();

        // p($treePermission);
        // die;

        $data = [];

        //p($treePermission);die;
        $treeMenu = EsMenuResource::collection($treePermission);

        $data['data'] = $treeMenu;

        $result = code(['code' => 0,'msg' => '获取树形编辑菜单成功!'], $data);

        return $result;
    }

    /**
     * 获取子级选项
     *
     * @param  Admin $adminObject
     */
    public function getChildrenOptions(Admin $adminObject)
    {
        $treePermissionCollection = $this->getTreeData();

        $data = [];

        $treeMenuResource = EsMenuOptionsResource::collection($treePermissionCollection);

        $dataArray['data'] = $treeMenuResource;

        $result = code(['code' => 0,'msg' => '获取树形菜单选项成功!'], $dataArray);

        return $result;
    }

    /**
     * 获取单个菜单表单数据
     *
     * @param  Admin  $adminObject
     * @param  [type] $validated
     */
    public function getSingleMenuForm(Admin $adminObject, GetSingleMenuFormDTO $requestDTO)
    {
        $result = code(config('admin_code.GetReplaceError'));

        $validated = $requestDTO->toArray();

        $esIndexName = config('common_es.indices.system.permissions');

        $permissionOBject = EsQueryFacade::index($esIndexName)->where('id', $validated['id'])->get()->first();

        if (!$permissionOBject) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $formDataResource = new EsSingleMenuFormResource($permissionOBject);

        $dataArray['data'] = $formDataResource;

        $result = code(['code' => 0,'msg' => '获取单个表单数据成功!'], $dataArray);

        return $result;
    }


    /**
     * 添加菜单
     *
     * @param [type] $data
     * @return void
     */
    public function addMenu(AddMenuDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddMenuError'));

        $validated = $requestDTO->toArray();

        $mapKey = $this->mapKey;

        $permissionObject = new Permission();

        foreach ($validated as $key => $value) {
            if ($key === "params" || $key === "id") {
                continue;
            } else {
                if (\is_null($value) || empty($value)) {
                    // 如果是数组，保持为空数组而不是转换为字符串
                    $value = is_array($value) ? [] : " ";
                }

                $permissionObject->{$mapKey[$key]} = $value;
            }
        }

        $permissionObject->created_time = time();
        $permissionObject->created_at = time();

        $permissionResult = $permissionObject->save();

        if (!$permissionResult) {
            throw new CommonException('AddMenuError');
        }

        $permissionObject = $permissionObject->fresh();

        AddMenuEvent::dispatch($permissionObject, $adminObject);

        CommonEvent::dispatch($adminObject, $requestDTO, 'AddMenu');

        $result = code(['code' => 0,'msg' => '添加菜单成功!']);

        return $result;
    }

    /**
     * 更新菜单
     *
     * @param [type] $validated
     * @return void
     */
    public function updateMenu(UpdateMenuDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateMenuError'));

        $validated = $requestDTO->toArray();

        $permissionObject =  Permission::find($validated['id']);

        if (!optional($permissionObject)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $mapKey = $this->mapKey;

        //查看级别是否变化
        $type = 0;

        foreach ($validated as $key => $value) {
            if ($key === "params" || $key === "id") {
                continue;
            } else {
                if ($mapKey[$key] === 'deep') {
                    if ($permissionObject->{$mapKey[$key]} < $value) {
                        //增加级别 实际是变成子级
                        $type = 1;
                    }

                    if ($permissionObject->{$mapKey[$key]} > $value) {
                        //减少级别 实际是提升级别
                        $type = 2;
                    }
                }

                if (\is_null($value) || empty($value)) {
                    // 如果是数组，保持为空数组而不是转换为字符串
                    $value = is_array($value) ? [] : " ";
                }

                $permissionObject->{$mapKey[$key]} = $value;
            }
        }

        $permissionObject->updated_time = time();

        $permissionObject->updated_at = time();

        $permissionResult = $permissionObject->save();

        if (!$permissionResult) {
            throw new CommonException('UpdateMenuError');
        }

        $childrenData = $this->getAllChildren($validated['id']);

        $updateChildrenResult = 1;

        if ($type == 1) {
            $updateChildrenResult = $this->updateChildrenDeep($childrenData['data'], 1);
        }

        if ($type == 2) {
            $updateChildrenResult = $this->updateChildrenDeep($childrenData['data'], 0);
        }

        if (!$updateChildrenResult) {
            throw new CommonException('UpdateMenuDeepError');
        }

        UpdateMenuEvent::dispatch($permissionObject, $adminObject);

        CommonEvent::dispatch($adminObject, $validated, 'UpdateMenu');

        $result = code(['code' => 0,'msg' => '更新菜单成功!']);

        return $result;
    }

    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function moveMenu(MoveMenuDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MoveMenuError'));

        $validated = $requestDTO->toArray();

        $permissionObject = Permission::find($validated['id']);

        $routePath = $permissionObject->route_path;

        if (!optional($permissionObject)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $permissionRevision = $permissionObject->revision;

        $oldDeep = $permissionObject->deep;

        $parentDeep = 1;

        if ($validated['parent_id']) {
            $parentPermission = Permission::find($validated['parent_id']);

            $parentDeep = $parentPermission->deep + 1;
        }


        if (self::$dropType[$validated['dropType']] == 10) {
            //处理路由路径
            $route_path = $this->processRoutePath($routePath, $parentDeep);

            $permissionUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'deep' => $parentDeep,
                'route_path' => $route_path,
                'revision' => $permissionRevision  + 1
            ];
        }

        if (self::$dropType[$validated['dropType']] == 20) {
            //处理路由路径
            $route_path = $this->processRoutePath($routePath, $parentDeep);
            $permissionUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'sort' => $validated['sort'],
                'deep' => $parentDeep,
                'route_path' => $route_path,
                'revision' => $permissionRevision  + 1
            ];
        }

        if (self::$dropType[$validated['dropType']] == 30) {
            //处理路由路径
            $route_path = $this->processRoutePath($routePath, $parentDeep);
            $permissionUpdate = [
                'updated_time' => time(),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'parent_id' => $validated['parent_id'],
                'sort' => $validated['sort'],
                'deep' => $parentDeep,
                'route_path' => $route_path,
                'revision' => $permissionRevision  + 1
            ];
        }

        $permissionWhere = [['id','=',$validated['id']],['revision','=',$permissionRevision]];

        //更新配置项
        $permissionResult = Permission::where($permissionWhere)->update($permissionUpdate);

        if (!$permissionResult) {
            throw new CommonException('MoveMenuError');
        }

        //修改子级deep
        $deepNumber = $parentDeep - $oldDeep;

        $updateDeepResult = $this->updateChildrenDeep($permissionObject->id, $deepNumber);

        if (!$updateDeepResult) {
            throw new CommonException('MoveMenuDeepError');
        }

        MoveMenuEvent::dispatch($permissionObject, $adminObject);

        CommonEvent::dispatch($adminObject, $validated, 'MoveMenu');

        $result = code(['code' => 0,'msg' => '移动菜单成功!']);

        return $result;
    }

    /**
     * 删除菜单
     *
     * @param [type] $id
     * @return void
     */
    public function deleteMenu(DeleteMenuDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteMenuError'));

        $validated = $requestDTO->toArray();

        //删除菜单之前要查看是否有子级菜单
        $permissionObject = Permission::where('parent_id', $validated['id'])->get();

        $count = $permissionObject->count();

        if ($count) {
            throw new CommonException('ThisDataHasChildrenError');
        }

        if ($count == 0) {
            $delPermissionObject = Permission::find($validated['id']);

            if (!optional($delPermissionObject)) {
                throw new CommonException('ThisDataNotExistsError');
            }


            $delPermissionObject->deleted_at = date('Y-m-d H:i:s', time());

            $delPermissionResult =  $delPermissionObject->save();

            if (!$delPermissionResult) {
                throw new CommonException('DeleteMenuError');
            }

            DeleteMenuEvent::dispatch($delPermissionObject, $adminObject);

            CommonEvent::dispatch($adminObject, $validated, 'DeleteMenu');

            $result = code(['code' => 0,'msg' => '删除菜单成功!']);
        }

        return $result;
    }

    /**
     * 禁用或者开启
     *
     * @param [type] $id
     * @return void
     */
    public function switchMenu(SwitchMenuDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DisableMenuError'));

        $validated = $requestDTO->toArray();

        $eventName = 'DisableMenu';

        if ($validated['switch']) {
            $result = code(config('admin_code.AbleMenuError'));

            $eventName = 'AbleMenu';
        }

        $where = [];
        $updateDataArray = [];

        $permissionObject = Permission::find($validated['id']);

        if (!optional($permissionObject)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        //获取子级数据
        $childrenData = $this->getAllChildren($validated['id']);

        if (isset($childrenData['idDataArray'])) {
            array_push($childrenData['idDataArray'], $validated['id']);
        }

        $updateDataArray = [
            'updated_time' => time(),
            'updated_at' => \date('Y-m-d H:i:s', time()),
            'switch' => $validated['switch']
        ];

        $permissionResult = Permission::whereIn('id', $childrenData['idDataArray'])->update($updateDataArray);

        if (!$permissionResult) {
            if ($validated['switch']) {
                throw new CommonException('AbleMenuError');
            }

            throw new CommonException('DisableMenuError');
        }

        SwitchMenuEvent::dispatch($permissionObject, $adminObject);

        CommonEvent::dispatch($adminObject, $validated, $eventName);

        $result = code(['code' => 0,'msg' => '禁用菜单成功!']);

        if ($validated['switch']) {
            $result = code(['code' => 0,'msg' => '开启菜单成功!']);
        }

        return $result;
    }

    /**
     * 根据deep参数处理routePath
     * @param string $routePath 待处理的路径
     * @param int $deep 层级参数（1表示一级路径，>1表示多级路径）
     * @return string 处理后的路径
     */
    public function processRoutePath($routePath, $deep)
    {
        // 移除路径中所有的/（用于重新构建符合规则的路径）
        $cleaned = str_replace('/', '', $routePath);

        // 根据deep处理路径
        if ($deep == 1) {
            // 一级路径：开头必须有且仅有一个/
            return '/' . $cleaned;
        } elseif ($deep > 1) {
            // 多级路径：开头不能有/
            return $cleaned;
        } else {
            // 若deep为非正数，可根据业务需求处理（此处返回原始路径）
            return $routePath;
        }
    }
}
