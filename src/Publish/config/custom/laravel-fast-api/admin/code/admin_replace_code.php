<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-21 12:43:14
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-08-21 13:05:48
 * @FilePath: \config\custom\admin\code\admin_replace_code.php
 */

$replaceCodeArray = [
	//选项
	//默认替换选项
	'DefaultReplaceError'=>['code'=>10000,'msg'=>'获取默认替换选项成功!','error'=>'DefaultReplaceError'],
	 //查找替换选项
	'FindReplaceError'=>['code'=>10000,'msg'=>'获取默认替换选项成功!','error'=>'FindReplaceError'],

	//获取替换列表
	'GetReplaceError'=>['code'=>10000,'msg'=>'获取默认替换列表成功!','error'=>'GetReplaceError'],
	'AddReplaceError'=>['code'=>10000,'msg'=>'添加替换成功!','error'=>'AddReplaceError'],
	'UpdateReplaceError'=>['code'=>10000,'msg'=>'修改替换成功!','error'=>'UpdateReplaceError'],
	'UpdateReplacePropertyError'=>['code'=>10000,'msg'=>'获取修改替换属性成功!','error'=>'UpdateReplacePropertyError'],
	'DeleteReplaceError'=>['code'=>10000,'msg'=>'删除替换成功!','error'=>'DeleteReplaceError'],
	'RestoreReplaceError'=>['code'=>10000,'msg'=>'恢复替换成功!','error'=>'RestoreReplaceError'],
	'MultipleDeleteReplaceError'=>['code'=>10000,'msg'=>'批量删除替换成功!','error'=>'DeleteReplaceError'],
	'MultipleRestoreReplaceError'=>['code'=>10000,'msg'=>'批量恢复替换成功!','error'=>'MultipleRestoreReplaceError'],
];

return $replaceCodeArray;