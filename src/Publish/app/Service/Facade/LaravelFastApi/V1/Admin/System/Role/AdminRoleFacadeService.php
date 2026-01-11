<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-24 22:30:33
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-15 17:33:46
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Admin\System\Role\AdminRoleFacadeService.php
 */

namespace App\Service\Facade\LaravelFastApi\V1\Admin\System\Role;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Collection;

use App\Service\Facade\Traits\V1\AlwaysService;

use App\Exceptions\Admin\CommonException;

use App\Events\Admin\CommonEvent;

use App\Models\LaravelFastApi\V1\System\Role\Role;
use App\Models\LaravelFastApi\V1\System\Union\RolePermissionUnion;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;

use App\Http\Resources\LaravelFastApi\V1\Admin\System\Role\RoleResource;

/**
 * @see \App\Facade\LaravelFastApi\V1\Admin\System\Role\AdminRoleFacade
 */
class AdminRoleFacadeService
{
   public function test()
   {
       echo "AdminRoleFacadeService test";
   }

    use AlwaysService;

    public function __construct()
    {
        $selectField = [
            'id','switch','parent_id','deep','sort','role_name','logic_name'
        ];
        $this->init((new Role),'deep',$selectField);
    }

     /**
     * 重写树形数据获取方法（处理权限字段，保持返回Collection）
     * @param array $with 关联查询，默认关联permission
     * @return Collection 带权限ID数组的树形集合
     */
    public function getTreeData($with = ['permission']): Collection
    {
        // 1. 直接调用Trait中的核心逻辑获取原始树形结构（不使用parent::）
        $tree = $this->getAllTree();
        $data = new Collection();

        if (!empty($tree)) {
            // 2. 按层级查询数据并处理关联
            for ($i = 0; $i < count($tree); $i++) {
                $query = $this->model::select($this->selectField)
                    ->where($this->field, $tree[$i])
                    ->orderBy($this->sortField, $this->sortOrder)
                    ->orderBy('sort', 'desc');

                if (!empty($with)) {
                    $query->with($with);
                }

                $this->treeData[$i] = $query->get();
            }

            // 3. 组装树形结构（从倒数第二级向上绑定子节点）
            for ($i = count($tree); $i > 1; $i--) {
                $this->treeData[$i - 2]->each(function ($parent) use ($i) {
                    $parent->children = new Collection();
                    $this->treeData[$i - 1]->each(function ($child) use ($parent) {
                        if ($child->parent_id == $parent->id) {
                            $parent->children->push($child);
                        }
                    });
                });
            }

            $data = $this->treeData[0];
        }

        // 4. 处理权限字段：将permission对象集合转为ID数组
        $this->formatPermissionInCollection($data);

        return $data;
    }

    /**
     * 递归处理集合中的权限字段
     * @param Collection $collection 待处理的集合（支持嵌套children）
     */
    private function formatPermissionInCollection(Collection $collection): void
    {
        $collection->each(function ($item) {
            // 处理当前节点的permission字段
            if (isset($item->permission) && $item->permission instanceof Collection) {
                $item->permission = $item->permission->pluck('id')->all();
            }

            // 递归处理子节点
            if (isset($item->children) && $item->children instanceof Collection) {
                $this->formatPermissionInCollection($item->children);
            }
        });
    }

    /**
     * 获取树形角色
     *
     * @return void
     */
    public function getTreeRole($with=['permission'])
    {
        $result = code(config('admin_code.GetRoleError'));

        $redisTreeRole = Redis::hget('system:config','treeRoles');

        if($redisTreeRole)
        {
            $treeRole = \json_decode($redisTreeRole ,true);
        }
        else
        {
           
			$treeRole = $this->getTreeData($with);

			$treeRole = RoleResource::collection($treeRole);

            $redisResult = Redis::hset('system:config','treeRoles',json_encode($treeRole));
        }

        $data = [];

        $data['data'] = $treeRole;

        $result = code(['code'=>0,'msg'=>'获取树形角色成功!'],$data);

        return  $result;
    }

