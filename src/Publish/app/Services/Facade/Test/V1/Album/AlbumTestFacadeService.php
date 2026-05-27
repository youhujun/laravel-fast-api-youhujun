<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-03 16:45:30
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-03 23:18:18
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Test\V1\Album\AlbumTestFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\Test\V1\Album;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

/**
 * @see \App\Facades\Test\V1\Album\AlbumTestFacade
 */
class AlbumTestFacadeService
{
    public function test()
    {
        echo "AlbumTestFacadeService test";
        //\App\Facades\LaravelFastApi\V1\Es\Sync\Album\EsSyncAlbumFacade::test();
        //$this->testEsSyncAlbum();
        // $this->testEsSyncAlbumPictrue();
        //$this->searchAlbumPicture();
    }

    public function testEsSyncAlbum()
    {
        \App\Facades\LaravelFastApi\V1\Es\Sync\Album\EsSyncAlbumFacade::syncAlbums();
    }

    public function testEsSyncAlbumPictrue()
    {
        \App\Facades\LaravelFastApi\V1\Es\Sync\Album\EsSyncAlbumFacade::syncAlbumPictures();
    }

    public function searchAlbumPicture()
    {
        $indexName = config('common_es.indices.album.album_pictures');

        $queryArray = [
            'term' => ['picture_tag' => 'cover']
        ];

        $esResult = EsFacade::searchDoc($indexName, $queryArray);

        //p($esResult);

        /* Array
        (
            [code] => 0
            [msg] => es文档批量查询
            [status] => 1
            [error] =>
            [data] => Array
                (
                    [took] => 2
                    [timed_out] =>
                    [_shards] => Array
                        (
                            [total] => 1
                            [successful] => 1
                            [skipped] => 0
                            [failed] => 0
                        )

                    [hits] => Array
                        (
                            [total] => Array
                                (
                                    [value] => 1
                                    [relation] => eq
                                )

                            [max_score] => 0.9808291
                            [hits] => Array
                                (
                                    [0] => Array
                                        (
                                            [_index] => youhu_album_pictures_index
                                            [_type] => _doc
                                            [_id] => 276414003930789
                                            [_score] => 0.9808291
                                            [_source] => Array
                                                (
                                                    [shard_db] => ds_0
                                                    [shard_table] => album_pictures_0
                                                    [album_picture_uid] => 276414003930789
                                                    [album_uid] => 276413718634222
                                                    [admin_uid] => 0
                                                    [user_uid] => 0
                                                    [picture_name] => system_cover.jpg
                                                    [picture_tag] => cover
                                                    [picture_path] => /system/images/cover.jpg
                                                    [picture_file] => system_cover.jpg
                                                    [picture_size] => 50
                                                    [picture_spec] => 1920*1080
                                                    [picture_type] => 20
                                                    [picture_url] => https://visit.youhujun.com/api.youhujun.com/config/album/album.png
                                                    [created_at] => 2026-02-12 18:18:22
                                                    [updated_at] => 2026-03-23 01:33:16
                                                    [deleted_at] =>
                                                )

                                        )

                                )

                        )

                )

        ) */

        $pictureArray = null;

        if (isset($esResult['code']) && $esResult['code'] == 0) {
            if (isset($esResult['data']['hits']['total']['value']) && $esResult['data']['hits']['total']['value']) {
                $pictureArray = $esResult['data']['hits']['hits'][0]['_source'];
            }
        }

        p($pictureArray);
    }
}
