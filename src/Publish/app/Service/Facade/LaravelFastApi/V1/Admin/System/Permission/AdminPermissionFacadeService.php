<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-05 21:54:15
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-09-27 14:15:14
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Admin\System\Permission\AdminPermissionFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\System\Permission;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Collection;

use App\Exceptions\Admin\CommonException;

use App\Events\Admin\CommonEvent;

use App\Services\Facade\Traits\V1\AlwaysService;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\Permission\Permission;

use App\Http\Resources\LaravelFastApi\V1\Admin\System\Permission\PermissionResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\System\Permission\MenuResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\System\Permission\MenuOptionsResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\System\Permission\SingleMenuFormResource;

use App\Facades\Common\V1\User\User\CommonUserFacade;

/**
 * @see \App\Facades\LaravelFastApi\V1\Admin\System\Permission\AdminPermissionFacade
 */
class AdminPermissionFacadeService
{
   public function test()
   {
       echo "AdminPermissionFacadeService test";
   }

   use AlwaysService;
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->init((new Permission),'deep');
    }

	//映射数据库key
	protected $mapKey = ['id'=>'id','parent_id'=>'parent_id','deep'=>'deep','type'=>'type','route_name'=>'route_name','route_path'=>'route_path','component'=>'component','perm'=>'permission_tag','hidden'=>'hidden','always_show'=>'always_show','switch'=>'switch','sort'=>'sort','icon'=>'meta_icon','title'=>'meta_title','cache'=>'meta_no_cache','affix'=>'meta_affix','breadcrumb'=>'meta_breadcrumb','active_menu'=>'meta_active_menu','params'=>'params','redirect'=>'redirect'];

    /**
     * 获取树形权限菜单
     *
     * @return void
     */
    public function getTreePermission(Admin $admin)
    {
        $result = [];

		$checkResult = 0;

        if(isDevelop($admin))
        {
            $checkResult =  Redis::hexists('system:config','develop_permission');
        }
        else
        {
            $checkResult =  Redis::hexists('system:config','permission');
        }

        //定义树形结构容器
        $treePermission = [];

        if($checkResult)
        {
            if(isDevelop($admin))
            {
                $redisPermission = Redis::hget('system:config','develop_permission');
            }
            else
            {
                $redisPermission = Redis::hget('system:config','permission');
            }

            $treePermission = json_decode( $redisPermission );
        }
        else
        {
            $treePermission = $this->getTreeData();

			//p($treePermission);die;

            if(isDevelop($admin))
            {
                Redis::hset('system:config','develop_permission',json_encode($treePermission));
            }
            else
            {
                Redis::hset('system:config','permission',json_encode($treePermission));
            }
        }


        $data = [];

        $treePermission = PermissionResource::collection($treePermission);

        $data['data'] = $treePermission;

        $result = code(['code'=>0,'msg'=>'获取树形路由成功!'],$data);

        return $result;
    }

	/**
	 * 获取树形菜单
	 *
	 * @param  Admin $admin
	 */
	public function getTreeMenu(Admin $admin)
	{
		$treePermission = $this->getTreeData();

		$data = [];

		//p($treePermission);die;
        $treeMenu = MenuResource::collection($treePermission);

        $data['data'] = $treeMenu;

        $result = code(['code'=>0,'msg'=>'获取树形编辑菜单成功!'],$data);

        return $result;
	}

	/**
	 * 获取子级选项
	 *
	 * @param  Admin $admin
	 */
	public function getChildrenOptions(Admin $admin,$validated)
	{
		$treePermission = $this->getTreeData();

		$data = [];

        $treeMenu = MenuOptionsResource::collection($treePermission);

        $data['data'] = $treeMenu;

		$result = code(['code'=>0,'msg'=>'获取树形菜单选项成功!'],$data);

		return $result;
	}

	/**
	 * 获取单个菜单表单数据
	 *
	 * @param  Admin  $admin
	 * @param  [type] $validated
	 */
	public function getSingleMenuForm(Admin $admin,$validated)
	{
		$result = code(config('admin_code.GetReplaceError'));

		if(!count($validated))
        {
            throw new CommonException('ParamsIsNullError');
        }

		$permission = Permission::find($validated['id']);

		if(!$permission)
		{
			throw new CommonException('ThisDataNotExistsError');
		}

		$formData = new SingleMenuFormResource($permission);

        $data['data'] = $formData;

		$result = code(['code'=>0,'msg'=>'获取单个表单数据成功!'],$data);

		return $result;

		
	}


    /**
     * 添加菜单
     *
     * @param [type] $data
     * @return void
     */
    public function addMenu($validated,$admin)
    {
        $result = code(config('admin_code.AddMenuError'));

		$mapKey = $this->mapKey;

        $permission = new Permission;

        foreach ( $validated as $key => $value)
        {
			if($key === "params" || $key === "id")
            {
				continue;
			}
			else
			{
				if(\is_null($value) || empty($value))
				{
				// 如果是数组，保持为空数组而不是转换为字符串
					$value = is_array($value) ? [] : " ";
				}

				$permission->{$mapKey[$key]} = $value;
			}
           
        }

        $permission->created_time = time();
        $permission->created_at = time();

        $permissionResult = $permission->save();

        if(!$permissionResult)
        {
            throw new CommonException('AddMenuError');
        }

        CommonEvent::dispatch($admin,$validated,'AddMenu');

        //清空redis的缓存数据
        Redis::hdel('system:config','permission');
        Redis::hdel('system:config','develop_permission');

        $result = code(['code'=>0,'msg'=>'添加菜单成功!']);

        return $result;
    }

    /**
     * 更新菜单
     *
     * @param [type] $validated
     * @return void
     */
    public function updateMenu($validated,$admin)
    {
        $result = code(config('admin_code.UpdateMenuError'));

        $permission =  Permission::find($validated['id']);

        if(!optional($permission))
        {
            throw new CommonException('ThisDataNotExistsError');
        }

		$mapKey = $this->mapKey;

        //查看级别是否变化
        $type = 0;

        foreach ($validated as $key => $value)
        {
            if($key === "params" || $key === "id")
            {
				continue;
			}
			else
			{
				if($mapKey[$key] === 'deep' )
				{
					if($permission->{$mapKey[$key]} < $value)
					{
						//增加级别 实际是变成子级
						$type = 1;
					}

					if($permission->{$mapKey[$key]} > $value)
					{
						//减少级别 实际是提升级别
						$type = 2;
					}
				}

				if(\is_null($value) || empty($value))
				{
				// 如果是数组，保持为空数组而不是转换为字符串
					$value = is_array($value) ? [] : " ";
				}

				$permission->{$mapKey[$key]} = $value;
			}

        }

        $permission->updated_time = time();

        $permission->updated_at = time();

        $permissionResult = $permission->save();

        if(!$permissionResult)
        {
            throw new CommonException('UpdateMenuError');
        }

        $childrenData = $this->getAllChildren($validated['id']);

        $updateChildrenResult = 1;

        if($type == 1)
        {
            $updateChildrenResult = $this->updateChildrenDeep( $childrenData['data'],1);
        }

        if($type == 2)
        {
            $updateChildrenResult = $this->updateChildrenDeep( $childrenData['data'],0);
        }

        if(!$updateChildrenResult)
        {
            throw new CommonException('UpdateMenuDeepError');
        }

        $eventResult = CommonEvent::dispatch($admin,$validated,'UpdateMenu');

         //清空redis的缓存数据
        Redis::hdel('system:config','permission');
        Redis::hdel('system:config','develop_permission');

        $result = code(['code'=>0,'msg'=>'更新菜单成功!']);

        return $result;
    }

    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function moveMenu($validated,$admin)
    {
        $result = code(config('admin_code.MoveMenuError'));

        $permission = Permission::find($validated['id']);

		$routePath = $permission->route_path;
		
        if(!optional($permission))
        {
            throw new CommonException('ThisDataNotExistsError');
        }

        $permissionRevision = $permission->revision;

        $oldDeep = $permission->deep;

        $parentDeep = 1;

        if($validated['parent_id'])
        {
            $parentPermission = Permission::find($validated['parent_id']);

            $parentDeep = $parentPermission->deep + 1;
        }


        if(self::$dropType[$validated['dropType']] == 10)
        {
			//处理路由路径
		   $route_path = $this->processRoutePath($routePath,$parentDeep);

           $permissionUpdate = [
                'updated_time'=>time(),
                'updated_at'=>date('Y-m-d H:i:s',time()),
                'parent_id' => $validated['parent_id'],
                'deep' => $parentDeep,
				'route_path'=>$route_path,
                'revision'=>$permissionRevision  + 1
            ];
        }

        if(self::$dropType[$validated['dropType']] == 20)
        {
			//处理路由路径
		   $route_path = $this->processRoutePath($routePath,$parentDeep);
           $permissionUpdate = [
                'updated_time'=>time(),
                'updated_at'=>date('Y-m-d H:i:s',time()),
                'parent_id' => $validated['parent_id'],
                'sort'=> $validated['sort'],
                'deep'=> $parentDeep,
				'route_path'=>$route_path,
                'revision'=>$permissionRevision  + 1
            ];
        }

        if(self::$dropType[$validated['dropType']] == 30)
        {
			//处理路由路径
		   $route_path = $this->processRoutePath($routePath,$parentDeep);
           $permissionUpdate = [
                'updated_time'=>time(),
                'updated_at'=>date('Y-m-d H:i:s',time()),
                'parent_id' => $validated['parent_id'],
                'sort'=> $validated['sort'],
                'deep'=> $parentDeep,
				'route_path'=>$route_path,
                'revision'=>$permissionRevision  + 1
            ];
        }

       $permissionWhere = [['id','=',$validated['id']],['revision','=',$permissionRevision]];

        //更新配置项
       $permissionResult = Permission::where($permissionWhere)->update($permissionUpdate);

        if(!$permissionResult)
        {
            throw new CommonException('MoveMenuError');
        }

        //修改子级deep
        $deepNumber = $parentDeep - $oldDeep;

        $updateDeepResult = $this->updateChildrenDeep($permission->id,$deepNumber);

        if(!$updateDeepResult)
        {
            throw new CommonException('MoveMenuDeepError');
        }

        CommonEvent::dispatch($admin,$validated,'MoveMenu');

        //清空redis的缓存数据
        Redis::hdel('system:config','permission');
        Redis::hdel('system:config','develop_permission');

        $result = code(['code'=>0,'msg'=>'移动菜单成功!']);

        return $result;
    }

    /**
     * 删除菜单
     *
     * @param [type] $id
     * @return void
     */
    public function deleteMenu($validated,$admin)
    {
        $result = code(config('admin_code.DeleteMenuError'));

        //删除菜单之前要查看是否有子级菜单
        $permission = Permission::where('parent_id',$validated['id'])->get();

        $count = $permission->count();

        if($count)
        {
            throw new CommonException('ThisDataHasChildrenError');
        }

        if($count == 0)
        {
            $delPermission = Permission::find($validated['id']);

            if(!optional($permission))
            {
                throw new CommonException('ThisDataNotExistsError');
            }

            $delPermission->deleted_time = time();

            $delPermission->deleted_at = date('Y-m-d H:i:s',time());

            $delPermissionResult =  $delPermission->save();

            if(!$delPermissionResult)
            {
                throw new CommonException('DeleteMenuError');
            }

            CommonEvent::dispatch($admin,$validated,'DeleteMenu');

            //清空redis的缓存数据
            Redis::hdel('system:config','permission');
            Redis::hdel('system:config','develop_permission');

            $result = code(['code'=>0,'msg'=>'删除菜单成功!']);
        }

        return $result;
    }

    /**
     * 禁用或者开启
     *
     * @param [type] $id
     * @return void
     */
    public function switchMenu($validated,$admin)
    {
        $result = code(config('admin_code.DisableMenuError'));

        $eventName = 'DisableMenu';

        if($validated['switch'])
        {
            $result = code(config('admin_code.AbleMenuError'));

            $eventName = 'AbleMenu';
        }

        $where = [];
        $updateData = [];

        $permission = Permission::find($validated['id']);

        if(!optional($permission))
        {
            throw new CommonException('ThisDataNotExistsError');
        }

        //获取子级数据
        $childrenData = $this->getAllChildren($validated['id']);

        if(isset( $childrenData['idData']))
        {
             array_push($childrenData['idData'],$validated['id']);
        }

        $updateData = [
            'updated_time'=>time(),
            'updated_at'=>\date('Y-m-d H:i:s',time()),
            'switch'=>$validated['switch']
        ];

        $permissionResult = Permission::whereIn('id',$childrenData['idData'])->update($updateData);

        if(!$permissionResult)
        {
            if($validated['switch'])
            {
                throw new CommonException('AbleMenuError');
            }

            throw new CommonException('DisableMenuError');
        }

        CommonEvent::dispatch($admin,$validated,$eventName);

        //清空redis的缓存数据
        Redis::hdel('system:config','permission');
        Redis::hdel('system:config','develop_permission');

        $result = code(['code'=>0,'msg'=>'禁用菜单成功!']);

        if($validated['switch'])
        {
             $result = code(['code'=>0,'msg'=>'开启菜单成功!']);
        }

        return $result;
    }

	/**
 * 根据deep参数处理routePath
 * @param string $routePath 待处理的路径
 * @param int $deep 层级参数（1表示一级路径，>1表示多级路径）
 * @return string 处理后的路径
 */
	function processRoutePath($routePath, $deep) 
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
