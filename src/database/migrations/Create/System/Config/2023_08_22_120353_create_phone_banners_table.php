<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-22 12:03:53
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-02-25 23:12:51
 * @FilePath: \base.laravel.comd:\wwwroot\Working\PHP\Components\Laravel\youhujun\laravel-fast-api-base\src\database\migrations\System\2023_08_22_120353_create_phone_banner_table.php
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
		if (!Schema::connection($db_connection)->hasTable('phone_banners'))
		{
			Schema::connection($db_connection)->create('phone_banners', function (Blueprint $table)
			{
				$table->id()->comment('主键-手机轮播图');
				$table->char('admin_uid', 20)->notNull()->default('')->comment('管理员uid,雪花ID');
				$table->char('album_picture_uid', 20)->notNull()->default('')->comment('相册图片uid,雪花ID');
				$table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
				$table->string('redirect_url',128)->nullable()->comment('跳转路径');
				$table->string('note',128)->nullable()->comment('备注');

				// 时间字段（自动填充+索引，关键优化）
				$table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

				$table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

				// 索引
				$table->index('album_picture_uid');
				$table->index('sort');
			});

			$prefix = config('database.connections.'.$db_connection.'.prefix');

			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}phone_banners` comment '手机轮播图'");
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

		if (Schema::connection($db_connection)->hasTable('phone_banners'))
		{
			Schema::connection($db_connection)->dropIfExists('phone_banners');
		}
        
    }
};
