<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-10-18 09:00:29
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-07 11:40:33
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

		if (!Schema::connection($db_connection)->hasTable('goods_classes'))
		{
			Schema::connection($db_connection)->create('goods_classes', function (Blueprint $table) {

				$table->id()->comment('主键');
				$table->unsignedInteger('parent_id')->notNull()->default(0)->comment('父级id');
				$table->unsignedTinyInteger('deep')->notNull()->default(0)->comment('级别');
				$table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
				$table->unsignedTinyInteger('switch')->notNull()->default(0)->comment('是否|开关 0关否1是开');
				$table->decimal('rate',4,2)->notNull()->default(0)->comment('分润比例%');
				$table->string('goods_class_name',64)->notNull()->default('')->comment('商品类名称');
				$table->string('goods_class_code',64)->nullable()->comment('商品分类逻辑名称');
				$table->unsignedBigInteger('goods_class_picture_id')->notNull()->default(0)->comment('分类图片id(相册图片id)');
				$table->unsignedTinyInteger('is_certificate')->notNull()->default(0)->comment('是否需要要资质证书 0否1是');
				$table->unsignedTinyInteger('certificate_number')->notNull()->default(0)->comment('主要资质证书数量');
				$table->string('note',255)->nullable()->comment('备注说明');
				$table->unsignedTinyInteger('sort')->notNull()->default(0)->comment('排序');

				// 索引
				$table->index('goods_class_code');

				// 时间字段（自动填充+索引，关键优化）
				$table->dateTime('created_at')->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

			});

			$prefix = config('database.connections.'.$db_connection.'.prefix');

			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}goods_classes` comment '产品分类表'");
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
		
		if (Schema::connection($db_connection)->hasTable('goods_classes'))
		{
			Schema::connection($db_connection)->dropIfExists('goods_classes');
		}
       
    }
};
