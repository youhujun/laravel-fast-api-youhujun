<?php

use Illuminate\Support\Facades\Redis;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;
use App\Models\LaravelFastApi\V1\System\Role\Role;

if (!function_exists('getAdminRoles')) {
    /**
     * 获取管理员角色列表
     *
     * 先从Redis缓存中获取管理员角色，如果不存在则从数据库查询并缓存到Redis
     *
     * @param Admin $admin 管理员对象
     * @return array 返回管理员角色逻辑名称数组
     */
    function getAdminRoles(Admin $admin)
    {
        // 1. 统一变量名（带e），消除拼写差异
        $rolesArray = [];
        // 2. 简化冗余Redis键，提高可读性
        $redisKey = "admin_roles";
        // 3. 先判断admin->user_uid是否为空，避免无效查询
        if (empty($admin->user_uid) || empty($admin->biz_id)) {
            return $rolesArray;
        }

        // 获取Redis缓存
        $redisRoles = Redis::hget($redisKey, $admin->biz_id);
        if ($redisRoles) {
            // 4. 处理json_decode失败场景，确保返回数组类型
            $rolesArray = json_decode($redisRoles, true);
            if (!is_array($rolesArray)) {
                $rolesArray = [];
            }
        } else {
            // 查询管理员与角色的关联关系

            $adminRoleUnionCollection = UserRoleUnion::query()
                ->forUserUid($admin->user_uid) // 模型专属宏，底层绑定分片
                ->where('user_uid', $admin->user_uid)
                ->get();


            $roleIdArray = [];
            // 5. 移除不必要的引用传递，避免变量污染
            foreach ($adminRoleUnionCollection as $key => $adminRoleUnionItem) {
                array_push($roleIdArray, $adminRoleUnionItem->role_id);
            }

            // 6. 正确使用array_unique，接收返回值实现去重
            $roleIdArray = array_unique($roleIdArray);

            // 7. 空值判断，避免生成无效SQL（where in ()）
            if (!empty($roleIdArray)) {
                // 获取角色集合
                $roleCollection = Role::whereIn('id', $roleIdArray)->get();

                $roleLogicNameArray = [];
                foreach ($roleCollection as $key => $roleItem) {
                    array_push($roleLogicNameArray, $roleItem->logic_name);
                }

                $rolesArray = $roleLogicNameArray;

                // 缓存到Redis（仅当角色数组非空时）
                if (!empty($rolesArray)) {
                    $result = Redis::hset($redisKey, $admin->biz_id, json_encode($rolesArray));
                    // 8. 修正Redis缓存失败判断：仅当返回false时才是真正失败
                    if ($result === false) {
                        plog(['RedisSaveAdminRolesError' => '缓存管理员角色失败'], 'AdminSystem', 'RedisSaveAdminRolesError');
                    }
                }
            }
        }

        return $rolesArray;
    }
}

if (!function_exists('getUserRoles')) {
    /**
     * 获取管理员角色列表
     *
     * 先从Redis缓存中获取管理员角色，如果不存在则从数据库查询并缓存到Redis
     *
     * @param User $user 用户对象（修正注释与实际参数一致）
     * @return array 返回管理员角色逻辑名称数组
     */
    function getUserRoles(User $user)
    {
        // 统一变量名：roles（带e），避免拼写错误
        $rolesArray = [];
        $redisKey = "user_roles"; // 简化冗余的Redis键
        $redisRoles = Redis::hget($redisKey, $user->id);

        if ($redisRoles) {
            // 处理json_decode失败的情况
            $rolesArray = json_decode($redisRoles, true);
            // 若解析失败，重置为空数组
            if (!is_array($rolesArray)) {
                $rolesArray = [];
            }
        } else {
            // 查询管理员的用户角色关联
            $userRoleUnionCollection = UserRoleUnion::where('user_id', $user->id)->get();

            $roleIdArray = [];
            foreach ($userRoleUnionCollection as $key => $userRoleUnionItem) {
                // 移除不必要的引用传递，避免变量污染
                array_push($roleIdArray, $userRoleUnionItem->role_id);
            }

            // 正确使用array_unique：接收返回值实现去重
            $roleIdArray = array_unique($roleIdArray);
            // 空值判断：避免生成无效SQL
            if (!empty($roleIdArray)) {
                // 获取角色集合
                $roleCollection = Role::whereIn('id', $roleIdArray)->get();

                $roleLogicNameArray = [];
                foreach ($roleCollection as $key => $roleItem) {
                    array_push($roleLogicNameArray, $roleItem->logic_name);
                }

                $rolesArray = $roleLogicNameArray;

                // 缓存到Redis（仅当角色数组非空时）
                if (!empty($rolesArray)) {
                    $result = Redis::hset($redisKey, $user->id, json_encode($rolesArray));
                    // 修正Redis缓存结果判断：result为false时才是真正失败（hset返回0/1均为成功）
                    if ($result === false) {
                        plog(['RedisSaveAdminRolesError' => '缓存用户角色失败'], 'UserSystem', 'RedisSaveUserRolesError');
                    }
                }
            }
        }

        return $rolesArray;
    }
}