    /**
     * 添加角色
     *
     * @param [type] $validated
     * @param [type] $user
     * @return void
     */
    public function addRole($validated,$admin)
    {
        $result = code(config('admin_code.AddRoleError'));

        $role = new Role;

        foreach ( $validated as $key => $value)
        {
            if(\is_null($value))
            {
                $value = "";
            }
            $role->$key = $value;
        }

        $role->switch = 1;
        $role->created_time = time();
        $role->created_at = time();

        $roleResult = $role->save();

        if(!$roleResult)
        {
            throw new CommonException('AddRoleError');
        }

        CommonEvent::dispatch($admin,$validated,'AddRole');

        Redis::hdel('system:config','treeRoles');
		// 因为重置了角色权限,所以需要清空缓存
		Redis::hdel('system:config','develop_permission');
		Redis::hdel('system:config','permission');

        $result = code(['code'=>0,'msg'=>'添加角色成功!']);

        return $result;
    }

    /**
     * 更新角色
     *
     * @param [type] $validated
     * @param [type] $user
     * @return void
     */
    public function updateRole($validated,$admin)
    {

        $result = code(config('admin_code.UpdateRoleError'));

        $role = Role::find($validated['id']);

        if(!$role)
        {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];
        $updateData = [];

        $where[] = ['revision','=',$role->revision];

        foreach ( $validated as $key => $value)
        {
            if($key === 'id')
            {
                $where[] = ['id','=',$value];
                continue;
            }

            if(\is_null($value))
            {
                $value = "";
            }

            $updateData[$key] = $value;
        }

        $updateData['updated_time'] = time();

        $updateData['updated_at']  =  date('Y-m-d H:i:s',time());
        $updateData['revision']  = $role->revision + 1;

        $roleResult = Role::where($where)->update($updateData);

        if(!$roleResult)
        {
            throw new CommonException('UpdateRoleError');
        }

        CommonEvent::dispatch($admin,$validated,'UpdateRole');

        Redis::hdel('system:config','treeRoles');
		// 因为重置了角色权限,所以需要清空缓存
		Redis::hdel('system:config','develop_permission');
		Redis::hdel('system:config','permission');

        $result = code(['code'=>0,'msg'=>'更新角色成功!']);

        return $result;
    }

    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function moveRole($validated,$admin)
    {
        $result = code(config('admin_code.MoveRoleError'));

        $role = Role::find($validated['id']);

        $roleRevision = $role->revision;

        $oldDeep = $role->deep;

        $parentDeep = 1;

        if($validated['parent_id'])
        {
            $parentRole = Role::find($validated['parent_id']);

            $parentDeep = $parentRole->deep  + 1;
        }

        if(self::$dropType[$validated['dropType']] == 10)
        {
            $roleUpdate = [
                'updated_time'=>time(),
                'updated_at'=>date('Y-m-d H:i:s',time()),
                'parent_id' => $validated['parent_id'],
                'deep' =>$parentDeep,
                'revision'=>$roleRevision + 1
            ];
        }

        if(self::$dropType[$validated['dropType']] == 20)
        {
            $roleUpdate = [
                'updated_time'=>time(),
                'updated_at'=>date('Y-m-d H:i:s',time()),
                'parent_id' => $validated['parent_id'],
                'sort'=> $validated['sort'],
                'deep'=>$parentDeep,
                'revision'=>$roleRevision + 1
            ];
        }

        if(self::$dropType[$validated['dropType']] == 30)
        {
            $roleUpdate = [
                'updated_time'=>time(),
                'updated_at'=>date('Y-m-d H:i:s',time()),
                'parent_id' => $validated['parent_id'],
                'sort'=> $validated['sort'],
                'deep'=>$parentDeep,
                'revision'=>$roleRevision + 1
            ];
        }

        $roleWhere = [['id','=',$validated['id']],['revision','=',$roleRevision]];

        //更新配置项
        $roleResult =Role::where($roleWhere)->update($roleUpdate);

        if(!$roleResult)
        {
            throw new CommonException('MoveRoleError');
        }

        CommonEvent::dispatch($admin,$validated,'MoveRole');

        //修改子级deep
        $deepNumber = $parentDeep - $oldDeep;

        $updateDeepResult = $this->updateChildrenDeep($role->id,$deepNumber);

        if(!$updateDeepResult)
        {
            throw new CommonException('MoveRoleChildrenDeepError');
        }

        //清空redis的缓存数据
        $redisRoleResult = Redis::hdel('system:config','treeRoles');

        $result = code(['code'=>0,'msg'=>'移动角色成功!']);

        return $result;
    }

