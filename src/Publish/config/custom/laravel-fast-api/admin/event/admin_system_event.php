<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-05 10:26:56
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-28 15:47:13
 * @FilePath: \config\custom\laravel-fast-api\admin\event\admin_system_event.php
 */

return [
         //|--系统配置
    //|--|--平台设置
    //|--|--|--缓存设置
    //|--|--|--首页轮播
    'AddPhoneBanner' => [ 'code' => 10000, 'info' => '添加首页轮播图' ,'event'=>'AddPhoneBanner'],
    'UpdatePhoneBanner' => [ 'code' => 10000, 'info' => '修改首页轮播图','event'=>'UpdatePhoneBanner' ],
    'DeletePhoneBanner' => [ 'code' => 10000, 'info' => '删除首页轮播图','event'=>'DeletePhoneBanner' ],
    'RestorePhoneBanner' => [ 'code' => 10000, 'info' => '恢复首页轮播图','event'=>'RestorePhoneBanner' ],
    'MultipleDeletePhoneBanner' => [ 'code' => 10000, 'info' => '批量删除首页轮播图','event'=>'MultipleDeletePhoneBanner' ],
    //|--|--|--首页轮播--详情
    'UpdatePhoneBannerPicture' => [ 'code' =>10000, 'info' => '修改首页轮播图图片','event'=>'UpdatePhoneBannerPicture'],
    'UpdatePhoneBannerUrl' => [ 'code' => 10000, 'info' => '修改首页轮播图跳转' ,'event'=>'UpdatePhoneBannerUrl'],
    'UpdatePhoneBannerSort' => [ 'code' => 10000, 'info' => '修改首页轮播图排序','event'=>'UpdatePhoneBannerSort' ],
    'UpdatePhoneBannerRemarkInfo' => [ 'code' => 10000, 'info' => '修改首页轮播图备注','event'=>'UpdatePhoneBannerRemarkInfo' ],

    //|--|--系统设置
    //|--|--|--参数配置
    'AddSystemConfig' => [ 'code' => 10000, 'info' => '添加系统配置','event'=>'AddSystemConfig'],
    'UpdateSystemConfig' => [ 'code' => 10000, 'info' => '修改系统配置','event'=>'UpdateSystemConfig'],
    'DeleteSystemConfig' => [ 'code' => 10000, 'info' => '删除系统配置','event'=>'DeleteSystemConfig'],
    'MultipleDeleteSystemConfig' => [ 'code' => 10000, 'info' => '批量删除系统配置','event'=>'MultipleDeleteSystemConfig'],
    //|--|--|--提示配置
    'AddSystemVoiceConfig' => [ 'code' => 10000, 'info' => '添加系统提示音配置','event'=>'AddSystemVoiceConfig'],
    'UpdateSystemVoiceConfig' => [ 'code' => 10000, 'info' => '修改系统提示音配置','event'=>'UpdateSystemVoiceConfig'],
    'DeleteSystemVoiceConfig' => [ 'code' => 10000, 'info' => '删除系统提示音配置','event'=>'DeleteSystemVoiceConfig'],
    'MultipleDeleteSystemVoiceConfig' => [ 'code' => 10000, 'info' => '批量删除系统提示音配置','event'=>'MultipleDeleteSystemVoiceConfig'],
	//|--|--|--三方平台
	//|--|--|--|--微信平台
	'AddSystemWechatConfig' => [ 'code' => 10000, 'info' => '添加系统微信配置','event'=>'AddSystemWechatConfig'],
    'UpdateSystemWechatConfig' => [ 'code' => 10000, 'info' => '修改系统微信配置','event'=>'UpdateSystemWechatConfig'],
    'DeleteSystemWechatConfig' => [ 'code' => 10000, 'info' => '删除系统微信配置','event'=>'DeleteSystemWechatConfig'],
    'MultipleDeleteSystemWechatConfig' => [ 'code' => 10000, 'info' => '批量删除系统微信配置','event'=>'MultipleDeleteSystemWechatConfig'],
	//|--|--|--|--抖音平台
	'AddSystemDouyinConfig' => [ 'code' => 10000, 'info' => '添加系统微信配置','event'=>'AddSystemDouyinConfig'],
    'UpdateSystemDouyinConfig' => [ 'code' => 10000, 'info' => '修改系统微信配置','event'=>'UpdateSystemDouyinConfig'],
    'DeleteSystemDouyinConfig' => [ 'code' => 10000, 'info' => '删除系统微信配置','event'=>'DeleteSystemDouyinConfig'],
    'MultipleDeleteSystemDouyinConfig' => [ 'code' => 10000, 'info' => '批量删除系统微信配置','event'=>'MultipleDeleteSystemDouyinConfig'],
    //|--|--菜单管理
    'AddMenu'=>[ 'code' => 10000, 'info' => '添加菜单','event'=>'AddMenu'],
    'UpdateMenu'=>[ 'code' => 10000, 'info' => '更新菜单','event'=>'UpdateMenu' ],
    'MoveMenu'=>[ 'code' => 10000, 'info' => '移动菜单','event'=>'MoveMenu' ],
    'DeleteMenu'=>[ 'code' => 10000, 'info' => '删除菜单','event'=>'DeleteMenu' ],
    'DisableMenu'=>[ 'code' => 10000, 'info' => '关闭菜单','event'=>'DisableMenu' ],
    'AbleMenu'=>[ 'code' => 10000, 'info' => '开启菜单' ,'event'=>'AbleMenu'],
    //|--|--角色管理
    'AddRole' => [ 'code' =>10000, 'info' => '添加角色','event'=>'AddRole' ],
    'UpdateRole' => [ 'code' => 10000, 'info' => '修改角色','event'=>'UpdateRole' ],
    'MoveRole' => [ 'code' => 10000, 'info' => '移动角色','event'=>'MoveRole' ],
    'DeleteRole' => [ 'code' => 10000, 'info' => '删除角色','event'=>'DeleteRole' ],
    'ResetRolePermission' => [ 'code' => 10000, 'info' => '重新赋值角色权限','event'=>'ResetRolePermission' ],
	//|--|--通用配置
    'UpdateWithdrawConfig' => [ 'code' => 10000, 'info' => '更新提现配置','event'=>'UpdateWithdrawConfig' ],
    //|--|--分类管理
    //|--|--|--产品分类
    'AddGoodsClass' => [ 'code' => 10000, 'info' => '添加产品分类' ,'event'=>'AddGoodsClass'],
    'UpdateGoodsClass' => [ 'code' => 10000, 'info' => '修改产品分类','event'=>'UpdateGoodsClass' ],
    'MoveGoodsClass' => [ 'code' => 10000, 'info' => '移动产品分类','event'=>'MoveGoodsClass' ],
    'DeleteGoodsClass' => [ 'code' => 10000, 'info' => '删除产品分类','event'=>'DeleteGoodsClass' ],
    //|--|--|--文章分类
    'AddCategory' => [ 'code' => 10000, 'info' => '添加文章分类','event'=>'AddCategory' ],
    'UpdateCategory' => [ 'code' => 10000, 'info' => '修改文章分类','event'=>'UpdateCategory' ],
    'MoveCategory' => [ 'code' => 10000, 'info' => '移动文章分类','event'=>'MoveCategory' ],
    'DeleteCategory' => [ 'code' => 10000, 'info' => '删除文章分类','event'=>'DeleteCategory' ],
    //|--|--|--标签管理
    'AddLabel' => [ 'code' => 10000, 'info' => '添加标签','event'=>'AddLabel' ],
    'UpdateLabel' => [ 'code' => 10000, 'info' => '修改标签','event'=>'UpdateLabel' ],
    'MoveLabel' => [ 'code' => 10000, 'info' => '移动标签','event'=>'MoveLabel' ],
    'DeleteLabel' => [ 'code' => 10000, 'info' => '删除标签','event'=>'DeleteLabel' ],
    //|--|--级别管理
    //|--|--|--级别条件
    'AddLevelItem' => [ 'code' => 10000, 'info' => '添加级别配置项','event'=>'AddLevelItem' ],
    'UpdateLevelItem' => [ 'code' => 10000, 'info' => '修改级别配置项','event'=>'UpdateLevelItem' ],
    'DeleteLevelItem' => [ 'code' => 10000, 'info' => '删除级别配置项','event'=>'DeleteLevelItem' ],
    'MultipleDeleteLevelItem' => [ 'code' => 10000, 'info' => '批量删除级别配置项','event'=>'MultipleDeleteLevelItem' ],
    //|--|--|--用户级别
    'AddUserLevel' => [ 'code' => 10000, 'info' => '添加用户级别','event'=>'AddUserLevel' ],
    'UpdateUserLevel' => [ 'code' => 10000, 'info' => '修改用户级别','event'=>'UpdateUserLevel' ],
    'DeleteUserLevel' => [ 'code' => 10000, 'info' => '删除用户级别','event'=>'DeleteUserLevel' ],
    'MultipleDeleteUserLevel' => [ 'code' => 10000, 'info' => '批量删除用户级别','event'=>'MultipleDeleteUserLevel' ],
    'AddUserLevelItemUnion' => [ 'code' => 10000, 'info' => '添加用户级别配置项','event'=>'AddUserLevelItemUnion' ],
    'UpdateUserLevelItemUnion' => [ 'code' => 10000, 'info' => '修改用户级别配置项','event'=>'UpdateUserLevelItemUnion' ],
    'DeleteUserLevelItemUnion' => [ 'code' => 10000, 'info' => '删除用户级别配置项','event'=>'DeleteUserLevelItemUnion' ],

    //|--|--地区管理
    'AddRegion' => [ 'code' => 10000, 'info' => '添加地区','event'=>'AddRegion' ],
    'UpdateRegion' => [ 'code' => 10000, 'info' => '修改地区','event'=>'UpdateRegion' ],
    'MoveRegion' => [ 'code' => 10000, 'info' => '移动地区','event'=>'MoveRegion' ],
    'DeleteRegion' => [ 'code' => 10000, 'info' => '删除地区','event'=>'DeleteRegion' ],
    //|--|--银行管理
    'AddBank' => [ 'code' => 10000, 'info' => '添加银行','event'=>'AddBank' ],
    'UpdateBank' => [ 'code' => 10000, 'info' => '修改银行','event'=>'UpdateBank' ],
    'DeleteBank' => [ 'code' => 10000, 'info' => '删除银行' ,'event'=>'DeleteBank'],
    'MultipleDeleteBank' => [ 'code' => 10000, 'info' => '批量删除银行','event'=>'MultipleDeleteBank' ],
];
