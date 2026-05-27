<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-05 21:54:15
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-02 01:47:25
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Template\V1\Replace\EsReplaceAdminTeeFacadeService.php
 */

namespace App\Services\Facade\Template\V1\Replace;

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
use App\DTOs\Template\V1\Replace\GetReplaceDTO;
use App\DTOs\Template\V1\Replace\AddReplaceDTO;
use App\DTOs\Template\V1\Replace\DisableReplaceDTO;
use App\DTOs\Template\V1\Replace\DeleteReplaceDTO;
use App\DTOs\Template\V1\Replace\MoveReplaceDTO;
//事件
use App\Events\Template\V1\Replace\AddReplaceEvent;
use App\Events\Template\V1\Replace\UpadteReplaceEvent;
use App\Events\Template\V1\Replace\MoveReplaceEvent;
use App\Events\Template\V1\Replace\DeleteReplaceEvent;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\Replace\Replace;
use App\Http\Resources\Template\V1\Es\Replace\EsReplaceResource;
use App\Http\Resources\Template\V1\Es\Replace\EsReplaceCollection;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\Permission\PermissionController
 * @see \App\Facades\LaravelFastApi\V1\Admin\System\Permission\AdminPermissionFacade
 */
class EsReplaceAdminTeeFacadeService
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
     * 获取树形菜单
     *
     * @param  Admin $adminObject
     */
    public function getTreeReplace(Admin $adminObject)
    {
        $treePermission = $this->getTreeData();

        // p($treePermission);
        // die;

        $data = [];

        //p($treePermission);die;
        $treeReplace = EsReplaceResource::collection($treePermission);

        $data['data'] = $treeReplace;

        $result = code(['code' => 0,'msg' => '获取树形编辑菜单成功!'], $data);

        return $result;
    }


    /**
     * 添加菜单
     *
     * @param [type] $data
     * @return void
     */
    public function addReplace(AddReplaceDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddReplaceError'));

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
            throw new CommonException('AddReplaceError');
        }

        AddReplaceEvent::dispatch($permissionObject, $adminObject);

        CommonEvent::dispatch($adminObject, $validated, 'AddReplace');

        $result = code(['code' => 0,'msg' => '添加菜单成功!']);

        return $result;
    }

    /**
     * 更新菜单
     *
     * @param [type] $validated
     * @return void
     */
    public function updateReplace(UpdateReplaceDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateReplaceError'));

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
            throw new CommonException('UpdateReplaceError');
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
            throw new CommonException('UpdateReplaceDeepError');
        }

        UpdateReplaceEvent::dispatch($permissionObject, $adminObject);

        CommonEvent::dispatch($adminObject, $validated, 'UpdateReplace');

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
    public function moveReplace(MoveReplaceDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MoveReplaceError'));

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
            throw new CommonException('MoveReplaceError');
        }

        //修改子级deep
        $deepNumber = $parentDeep - $oldDeep;

        $updateDeepResult = $this->updateChildrenDeep($permissionObject->id, $deepNumber);

        if (!$updateDeepResult) {
            throw new CommonException('MoveReplaceDeepError');
        }

        MoveReplaceEvent::dispatch($permissionObject, $adminObject);

        CommonEvent::dispatch($adminObject, $validated, 'MoveReplace');

        $result = code(['code' => 0,'msg' => '移动菜单成功!']);

        return $result;
    }

    /**
     * 删除菜单
     *
     * @param [type] $id
     * @return void
     */
    public function deleteReplace(DeleteReplaceDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteReplaceError'));

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
                throw new CommonException('DeleteReplaceError');
            }

            DeleteReplaceEvent::dispatch($delPermissionObject, $adminObject);

            CommonEvent::dispatch($adminObject, $validated, 'DeleteReplace');

            $result = code(['code' => 0,'msg' => '删除菜单成功!']);
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