    /**
     * 删除角色
     *
     * @param [type] $id
     * @param [type] $user
     * @return void
     */
    public function deleteRole($validated,$admin)
    {
        $result = code(config('admin_code.DeleteRoleError'));

        $id = $validated['id'];

		//p($validated);die;

		$systemRoleIdArray = [10,20,30,40,100,200,300,400];
        //系统角色不允许删除
        if(in_array($id,$systemRoleIdArray))
        {
           throw new CommonException('DeleteSystemRoleError');
        }

        //查看是否有子类
        $role = Role::where('parent_id',$id)->get();

        $count = $role->count();

        if($count)
        {
            throw new CommonException('DeleteNoRoleError');
        }

        //查看是否有用户具有该角色
        $userRole = UserRoleUnion::where('role_id',$id)->get();

        $userCount = $userRole->count();

        if($userCount)
        {
            throw new CommonException('DeleteNoUserRoleError');
        }

        $delRole = Role::find($id);

		if(!$delRole)
		{
			throw new CommonException('ThisDataNotExistsError');
		}

        $delRole->deleted_at = date('Y-m-d H:i:s',time());

        $delRoleResult =  $delRole->save();

        if(!$delRoleResult)
        {
            throw new CommonException('DeleteNoUserRoleError');
        }

        CommonEvent::dispatch($admin,$validated,'DeleteRole');

        //清空redis的缓存数据
        Redis::hdel('system:config','treeRoles');
		// 因为重置了角色权限,所以需要清空缓存
		Redis::hdel('system:config','develop_permission');
		Redis::hdel('system:config','permission');

        $result = code(['code'=>0,'msg'=>'删除角色成功!']);

        return $result;
    }

    /**
     * 重置更新权限
     *
     * @param [type] $validated
     * @param [type] $user
     * @return void
     */
    public function resetRolePermission($validated ,$admin)
    {
        $result = code(config('admin_code.ResetRolePermissionError'));

        $role_id = $validated['role_id'];
        $beforePermissin = isset($validated['before_permission'])?$validated['before_permission']:[];
        $afterPermissin = isset($validated['after_permission'])?$validated['after_permission']:[];

		//p($validated);die;

        $deleteResult = 1;
        //先清空之前的权限
        if(\is_array($beforePermissin) && count($beforePermissin))
        {
            $deleteResult = RolePermissionUnion::where('role_id',$role_id)->whereIn('permission_id',$beforePermissin)->forceDelete();
        }

        if(!$deleteResult)
        {
            throw new CommonException('DeleteBeforeRolePermissionError');
        }

        $insertResult = 1;

        if(\is_array($afterPermissin) && count($afterPermissin))
        {
            $insertData = [];

            $insertData = array_map(function($v)use($role_id)
            {

                return  ['role_id'=>$role_id,'permission_id'=>$v,'created_time'=>time(),'created_at'=>date('Y-m-d H:i:s',time())];

            },$afterPermissin);

             $insertResult = RolePermissionUnion::insert($insertData);
        }

        if(!$insertResult)
        {
            throw new CommonException('InsertAfterRolePermissionError');
        }


        $logData = ['role_id'=>$role_id,'$beforePermissin'=>$beforePermissin,'$afterPermissin' =>$afterPermissin];

        CommonEvent::dispatch($admin,$logData,'ResetRolePermission');

        Redis::hdel('system:config','treeRoles');

		// 因为重置了角色权限,所以需要清空缓存
		Redis::hdel('system:config','develop_permission');
		Redis::hdel('system:config','permission');

        $result = code(['code'=>0,'msg'=>'角色重置权限成功!']);

        return $result;
    }
}
