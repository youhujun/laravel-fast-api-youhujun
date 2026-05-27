<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-24 18:30:12
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-05 12:40:17
 * @picturePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\Picture\AlbumPictureSeeder.php
 */

namespace Database\Seeders\LaravelFastApi\Picture;

use Illuminate\Database\Seeder;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Facades\Common\V1\Shard\ShardHelperFacade;

class AlbumPictureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShardHelperFacade::queryAllShards(AlbumPicture::class, function ($query) {
            $query->truncate();
        });
        // 查询系统相册的 album_uid
        $configAlbumObject = ShardHelperFacade::queryAllShards(
            Album::class,
            function ($query) {
                $query->where('album_name', 'config');
            },
            'album_name',
            ['config']
        )->first();

        if (!$configAlbumObject) {
            $this->command->warn('系统相册不存在，跳过 AlbumPictureSeeder');
            return;
        }

        $config_album_uid = $configAlbumObject->album_uid;

        $this->command->info('开始创建相册图片数据...');

        AlbumPicture::bindShardBusinessId($config_album_uid);
        // 系统相册封面
        AlbumPicture::create([
            'album_picture_uid' =>  get_snow_flake_id(),
            'album_uid' => $config_album_uid,
            'picture_name' => 'system_cover.jpg',
            'picture_tag' => 'cover',
            'picture_path' => '/system/images/cover.jpg',
            'picture_file' => 'system_cover.jpg',
            'picture_size' => 50,
            'picture_spec' => '1920*1080',
            'picture_type' => 20,
            'picture_url' => 'https://visit.youhujun.com/qiniu.youhujun.com/config/album/album.png',
            'revision' => 0,
        ]);

        $this->command->info("相册封面创建完成");


        AlbumPicture::bindShardBusinessId($config_album_uid);

        // 系统用户头像
        AlbumPicture::create([
            'album_picture_uid' =>  get_snow_flake_id(),
            'album_uid' => $config_album_uid,
            'picture_name' => 'default_avatar.jpg',
            'picture_tag' => 'avatar',
            'picture_path' => '/system/images/avatar.jpg',
            'picture_file' => 'default_avatar.jpg',
            'picture_size' => 20,
            'picture_spec' => '200*200',
            'picture_type' => 20,
            'picture_url' => 'https://visit.youhujun.com/qiniu.youhujun.com/config/avatar/01-avatar-system.png',
            'revision' => 0,
        ]);

        $this->command->info("默认头像创建完成");


        AlbumPicture::bindShardBusinessId($config_album_uid);

        // 系统默认logo
        AlbumPicture::create([
            'album_picture_uid' =>  get_snow_flake_id(),
            'album_uid' => $config_album_uid,
            'picture_name' => 'default_logo.jpg',
            'picture_tag' => 'logo',
            'picture_path' => '/system/images/logo.jpg',
            'picture_file' => 'default_logo.jpg',
            'picture_size' => 30,
            'picture_spec' => '300*100',
            'picture_type' => 20,
            'picture_url' => 'https://visit.youhujun.com/qiniu.youhujun.com/config/file/config/default_logo.png',
            'revision' => 0,
        ]);
        $this->command->info("默认logo创建完成");

        $this->command->info('✅ 所有相册图片数据填充完成（模型填充）！');
    }
}