if (!function_exists('isDevelop')) {
    /**
     * 是否是开发者
     *
     * @return boolean
     */
    function isDevelop(Admin $admin)
    {
        $rolesArray = getAdminRoles($admin);

        $result = 0;

        $result = \in_array('develop', $rolesArray);

        return $result;
    }
}

if (!function_exists('isSuper')) {
    /**
     * 是否是超级管理员
     *
     * @return boolean
     */
    function isSuper(Admin $admin)
    {
        $rolesArray = getAdminRoles($admin);

        $result = 0;
        $developResult = \in_array('develop', $rolesArray);
        $superResult = \in_array('super', $rolesArray);

        if ($developResult || $superResult) {
            $result = 1;
        }

        return $result;
    }
}


if (!function_exists('isAdmin')) {
    /**
     * 是否是管理员
     *
     * @return boolean
     */
    function isAdmin(Admin $admin)
    {
        $rolesArray = getAdminRoles($admin);

        $result = 0;

        $developResult = \in_array('develop', $rolesArray);
        $superResult = \in_array('super', $rolesArray);
        $adminResult = \in_array('admin', $rolesArray);

        if ($developResult || $superResult || $adminResult) {
            $result = 1;
        }

        return $result;
    }
}

if (!function_exists('isAConfigAdmin')) {
    /**
     * 是否是配置管理员
     *
     * @return boolean
     */
    function isAConfigAdmin(Admin $admin)
    {
        $rolesArray = getAdminRoles($admin);

        $result = 0;

        $developResult = \in_array('develop', $rolesArray);
        $superResult = \in_array('super', $rolesArray);
        $adminResult = \in_array('config_admin', $rolesArray);

        if ($developResult || $superResult || $adminResult) {
            $result = 1;
        }

        return $result;
    }
}

if (!function_exists('isAlbumAdmin')) {
    /**
     * 是否是相册管理员
     *
     * @return boolean
     */
    function isAlbumAdmin(Admin $admin)
    {
        $rolesArray = getAdminRoles($admin);

        $result = 0;

        $developResult = \in_array('develop', $rolesArray);
        $superResult = \in_array('super', $rolesArray);
        $adminResult = \in_array('album_admin', $rolesArray);

        if ($developResult || $superResult || $adminResult) {
            $result = 1;
        }

        return $result;
    }
}

if (!function_exists('isOrderAdmin')) {
    /**
     * 是否是相册管理员
     *
     * @return boolean
     */
    function isOrderAdmin(Admin $admin)
    {
        $rolesArray = getAdminRoles($admin);

        $result = 0;

        $developResult = \in_array('develop', $rolesArray);
        $superResult = \in_array('super', $rolesArray);
        $adminResult = \in_array('order_admin', $rolesArray);

        if ($developResult || $superResult || $adminResult) {
            $result = 1;
        }

        return $result;
    }
}

if (!function_exists('isArticleAdmin')) {
    /**
     * 是否是文章管理员
     *
     * @return boolean
     */
    function isArticleAdmin(Admin $admin)
    {
        $rolesArray = getAdminRoles($admin);

        $result = 0;

        $developResult = \in_array('develop', $rolesArray);
        $superResult = \in_array('super', $rolesArray);
        $adminResult = \in_array('article_admin', $rolesArray);

        if ($developResult || $superResult || $adminResult) {
            $result = 1;
        }

        return $result;
    }
}


if (!function_exists('isUser')) {
    /**
     * 是否是用户
     *
     * @return boolean
     */
    function isUser(Admin $admin)
    {
        $rolesArray = getAdminRoles($admin);

        $result = 0;

        $developResult = \in_array('develop', $rolesArray);
        $superResult = \in_array('super', $rolesArray);
        $adminResult = \in_array('admin', $rolesArray);
        $userResult = \in_array('user', $rolesArray);

        if ($developResult || $superResult || $adminResult || $userResult) {
            $result = 1;
        }

        return $result;
    }
}

if (!function_exists('isPhoneUser')) {
    /**
     * 是否是用户
     *
     * @return boolean
     */
    function isPhoneUser(User $user)
    {
        $rolesArray = getUserRoles($user);

        //p($rolesArray);

        $result = 0;

        $userResult = \in_array('user', $rolesArray);

        if ($userResult) {
            $result = 1;
        }

        return $result;
    }
}
