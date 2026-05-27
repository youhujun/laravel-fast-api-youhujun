<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-02 02:54:25
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 01:48:04
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Picture\AdminAlbumFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Picture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\GetDefaultAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\FindAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\AddAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\GetAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\UpdateAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\DeleteAlbumDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Picture\Album\GetAlbumPictureDTO;

use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Picture\EsAlbumCollection;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Picture\EsAlbumResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Picture\EsAlbumPictureResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Picture\EsAlbumPictureCollection;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Picture\AlbumController
 * @see \App\Facades\LaravelFastApi\V1\Admin\Picture\AdminAlbumFacade
 */
class AdminAlbumFacadeService
{
    public function test()
    {
        echo "AdminAlbumFacadeService test";
    }

    //相册排序
    protected static $sortMapArray = [
        1 => ['sort','desc'],
        2 => ['sort','asc'],
        3 => ['created_time','asc'],
        4 => ['created_time','desc']
    ];

    //相册图片排序
    protected static $pictureSortMapArray = [
        1 => ['picture_size','desc'],
        2 => ['picture_size','asc'],
        3 => ['created_time','asc'],
        4 => ['created_time','desc']
    ];

    /**
    * 获取默认的管理员相册
    *
    * @param [type] $validated
    * @param [type] $adminObject
    * @return void
    */
    public function getDefaultAlbum(GetDefaultAlbumDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetDefaultAlbumError'));

        $indexName = config('common_es.indices.album.albums');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

        //如果是相册管理员就可以获取所有
        if (is_album_admin($adminObject)) {
            //相册管理员必定查询系统相册
            if ($requestDTO->album_type) {
                $esQuery->whereIn('album_type', [0,$requestDTO->album_type]);
            } else {
                $esQuery->whereIn('album_type', [0,10,20]);
            }
        } else {
            //否则只能查询自己的相册
            if ($requestDTO->album_type) {
                $esQuery->where('album_type', $requestDTO->album_type);
            } else {
                $esQuery->whereIn('album_type', [10,20]);
            }
            $esQuery->where('admin_uid', $adminObject->biz_id);
        }

        $albumColelction = $esQuery->where('is_default', 1)->orderBy('sort', 'desc')->orderBy('created_time', 'asc')->limit(10)->get();

        if (!optional($albumColelction)) {
            throw new CommonException('GetDefaultAlbumError');
        }

        $data['data'] = EsAlbumResource::collection($albumColelction);

        $result = code(['code' => 0,'msg' => '获取默认相册成功!'], $data);

        return  $result;
    }

    /**
     * 搜索查找选项
     *
     * @param [type] $find
     * @return void
     */
    public function findAlbum(FindAlbumDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.FindAlbumError'));

        $indexName = config('common_es.indices.album.albums');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

        //如果不是相册管理员就只能获取自己的相册
        if (is_album_admin($adminObject)) {
            //相册管理员必定查询系统相册
            if ($requestDTO->album_type) {
                $esQuery->whereIn('album_type', [0,$requestDTO->album_type]);
            } else {
                $esQuery->whereIn('album_type', [0,10,20]);
            }
        } else {
            //否则只能查询自己的相册
            if ($requestDTO->album_type) {
                $esQuery->where('album_type', $requestDTO->album_type);
            } else {
                $esQuery->whereIn('album_type', [10,20]);
            }
            $esQuery->where('admin_uid', $adminObject->biz_id);
        }

        if ($requestDTO->find) {
            $esQuery->whereLike('album_name', $requestDTO->find);
        }


        $albumColelction = $esQuery->orderBy('sort', 'desc')->orderBy('created_at', 'asc')->limit(100)->get();

        if (!optional($albumColelction)) {
            throw new CommonException('FindAlbumError');
        }

        $data['data'] = EsAlbumResource::collection($albumColelction);

        $result = code(['code' => 0,'msg' => '查找相册成功!'], $data);

        return  $result;
    }

