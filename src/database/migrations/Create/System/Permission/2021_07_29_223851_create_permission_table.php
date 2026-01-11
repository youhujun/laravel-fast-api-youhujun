<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-02 09:29:26
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-05 13:39:43
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		$db_connection = config('youhujun.db_connection');

		if (!Schema::connection($db_connection)->hasTable('permission')) 
		{
			Schema::connection($db_connection)->create('permission', function (Blueprint $table) {

				$table->id()->comment('主键');
				$table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
				$table->unsignedBigInteger('parent_id')->index()->default(0)->comment('父级id');
				$table->unsignedTinyInteger('deep')->index()->default(0)->comment('菜单等级');
				$table->unsignedTinyInteger('type')->index()->default(10)->comment('菜单类型 10菜单MENU,20目录CATALOG,30外链EXTLINK,40按钮BUTTON');
				$table->string('route_name',64)->default('')->comment('前端路径逻辑名称');
				$table->string('route_path',64)->default('')->comment('前端路径');
				$table->string('component',128)->default('')->comment('实际组件位置,跟前端做映射');
				$table->unsignedTinyInteger('hidden')->default(0)->comment('是否隐藏,默认false');
				$table->unsignedTinyInteger('always_show')->default(0)->comment('是否总是显示,默认false如果设置为true,将始终显示根菜单,如果没有设置alwaysShow,当item有多个子路由时,它将成为嵌套模式,否则不显示根菜单');
				$table->string('redirect',64)->default('')->comment('noRedirect或者跳转路径,注意如果设置不跳转将不会跳转');
				$table->string('permission_tag',128)->default('')->comment('权限标识(示例:sys:permission:add)系统菜单添加');
				$table->string('meta_title',64)->default('')->comment('权限显示名称');
				$table->string('meta_icon',32)->default('')->comment('图标');
				$table->unsignedTinyInteger('meta_no_cache')->default(0)->comment('是否缓存,默认false');
				$table->unsignedTinyInteger('meta_affix')->default(0)->comment('是否固定(在视图中),默认false');
				$table->unsignedTinyInteger('meta_breadcrumb')->default(1)->comment('是否在面包屑中显示,默认true');
				$table->string('meta_active_menu',128)->default('')->comment('活动路由,如果设置路径，侧边栏将突出显示您设置的路径');
				$table->unsignedTinyInteger('switch')->index()->default(0)->comment('是否启用,默认0禁用,1启用');
	
				$table->dateTime('created_at')->useCurrent()->comment('创建时间string');
				$table->unsignedInteger('created_time')->index(0)->default(0)->comment('创建时间int');
				$table->dateTime('updated_at')->nullable()->comment('更新时间string');
				$table->unsignedInteger('updated_time')->default(0)->comment('更新时间int');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间string');
				$table->unsignedInteger('deleted_time')->default(0)->comment('删除时间int');
				$table->unsignedTinyInteger('sort')->index()->default(100)->comment('排序');
	
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}permission` comment '权限菜单指令表'");
		}
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		$db_connection = config('youhujun.db_connection');

		if (Schema::connection($db_connection)->hasTable('permission')) 
		{
			Schema::connection($db_connection)->dropIfExists('permission');
		}
        
    }


};
