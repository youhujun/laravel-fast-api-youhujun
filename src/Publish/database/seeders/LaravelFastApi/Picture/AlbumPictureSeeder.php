<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-24 18:30:12
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-24 17:00:43
 * @FilePath: \database\seeders\LaravelFastApi\Picture\AlbumPictureSeeder.php
 */

namespace Database\Seeders\LaravelFastApi\Picture;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlbumPictureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $albumPictureData = [
            [
				'admin_id'=>1,
				'user_id'=>1,
				'album_id'=>1,
				'picture_name'=>'album',
				'picture_path'=>DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'album'.DIRECTORY_SEPARATOR,
				'picture_type'=>20,
				'picture_file'=>'01-album-system.png',
				'picture_size'=>'11',
				'picture_spec'=>'80x80',
				'picture_url'=>'https://qiniu.youhujun.com/config/album/01-album-system.png',
				'created_time'=>time(),
				'created_at'=>date('Y-m-d H:i:s',time())
			],

            [
				'admin_id'=>1,
				'user_id'=>1,
				'album_id'=>1,
				'picture_name'=>'avatar',
				'picture_path'=>DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'avatar'.DIRECTORY_SEPARATOR,
				'picture_type'=>20,
				'picture_file'=>'01-avatar-system.png',
				'picture_size'=>'57',
				'picture_spec'=>'658x494',
				'picture_url'=>'https://qiniu.youhujun.com/config/avatar/01-avatar-system.png',
				'created_time'=>time(),
				'created_at'=>date('Y-m-d H:i:s',time())
			],

			 [
				'admin_id'=>1,
				'user_id'=>1,
				'album_id'=>1,
				'picture_name'=>'logo',
				'picture_path'=>DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR,
				'picture_type'=>20,
				'picture_file'=>'default_logo.png',
				'picture_size'=>'60',
				'picture_spec'=>'600x600',
				'picture_url'=>'https://qiniu.youhujun.com/config/file/config/default_logo.png',
				'created_time'=>time(),
				'created_at'=>date('Y-m-d H:i:s',time())
			],
        ];

        DB::table('album_picture')->insert($albumPictureData);

    }
}