    /**
    *
    * @param [type] $sortType
    * @param [type] $adminObject
    * @return void
    */
    public function getAlbum(GetAlbumDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_admin_code.GetAlbumError'));

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;

        $indexName = config('common_es.indices.album.albums');

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

        //如果设置相册类型就要先根据相册类型查询
        if (isset($requestDTO->album_type) && $requestDTO->album_type && $requestDTO->album_type != -1) {
            $esQuery->where('album_type', $requestDTO->album_type);

            //如果不是开发者,超级管理员和相册管理员,那么就只能查看自己的相册
            if (!is_album_admin($adminObject)) {
                $esQuery->where('admin_uid', $adminObject->biz_id);
            }
        } else {
            //如果不是开发者,超级管理员和相册管理员,那么就只能查看自己的相册
            if (!is_album_admin($adminObject)) {
                //同时只能查看 相册类型是管理员的
                $esQuery->where('admin_uid', $adminObject->biz_id);
                $esQuery->where('album_type', 10);
            }
            //开发者,超级管理员,相册管理员可以查看所有相册
        }

        $sortType = $requestDTO->sortType ?? '3';

        if ($sortType == '3' || $sortType == '4') {
            $esQuery->orderBy(self::$sortMapArray[$sortType][0], self::$sortMapArray[$sortType][1]);
        } else {
            $esQuery->orderBy(self::$sortMapArray[$sortType][0], self::$sortMapArray[$sortType][1])->orderBy('created_time', 'desc');
        }

        $albumPaginator = $esQuery->page($currentPage, $perPage)->paginate();

        if (\optional($albumPaginator)) {
            $result = new EsAlbumCollection($albumPaginator, ['code' => 0,'msg' => '查询相册列表成功!']);
        }

        return  $result;
    }

