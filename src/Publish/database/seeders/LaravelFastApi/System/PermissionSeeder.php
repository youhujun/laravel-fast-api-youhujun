<?php

/*
 * @Descripttion: 后台权限菜单 - 树形结构数据
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-22 14:35:41
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-06-12 16:52:08
 * @Fileroute_path: \database\seeders\LaravelFastApi\System\PermissionSeeder.php
 */

namespace Database\Seeders\LaravelFastApi\System;

use Illuminate\Database\Seeder;
use App\Attributes\Common\DocParams;
use App\Models\LaravelFastApi\V1\System\Permission\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionTreeArray = [
            //|-- 系统设置
            [
                'type' => 20, 'route_path' => '/config', 'component' => 'Layout', 'route_name' => 'config', 'permission_tag' => '', 'meta_title' => '系统设置', 'meta_icon' => 'el-icon-Setting', 'meta_affix' => 0, 'always_show' => 1, 'hidden' => 0, 'meta_no_cache' => 0,
                'children' => [
                    //|--|-- 菜单管理
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'permission', 'component' => 'system/permission/index', 'route_name' => 'Permission', 'permission_tag' => '', 'meta_title' => '菜单管理', 'meta_icon' => 'menu', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [],
                    ],
                    //|--|-- 平台配置
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'platformConfig', 'component' => 'platformConfig', 'route_name' => 'platformConfig', 'permission_tag' => '', 'meta_title' => '平台配置', 'meta_icon' => 'monitor', 'meta_affix' => 0, 'always_show' => 1, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [
                            //|--|--|-- 缓存设置
                            ['type' => 10, 'sort' => 100, 'route_path' => 'cacheConfig', 'component' => 'system/platform/cacheConfig/index', 'route_name' => 'cacheConfig', 'permission_tag' => '', 'meta_title' => '缓存设置', 'meta_icon' => 'el-icon-Box', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                        ],
                    ],
                    //|--|-- 系统配置
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'system', 'component' => 'system', 'route_name' => 'system', 'permission_tag' => '', 'meta_title' => '系统配置', 'meta_icon' => 'system', 'meta_affix' => 0, 'always_show' => 1, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [
                            //|--|--|-- 配置参数
                            ['type' => 10, 'sort' => 100, 'route_path' => 'systemConfig', 'component' => 'system/system/systemConfig/index', 'route_name' => 'systemConfig', 'permission_tag' => '', 'meta_title' => '配置参数', 'meta_icon' => 'cascader', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                            //|--|--|-- 提示配置
                            ['type' => 10, 'sort' => 100, 'route_path' => 'voiceConfig', 'component' => 'system/system/voiceConfig/index', 'route_name' => 'voiceConfig', 'permission_tag' => '', 'meta_title' => '提示配置', 'meta_icon' => 'el-icon-bell', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                            //|--|--|-- 三方平台
                            ['type' => 10, 'sort' => 100, 'route_path' => 'otherPaltform', 'component' => 'system/system/otherPlatform/index', 'route_name' => 'otherPaltform', 'permission_tag' => '', 'meta_title' => '三方平台', 'meta_icon' => 'api', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                            //|--|--|-- 微信平台
                            ['type' => 10, 'sort' => 100, 'route_path' => 'wechatPlatform', 'component' => 'system/system/otherPlatform/wechat/index', 'route_name' => 'wechatPlatform', 'permission_tag' => '', 'meta_title' => '微信平台', 'meta_icon' => 'wechat', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 1, 'meta_no_cache' => 0],
                            //|--|--|-- 抖音平台
                            ['type' => 10, 'sort' => 100, 'route_path' => 'douyinPlatform', 'component' => 'system/system/otherPlatform/douyin/index', 'route_name' => 'douyinPlatform', 'permission_tag' => '', 'meta_title' => '抖音平台', 'meta_icon' => 'el-icon-Headset', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 1, 'meta_no_cache' => 0],
                        ],
                    ],
                    //|--|-- 角色管理
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'role', 'component' => 'system/role/index', 'route_name' => 'Role', 'permission_tag' => '', 'meta_title' => '角色管理', 'meta_icon' => 'role', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [],
                    ],
                    //|--|-- 地区管理
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'region', 'component' => 'system/region/index', 'route_name' => 'region', 'permission_tag' => '', 'meta_title' => '地区管理', 'meta_icon' => 'el-icon-location', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [],
                    ],
                    //|--|-- 银行管理
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'bank', 'component' => 'system/bank/index', 'route_name' => 'bank', 'permission_tag' => '', 'meta_title' => '银行管理', 'meta_icon' => 'el-icon-Postcard', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [],
                    ],
                ],
            ],

            //|-- 业务设置
            [
                'type' => 20, 'route_path' => '/businesses', 'component' => 'Layout', 'route_name' => 'businesses', 'permission_tag' => '', 'meta_title' => '业务设置', 'meta_icon' => 'client', 'meta_affix' => 0, 'always_show' => 1, 'hidden' => 0, 'meta_no_cache' => 0,
                'children' => [
                    //|--|-- 通用设置
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'common', 'component' => 'common', 'route_name' => 'common', 'permission_tag' => '', 'meta_title' => '通用设置', 'meta_icon' => 'el-icon-setting', 'meta_affix' => 0, 'always_show' => 1, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [
                            //|--|--|-- 集合设置
                            ['type' => 10, 'sort' => 100, 'route_path' => 'gather', 'component' => 'business/common/collection/index', 'route_name' => 'gather', 'permission_tag' => '', 'meta_title' => '集合设置', 'meta_icon' => 'el-icon-BrushFilled', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                        ],
                    ],
                    //|--|-- 分类管理
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'group', 'component' => 'group', 'route_name' => 'group', 'permission_tag' => '', 'meta_title' => '分类管理', 'meta_icon' => 'tree', 'meta_affix' => 0, 'always_show' => 1, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [
                            //|--|--|-- 产品分类
                            ['type' => 10, 'sort' => 100, 'route_path' => 'goodsClass', 'component' => 'business/group/goodsClass/index', 'route_name' => 'goodsClass', 'permission_tag' => '', 'meta_title' => '产品分类', 'meta_icon' => 'el-icon-present', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                            //|--|--|-- 文章分类
                            ['type' => 10, 'sort' => 100, 'route_path' => 'category', 'component' => 'business/group/category/index', 'route_name' => 'category', 'permission_tag' => '', 'meta_title' => '文章分类', 'meta_icon' => 'dict', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                            //|--|--|-- 标签管理
                            ['type' => 10, 'sort' => 100, 'route_path' => 'label', 'component' => 'business/group/label/index', 'route_name' => 'label', 'permission_tag' => '', 'meta_title' => '标签管理', 'meta_icon' => 'el-icon-CollectionTag', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                        ],
                    ],
                    //|--|-- 级别管理
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'level', 'component' => 'level', 'route_name' => 'level', 'permission_tag' => '', 'meta_title' => '级别管理', 'meta_icon' => 'el-icon-Flag', 'meta_affix' => 0, 'always_show' => 1, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [
                            //|--|--|-- 级别条件
                            ['type' => 10, 'sort' => 100, 'route_path' => 'levelItem', 'component' => 'business/level/levelItem/index', 'route_name' => 'levelItem', 'permission_tag' => '', 'meta_title' => '级别条件', 'meta_icon' => 'size', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                            //|--|--|-- 用户级别
                            ['type' => 10, 'sort' => 100, 'route_path' => 'userLevel', 'component' => 'business/level/userLevel/index', 'route_name' => 'userLevel', 'permission_tag' => '', 'meta_title' => '用户级别', 'meta_icon' => 'el-icon-UserFilled', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                        ],
                    ],
                    //|--|-- 业务平台
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'servicePlatform', 'component' => 'servicePlatform', 'route_name' => 'servicePlatform', 'permission_tag' => '', 'meta_title' => '业务平台', 'meta_icon' => 'el-icon-Monitor', 'meta_affix' => 0, 'always_show' => 1, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [
                            //|--|--|-- 首页轮播
                            ['type' => 10, 'sort' => 100, 'route_path' => 'phoneIndexBanner', 'component' => 'business/platform/banner/index', 'route_name' => 'phoneIndexBanner', 'permission_tag' => '', 'meta_title' => '首页轮播', 'meta_icon' => 'el-icon-Star', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                        ],
                    ],
                ],
            ],

            //|-- 用户管理
            [
                'type' => 20, 'route_path' => '/users', 'component' => 'Layout', 'route_name' => 'users', 'permission_tag' => '', 'meta_title' => '用户管理', 'meta_icon' => 'el-icon-UserFilled', 'meta_affix' => 0, 'always_show' => 1, 'hidden' => 0, 'meta_no_cache' => 0,
                'children' => [
                    //|--|-- 管理员管理
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'admin', 'component' => 'user/admin/adminList/index', 'route_name' => 'userAdmin', 'permission_tag' => '', 'meta_title' => '管理员管理', 'meta_icon' => 'el-icon-Avatar', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [],
                    ],
                    //|--|-- 用户管理
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'user', 'component' => 'user', 'route_name' => 'user', 'permission_tag' => '', 'meta_title' => '用户管理', 'meta_icon' => 'el-icon-user', 'meta_affix' => 0, 'always_show' => 1, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [
                            //|--|--|-- 用户列表
                            ['type' => 10, 'sort' => 100, 'route_path' => 'userList', 'component' => 'user/user/userList/index', 'route_name' => 'userList', 'permission_tag' => '', 'meta_title' => '用户列表', 'meta_icon' => 'table', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                            //|--|--|-- 等待认证
                            ['type' => 10, 'sort' => 100, 'route_path' => 'userCheck', 'component' => 'user/user/userAuth/index', 'route_name' => 'userCheck', 'permission_tag' => '', 'meta_title' => '等待认证', 'meta_icon' => 'captcha', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                            //|--|--|-- 编辑用户
                            ['type' => 10, 'sort' => 100, 'route_path' => 'editUser', 'component' => 'user/user/editUser/index', 'route_name' => 'editUser', 'permission_tag' => '', 'meta_title' => '编辑用户', 'meta_icon' => 'edit', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 1, 'meta_no_cache' => 1],
                            //|--|--|-- 用户地址
                            ['type' => 10, 'sort' => 100, 'route_path' => 'userAddress', 'component' => 'user/user/userAddress/index', 'route_name' => 'userAddress', 'permission_tag' => '', 'meta_title' => '用户地址', 'meta_icon' => 'edit', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 1, 'meta_no_cache' => 1],
                            //|--|--|-- 用户银行
                            ['type' => 10, 'sort' => 100, 'route_path' => 'userBank', 'component' => 'user/user/userBank/index', 'route_name' => 'userBank', 'permission_tag' => '', 'meta_title' => '用户银行', 'meta_icon' => 'edit', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 1, 'meta_no_cache' => 1],
                            //|--|--|-- 用户身份证
                            ['type' => 10, 'sort' => 100, 'route_path' => 'userIdCard', 'component' => 'user/user/userIdCard/index', 'route_name' => 'userIdCard', 'permission_tag' => '', 'meta_title' => '用户身份证', 'meta_icon' => 'edit', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 1, 'meta_no_cache' => 1],
                            //|--|--|-- 用户团队
                            ['type' => 10, 'sort' => 100, 'route_path' => 'userTeam', 'component' => 'user/user/userTeam/index', 'route_name' => 'userTeam', 'permission_tag' => '', 'meta_title' => '用户团队', 'meta_icon' => 'edit', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 1, 'meta_no_cache' => 1],
                            //|--|--|-- 账户日志
                            ['type' => 10, 'sort' => 100, 'route_path' => 'userAccountLog', 'component' => 'user/user/userAccountLog/index', 'route_name' => 'userAccountLog', 'permission_tag' => '', 'meta_title' => '账户日志', 'meta_icon' => 'edit', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 1, 'meta_no_cache' => 1],
                            //|--|--|-- 用户订单
                            ['type' => 10, 'sort' => 100, 'route_path' => 'userOrder', 'component' => 'user/user/userOrder/index', 'route_name' => 'userOrder', 'permission_tag' => '', 'meta_title' => '用户订单', 'meta_icon' => 'edit', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 1, 'meta_no_cache' => 1],
                            //|--|--|-- 用户文章
                            ['type' => 10, 'sort' => 100, 'route_path' => 'userArticle', 'component' => 'user/user/userArticle/index', 'route_name' => 'userArticle', 'permission_tag' => '', 'meta_title' => '用户文章', 'meta_icon' => 'edit', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 1, 'meta_no_cache' => 1],
                        ],
                    ],
                ],
            ],

            //|-- 文章管理
            [
                'type' => 20, 'route_path' => '/article', 'component' => 'Layout', 'route_name' => 'article', 'permission_tag' => '', 'meta_title' => '文章管理', 'meta_icon' => 'document', 'meta_affix' => 0, 'always_show' => 1, 'hidden' => 0, 'meta_no_cache' => 0,
                'children' => [
                    //|--|-- 公告管理
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'notice', 'component' => 'article/notice/index', 'route_name' => 'notice', 'permission_tag' => '', 'meta_title' => '公告管理', 'meta_icon' => 'el-icon-postcard', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [],
                    ],
                    //|--|-- 系统文章
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'systemArticle', 'component' => 'article/system/index', 'route_name' => 'systemArticle', 'permission_tag' => '', 'meta_title' => '系统文章', 'meta_icon' => 'el-icon-document-copy', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [],
                    ],
                ],
            ],

            //|-- 图片空间
            [
                'type' => 20, 'route_path' => '/picture', 'component' => 'Layout', 'route_name' => 'picture', 'permission_tag' => '', 'meta_title' => '图片空间', 'meta_icon' => 'el-icon-picture', 'meta_affix' => 0, 'always_show' => 1, 'hidden' => 0, 'meta_no_cache' => 0,
                'children' => [
                    //|--|-- 我的相册
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'album', 'component' => 'picture/album/albumList/index', 'route_name' => 'album', 'permission_tag' => '', 'meta_title' => '我的相册', 'meta_icon' => 'el-icon-collection', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [],
                    ],
                ],
            ],

            //|-- 日志管理
            [
                'type' => 20, 'route_path' => '/log', 'component' => 'Layout', 'route_name' => 'log', 'permission_tag' => '', 'meta_title' => '日志管理', 'meta_icon' => 'el-icon-Edit', 'meta_affix' => 0, 'always_show' => 1, 'hidden' => 0, 'meta_no_cache' => 0,
                'children' => [
                    //|--|-- 登录日志
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'loginLog', 'component' => 'loginLog', 'route_name' => 'LoginLog', 'permission_tag' => '', 'meta_title' => '登录日志', 'meta_icon' => 'el-icon-ElementPlus', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [
                            //|--|--|-- 手机登录
                            ['type' => 10, 'sort' => 100, 'route_path' => 'phoneLogin', 'component' => 'log/login/phoneLogin/index', 'route_name' => 'phoneLogin', 'permission_tag' => '', 'meta_title' => '手机登录', 'meta_icon' => 'el-icon-phone', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                            //|--|--|-- 后台登录
                            ['type' => 10, 'sort' => 100, 'route_path' => 'adminLogin', 'component' => 'log/login/adminLogin/index', 'route_name' => 'adminLogin', 'permission_tag' => '', 'meta_title' => '后台登录', 'meta_icon' => 'el-icon-Paperclip', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                        ],
                    ],
                    //|--|-- 事件日志
                    [
                        'type' => 10, 'sort' => 100, 'route_path' => 'eventLog', 'component' => 'eventLog', 'route_name' => 'eventLog', 'permission_tag' => '', 'meta_title' => '事件日志', 'meta_icon' => 'el-icon-view', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0,
                        'children' => [
                            //|--|--|-- 手机事件
                            ['type' => 10, 'sort' => 100, 'route_path' => 'phoneEvent', 'component' => 'log/event/phoneEvent/index', 'route_name' => 'phoneEvent', 'permission_tag' => '', 'meta_title' => '手机事件', 'meta_icon' => 'el-icon-phone', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                            //|--|--|-- 后台事件
                            ['type' => 10, 'sort' => 100, 'route_path' => 'adminEvent', 'component' => 'log/event/adminEvent/index', 'route_name' => 'adminEvent', 'permission_tag' => '', 'meta_title' => '后台事件', 'meta_icon' => 'el-icon-Paperclip', 'meta_affix' => 0, 'always_show' => 0, 'hidden' => 0, 'meta_no_cache' => 0],
                        ],
                    ],
                ],
            ],
        ];

       
		Permission::truncate();

		$this->command->info('开始填充系统权限菜单');

		$this->insertTree($permissionTreeArray, 0, 1);


		$this->command->info('✅填充系统权限菜单完成');

    }

	#[DocParams(note:'递归插入权限菜单树',params:['nodes'=>'当前层级节点数组','parentId'=>'父级ID','deep'=>'当前深度'])]
    protected function insertTree(array $nodes, int $parentId, int $deep)
    {
        foreach ($nodes as $node) {
            // 插入当前节点
            $item = Permission::create([
                'type' => $node['type'],
                'route_path' => $node['route_path'],
                'component' => $node['component'],
                'route_name' => $node['route_name'],
                'permission_tag' => $node['permission_tag'],
                'meta_title' => $node['meta_title'],
                'meta_icon' => $node['meta_icon'],
                'meta_affix' => $node['meta_affix'],
                'always_show' => $node['always_show'],
                'hidden' => $node['hidden'],
                'meta_no_cache' => $node['meta_no_cache'],
                'sort' => isset($node['sort'])?$node['sort']:100,
                'parent_id' => $parentId,
                'deep' => $deep,
				'switch' => 1,
                'created_time' => time(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // 递归插入子节点，父ID为刚插入的主键
            if (!empty($node['children'])) {
                $this->insertTree($node['children'], $item->id, $deep + 1);
            }
        }
    }
}
