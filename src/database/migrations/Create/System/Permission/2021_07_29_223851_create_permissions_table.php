<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-02 09:29:26
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-17 11:48:46
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $db_connection = config('youhujun.db_connection');

        if (!Schema::connection($db_connection)->hasTable('permissions')) {
            Schema::connection($db_connection)->create('permissions', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->unsignedBigInteger('parent_id')->notNull()->default(0)->comment('父级id');
                $table->unsignedTinyInteger('deep')->notNull()->default(0)->comment('菜单等级');
                $table->unsignedTinyInteger('type')->notNull()->default(10)->comment('菜单类型 10菜单MENU,20目录CATALOG,30外链EXTLINK,40按钮BUTTON');
                $table->string('route_name', 64)->notNull()->default('')->comment('前端路径逻辑名称');
                $table->string('route_path', 64)->notNull()->default('')->comment('前端路径');
                $table->string('component', 128)->notNull()->default('')->comment('实际组件位置,跟前端做映射');
                $table->unsignedTinyInteger('hidden')->notNull()->default(0)->comment('是否隐藏,默认false');
                $table->unsignedTinyInteger('always_show')->notNull()->default(0)->comment('是否总是显示,默认false如果设置为true,将始终显示根菜单,如果没有设置alwaysShow,当item有多个子路由时,它将成为嵌套模式,否则不显示根菜单');
                $table->string('redirect', 64)->notNull()->default('')->comment('noRedirect或者跳转路径,注意如果设置不跳转将不会跳转');
                $table->string('permission_tag', 128)->notNull()->default('')->comment('权限标识(示例:sys:permission:add)系统菜单添加');
                $table->string('meta_title', 64)->notNull()->default('')->comment('权限显示名称');
                $table->string('meta_icon', 32)->notNull()->default('')->comment('图标');
                $table->unsignedTinyInteger('meta_no_cache')->notNull()->default(0)->comment('是否缓存,默认false');
                $table->unsignedTinyInteger('meta_affix')->notNull()->default(0)->comment('是否固定(在视图中),默认false');
                $table->unsignedTinyInteger('meta_breadcrumb')->notNull()->default(1)->comment('是否在面包屑中显示,默认true');
                $table->string('meta_active_menu', 128)->notNull()->default('')->comment('活动路由,如果设置路径，侧边栏将突出显示您设置的路径');
                $table->unsignedTinyInteger('switch')->notNull()->default(0)->comment('是否启用,默认0禁用,1启用');
                $table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');



                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');


                // 索引
                $table->index('parent_id');
                $table->index('deep');
                $table->index('type');
                $table->index('switch');
                $table->index('sort');
                $table->index('route_name');
                $table->index('route_path');
                $table->index('permission_tag');
                $table->index('created_time');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}permissions` comment '权限菜单指令表'");
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

        if (Schema::connection($db_connection)->hasTable('permissions')) {
            Schema::connection($db_connection)->dropIfExists('permissions');
        }
    }
};
