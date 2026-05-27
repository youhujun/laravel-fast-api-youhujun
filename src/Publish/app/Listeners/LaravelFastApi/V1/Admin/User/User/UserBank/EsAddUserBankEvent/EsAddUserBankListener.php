<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-12 17:48:06
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-16 16:20:33
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\User\User\UserBank\EsAddUserBankEvent\EsAddUserBankListener.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\User\User\UserBank\EsAddUserBankEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\Admin\CommonException;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Models\LaravelFastApi\V1\User\Info\UserBank;
use App\Models\LaravelFastApi\V1\System\Module\Bank;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\Admin\Admin;
/**
 * @see \App\Events\LaravelFastApi\V1\Admin\User\User\UserBank\EsAddUserBankEvent
 */
class EsAddUserBankListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $userBankObject = $event->userBankObject;
        $adminObject = $event->adminObject;

        $bank_name = Bank::find($userBankObject->bank_id)->bank_name;

        $albumPictureIndexName = config('common_es.indices.album.album_pictures');

        $esFrontPcitureObject = EsQueryFacade::index($albumPictureIndexName)->where('album_picture_uid', $userBankObject->bank_front_uid)->get()->first();

        $esBackPcitureObject = EsQueryFacade::index($albumPictureIndexName)->where('album_picture_uid', $userBankObject->bank_back_uid)->get()->first();

        $bank_front_picture = '';
        $bank_back_picture = '';

        if (isset($esFrontPcitureObject->album_picture_uid)) {
        
            $ablum_pciture_type = $esFrontPcitureObject->picture_type;

            //本地存储
            if ($ablum_pciture_type == 10) {
                $bank_front_picture = asset('/storage'.$esFrontPcitureObject->picture_path.DIRECTORY_SEPARATOR.$esFrontPcitureObject->picture_file);
            }
            //云端存储
            if ($ablum_pciture_type == 20) {
                $bank_front_picture = $esFrontPcitureObject->picture_url;
            }
        }

        if (isset($esBackPcitureObject->album_picture_uid)) {

            $ablum_pciture_type = $esBackPcitureObject->picture_type;

            //本地存储
            if ($ablum_pciture_type == 10) {
                $bank_back_picture = asset('/storage'.$esBackPcitureObject->picture_path.DIRECTORY_SEPARATOR.$esBackPcitureObject->picture_file);
            }
            //云端存储
            if ($ablum_pciture_type == 20) {
                $bank_back_picture = $esBackPcitureObject->picture_url;
            }
        }

        $indexName = config('common_es.indices.user.user_banks');

        $configKey = get_shard_config_key();

        $insertDataArray = [
            '_docId' => $userBankObject->biz_id,
            'user_uid' => $userBankObject->user_uid,
            'user_bank_uid' => $userBankObject->biz_id,
            'shard_key' => $userBankObject->shard_key,
            'shard_db' => ShardFacade::getDbName($userBankObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($userBankObject->user_uid, 'user_banks', $configKey),
            'bank_id' => $userBankObject->bank_id,
            'bank_name' => $bank_name,
            'bank_front_uid' => $userBankObject->bank_front_uid,
            'bank_back_uid' => $userBankObject->bank_back_uid,
            'bank_front_picture' => $bank_front_picture,
            'bank_back_picture' => $bank_back_picture,
            'is_default' => $userBankObject->is_default,
            'bank_number' => $userBankObject->bank_number,
            'bank_account' => $userBankObject->bank_account,
            'bank_address' => $userBankObject->bank_address,
            'sort' => $userBankObject->sort,
            'created_at' => $userBankObject->created_at,
            'created_time' => $userBankObject->created_time,
            'updated_at' => $userBankObject->updated_at,
            'updated_time' => $userBankObject->updated_time,
            'deleted_at' => $userBankObject->deleted_at
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $userBankObject->biz_id);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加默认银行卡失败','esResult' => $esResult,'userBankObject' => $userBankObject,'adminObject' => $adminObject], 'EsAddUserBankListener', 'handleError');

            throw new CommonException('EsAddUserBankError');
        }
    }
}