    /**
     * 添加相册
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function addAlbum(AddAlbumDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_admin_code.AddAlbumError'));

        $insertDataArray = [
            'album_uid' => get_snow_flake_id(),
            'admin_uid' => $adminObject->biz_id,
            'user_uid' => $adminObject->user_uid,
            'cover_album_picture_uid' => get_cover_album_picture_uid(),
            'is_default' => 0,
            'is_system' => 0,
            'album_type' => $requestDTO->album_type,
            'album_name' => $requestDTO->album_name ?? $adminObject->account_name,
            'album_description' => $requestDTO->album_description ?? $adminObject->account_name,
            'sort' => 100
        ];
        $albumObject = ShardHelperFacade::createWithShard(Album::class, $adminObject->user_uid, $insertDataArray);

        if (!isset($albumObject->biz_id)) {
            throw new CommonException('AddAlbumError');
        }

        $indexName = config('common_es.indices.album.albums');

        $configKey = get_shard_config_key();

        $inserDataArray =  [
            '_docId' => $albumObject->album_uid,
            'album_uid' => $albumObject->album_uid,
            'shard_key' => $albumObject->shard_key,
            'shard_db' => ShardFacade::getDbName($albumObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($albumObject->user_uid, 'albums', $configKey),
            'admin_uid' => $albumObject->admin_uid,
            'user_uid' => $albumObject->user_uid,
            'cover_album_picture_uid' => $albumObject->cover_album_picture_uid,
            'is_default' => $albumObject->is_default,
            'is_system' => $albumObject->is_system,
            'album_type' => $albumObject->album_type,
            'album_name' => $albumObject->album_name,
            'album_description' => $albumObject->album_description,
            'sort' => $albumObject->sort,
            'created_time' => $albumObject->created_time,
            'updated_time' => $albumObject->updated_time,
            'created_at' => $albumObject->created_at,
            'updated_at' => $albumObject->updated_at,
            'deleted_at' => $albumObject->deleted_at
        ];
        $esResult = EsFacade::createDoc($indexName, $inserDataArray, $albumObject->biz_id);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加相册失败','$albumObject' => $albumObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'AdminAlbumFacadeService', 'handleError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'AddAlbum');

        //控制相册是否包含图片加载
        //AlbumResource::setWithPicture(1);

        $data['data'] =  new EsAlbumResource($albumObject);

        $result = code(['code' => 0,'msg' => '添加相册成功'], $data);

        return  $result;
    }

    /**
     * 更新相册
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updateAlbum(UpdateAlbumDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_admin_code.UpdateAlbumError'));

        $indexName = config('common_es.indices.album.albums');

        $esAlbumObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('album_uid', $requestDTO->album_uid)->get()->first();

        //降级熔断
        if (!isset($esAlbumObject->album_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $updateDataArray = [
            'album_name' => $requestDTO->album_name,
            'album_description' => $requestDTO->album_description,
            'album_type' => $requestDTO->album_type,
            'sort' => $requestDTO->sort,
        ];

        $albumObject = Album::queryByShard($esAlbumObject->user_uid)->where('album_uid', $requestDTO->album_uid)->first();

        if (!isset($albumObject->album_uid)) {
            throw new CommonException('ThatDataNotExistsError');
        }

        $updateResult = $albumObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('UpdateAlbumError');
        }

        $albumObject = $albumObject->fresh();

        $indexName = config('common_es.indices.album.albums');

        $updateDataArray =  [
            'cover_album_picture_uid' => $albumObject->cover_album_picture_uid,
            'album_uid' => $albumObject->album_uid,
            'album_type' => $albumObject->album_type,
            'album_name' => $albumObject->album_name,
            'album_description' => $albumObject->album_description,
            'sort' => $albumObject->sort,
            'updated_time' => $albumObject->updated_time,
            'updated_at' => $albumObject->updated_at,

        ];
        $esResult = EsFacade::updateDoc($indexName, $albumObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es更新相册失败','$albumObject' => $albumObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'AdminAlbumFacadeService', 'handleError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'UpdateAlbum');

        $data['data'] =  new EsAlbumResource($albumObject);

        $result = code(['code' => 0,'msg' => '更新相册成功'], $data);

        return  $result;
    }

    /**
     * 删除相册
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function deleteAlbum(DeleteAlbumDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_admin_code.DeleteAlbumError'));

        $indexName = config('common_es.indices.album.albums');

        $esAlbumObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('album_uid', $requestDTO->album_uid)->get()->first();

        //降级熔断
        if (!isset($esAlbumObject->album_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $albumObject = Album::queryByShard($esAlbumObject->user_uid)->where('album_uid', $requestDTO->album_uid)->first();

        if (!isset($albumObject->album_uid)) {
            throw new CommonException('ThatDataNotExistsError');
        }

        if ($albumObject->is_system) {
            throw new CommonException('ThisAlbumIsSystemError');
        }

        //查询该相册下的图片数量
        $albumPictureIndexName = config('common_es.indices.album.album_pictures');

        $albumPictureNumber = EsQueryFacade::index($albumPictureIndexName)->whereNull('deleted_at')->where('album_uid', $requestDTO->album_uid)->get()->count();

        //如果有图片需要先将图片转移到默认相册
        if ($albumPictureNumber) {
            //根据删除的相册类型获取默认相册album_uid
            $default_album_uid = 0;

            //根据删除的系统相册类型获取默认相册
            if ($albumObject->album_type == 0) {
                $default_album_uid = get_system_album_uid();
            }

            //管理员
            if ($albumObject->album_type == 10) {
                $default_album_uid = get_admin_album_uid($albumObject->admin_uid);
            }

            //用户
            if ($albumObject->album_type == 20) {
                $default_album_uid = get_user_album_uid($albumObject->user_uid);
            }

            if (!$default_album_uid) {
                throw new CommonException('ThisSystemAlbumNotExistsError');
            }

            //开始事务
            DB::beginTransaction();

            //查询该相册下的所有图片
            $albumPictureCollection = AlbumPicture::queryByShard($albumObject->biz_id)->where('album_uid', $requestDTO->album_uid)->lockForUpdate()->get();

            foreach ($albumPictureCollection as $key => $albumPictureObject) {
                //因为涉及分片计算业务所有需要添加新数据
                $insertDataArray = [
                    'album_picture_uid' => get_snow_flake_id(),
                    'admin_uid' => $albumPictureObject->admin_uid,
                    'album_uid' => $album_uid,
                    'user_uid' => $albumPictureObject->user_uid,
                    'picture_name' => $albumPictureObject->picture_name,
                    'picture_file' => $albumPictureObject->picture_file,
                    'picture_path' => $albumPictureObject->picture_path,
                    'picture_size' => $albumPictureObject->picture_size,
                    'picture_spec' => $albumPictureObject->picture_spec,
                    'picture_type' => $albumPictureObject->picture_type,
                ];

                $newAlbumPictureObject = ShardHelperFacade::createWithShard(AlbumPicture::class, $album_uid, $insertDataArray);

                if (!isset($newAlbumPictureObject->biz_id)) {
                    plog(['error' => '迁移相册图片失败','$albumPictureObject' => $albumPictureObject,'$insertDataArray' => $insertDataArray], 'AdminAlbumFacadeService', 'deleteAlbumError');
                    continue;
                }

                //删除原有的图片
                $deleteUpdateDataArray = [
                    'deleted_at' => date('Y-m-d H:i:s'),
                ];

                $updateResult = $albumPicutreObject->updateWithShard($deleteUpdateDataArray);

                if (!$updateResult) {
                    plog(['error' => '迁移相册图片失败','$albumPictureObject' => $albumPictureObject,'$updateResult' => $updateResult], 'AdminAlbumFacadeService', 'deleteAlbumError');
                    continue;
                }

               $albumPictureIndexName = config('common_es.indices.album.album_pictures');

                $configKey = get_shard_config_key();
                //es先添加
                $inserDataArray = [
                    '_docId' => $newAlbumPictureObject->album_picture_uid,
                    'shard_db' => ShardFacade::getDbName($newAlbumPictureObject->user_uid, $configKey),
                    'shard_table' => ShardFacade::getTableName($newAlbumPictureObject->user_uid, 'album_pictures', $configKey),
                    'album_picture_uid' => $newAlbumPictureObject->album_picture_uid,
                    'album_uid' => $newAlbumPictureObject->album_uid,
                    'admin_uid' => $newAlbumPictureObject->admin_uid,
                    'user_uid' => $newAlbumPictureObject->user_uid,
                    'picture_name' => $newAlbumPictureObject->picture_name,
                    'picture_tag' => $newAlbumPictureObject->picture_tag,
                    'picture_path' => $newAlbumPictureObject->picture_path,
                    'picture_file' => $newAlbumPictureObject->picture_file,
                    'picture_size' => $newAlbumPictureObject->picture_size,
                    'picture_spec' => $newAlbumPictureObject->picture_spec,
                    'picture_type' => $newAlbumPictureObject->picture_type,
                    'picture_url' => $newAlbumPictureObject->picture_url,
                    'created_time' => $newAlbumPictureObject->created_time,
                    'updated_time' => $newAlbumPictureObject->updated_time,
                    'created_at' => $newAlbumPictureObject->created_at,
                    'updated_at' => $newAlbumPictureObject->updated_at,
                    'deleted_at' => $newAlbumPictureObject->deleted_at
                ];

                $insertResult = EsFacade::createDoc($albumPictureIndexName, $inserDataArray, $newAlbumPictureObject->album_picture_uid);

                if (!isset($insertResult) || !isset($insertResult['code']) || $insertResult['code'] != 0) {
                    plog(['error' => 'es添加相册图片数据失败','$insertResult' => $insertResult,'$newAlbumPictureObject' => $newAlbumPictureObject,'$adminObject' => $adminObject], 'AdminAlbumFacadeService', 'handleError');
                }

                //更新删除
                $udpateDataArray = [
                    'deleted_at' => date('Y-m-d H:i:s', time()),
                ];

                $updateResult = EsFacade::updateDoc($albumPictureIndexName, $albumPicutreObject->album_picture_uid, $udpateDataArray);

                if (!isset($updateResult) || !isset($updateResult['code']) || $updateResult['code'] != 0) {
                    plog(['error' => 'es删除相册图片数据失败','$updateResult' => $updateResult,'$albumPicutreObject' => $albumPicutreObject,'$adminObject' => $adminObject], 'AdminAlbumFacadeService', 'handleError');
                }
            }
        }

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s'),
        ];

        $deleteResult = $albumObject->updateWithShard($updateDataArray);

        if (!$deleteResult) {
            //回滚
            DB::rollBack();

            throw new CommonException('DeleteAlbumError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'DeleteAlbum', true);

        DB::commit();

        $indexName = config('common_es.indices.album.albums');

        $updateDataArray =  [
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_time' => $albumObject->updated_time,
            'updated_at' => $albumObject->updated_at,

        ];
        $esResult = EsFacade::updateDoc($indexName, $albumObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es删除相册失败','$albumObject' => $albumObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'AdminAlbumFacadeService', 'handleError');
        }

        $result = code(['code' => 0,'msg' => '删除相册成功']);

        return  $result;
    }

    /**
     * 获取相册图片
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function getAlbumPicture(GetAlbumPictureDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_admin_code.GetAlbumPictureError'));

        $indexName = config('common_es.indices.album.albums');

        $esAlbumObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('album_uid', $requestDTO->album_uid)->get()->first();

        //降级熔断
        if (!isset($esAlbumObject->album_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $albumPictureIndexName = config('common_es.indices.album.album_pictures');

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;
        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($albumPictureIndexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

        $esQuery->where('album_uid', '=', $requestDTO->album_uid);

        //如果普通管理员 那么只能查看他自己的所属图片
        if (!is_album_admin($adminObject)) {
            $esQuery->where('admin_uid', '=', $adminObject->biz_id);
        }

        $sortType = $requestDTO->sortType ?? 3;

        $esQuery->orderBy(self::$pictureSortMapArray[$sortType][0], self::$pictureSortMapArray[$sortType][1]);

        //如果是 1或 2是根据 图片大小排序 可以再加上时间排序
        if ($sortType == 1 || $sortType == 2) {
            $esQuery->orderBy('created_time', 'desc');
        }

        $picturePaginator = $esQuery->page($currentPage, $perPage)->paginate();

        if (\optional($picturePaginator)) {
            $result = new EsAlbumPictureCollection($picturePaginator, ['code' => 0,'msg' => '获取相册图片成功']);
        }

        return  $result;
    }
}
