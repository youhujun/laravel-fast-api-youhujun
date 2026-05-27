<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-19 22:02:46
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-16 19:12:20
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserRealAuthFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Events\LaravelFastApi\V1\Admin\User\User\CheckUserRealAuthEvent;
use App\Events\LaravelFastApi\V1\Admin\User\User\SetUserIdCardEvent;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\User\User\RealAuth\GetUserRealAuthApplyDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\RealAuth\RealAuthUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\RealAuth\GetUserIdCardDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\RealAuth\SetUserIdCardDTO;
//Job
use App\Jobs\LaravelFastApi\V1\Admin\User\User\RealAuth\EsAddUserIdCardJob;
use App\Jobs\LaravelFastApi\V1\Admin\User\User\RealAuth\EsUpdateUserIdCardJob;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Log\UserRealAuthLog;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\User\Info\UserIdCard;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\UserRealAuth\EsUserRealAuthLogCollection;
use App\Http\Resources\LaravelFastApi\V1\Db\Admin\User\UserResource;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserRealAuthController
 * @see \App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserRealAuthFacade
 */
class AdminUserRealAuthFacadeService
{
    public function test()
    {
        echo "AdminUserRealAuthFacadeService test";
    }

    protected static $sortMapArray = [
        1 => ['created_time','asc'],
        2 => ['created_time','desc'],
    ];

    /**
     * 获取用户实名认证申请
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function getUserRealAuthApply(GetUserRealAuthApplyDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.GetUserRealAuthApplyError'));

        $validated = $requestDTO->toArray();

        $indexName = config('common_es.indices.logs.user_real_auth_logs');

        //分页参数
        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;
        $pageName = 'page';

        //初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

        $esQuery->where('user_uid', $validated['user_uid']);

        $esQuery->orderBy('created_time', 'desc');

        // 排序
         //排序
        if (isset($requestDTO->sortType)) {
            $sortType = $requestDTO->sortType;

            $esQuery->orderBy(self::$sortMapArray[$sortType][0], self::$sortMapArray[$sortType][1]);
        }

        $userApplyRealAuthPaginator = $esQuery->page($currentPage, $perPage)->paginate();

        if (\optional($userApplyRealAuthPaginator)) {
            $result = new EsUserRealAuthLogCollection($userApplyRealAuthPaginator, ['code' => 0,'msg' => '获取用户实名认证申请成功!'], null);
        }

        return  $result;
    }

    /**
      * 实名认证技师
      *
      * @param [type] $validated
      * @param [type] $adminObject
      * @return void
      */
    public function realAuthUser(RealAuthUserDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.RealAuthUserError'));

        $validated = $requestDTO->toArray();

        $userObject = User::queryByShard($validated['user_uid'])->where('user_uid', $validated['user_uid'])->first();

        if (!isset($userObject->biz_id)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $userRealAuthLogObject = UserRealAuthLog::queryByShard($validated['user_uid'])->where('user_real_auth_log_uid', $validated['user_real_auth_log_uid'])->first();

        if (!isset($userRealAuthLogObject->biz_id)) {
            throw new CommonException('ThisDataNotExistsError');
        }


        $updateDataArray = [];

        //默认审核不通过
        $updateDataArray['real_auth_status'] = 30;

        if ($validated['is_real_auth']) {
            //审核通过
            $updateDataArray['real_auth_status'] = 40;
        }

        $userUpdateResult = $userObject->updateWithShard($updateDataArray);

        if (!$userUpdateResult) {
            throw new CommonException('RealAuthUserError');
        }

        //默认审核日志不通过
        $updateLogDataArray = [];

        $updateLogDataArray['status'] = 30;
        $updateLogDataArray['auth_at'] = date('Y-m-d H:i:s');
        $updateLogDataArray['auth_time'] = time();

        if ($validated['is_real_auth']) {
            //审核通过
            $updateLogDataArray['status'] = 20;
        }

        $udpateLogResult = $userRealAuthLogObject->updateWithShard($updateLogDataArray);

        if (!$udpateLogResult) {
            throw new CommonException('RealAuthUserError');
        }

        CheckUserRealAuthEvent::dispatch($adminObject, $requestDTO);

        CommonEvent::dispatch($adminObject, $validated, 'RealAuthUser');

        $result = code(['code' => 0,'msg' => '审核实名认证用户成功!']);

        return $result;
    }

    /**
     *获取用户身份证
     *
     * @return void
     */
    public function getUserIdCard(GetUserIdCardDTO $requestDTO, Admin $adminObject)
    {
        $result = ['code' => 10000,'msg' => '获取用户身份证失败!'];

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;

        //先查询es中有没有
        $indexName = config('common_es.indices.user.user_id_cards');

        $esUserIdCardObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid', $user_uid)->get()->first();

        $userIndexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($userIndexName)->whereNull('deleted_at')->where('user_uid', $user_uid)->get()->first();

        if (!isset($esUserObject->user_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $data = [];

        $data['id_number'] = $esUserObject->id_number;
        $data['user_uid'] = $esUserObject->user_uid;

        if (isset($esUserIdCardObject->id_card_front_picture)) {
            $data['id_card_front_picture'] = $esUserIdCardObject->id_card_front_picture;
        }

        if (isset($esUserIdCardObject->id_card_back_picture) ) {
            $data['id_card_back_picture'] = $esUserIdCardObject->id_card_back_picture;
        }

        $result = code(['code' => 0,'msg' => '获取用户身份证成功!'], ['data' => $data]);

        return $result;
    }

    /**
    * 设置用户身份证
    *
    * @param [type] $validated
    * @param [type] $adminObject
    * @return void
    */
    public function setUserIdCard(SetUserIdCardDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.SetUserIdCardError'));

        $validated = $requestDTO->toArray();

        $user_uid = $requestDTO->user_uid;
        $id_number = $requestDTO->id_number;

        $id_card_front_uid = $requestDTO->id_card_front_uid;
        $id_card_back_uid = $requestDTO->id_card_back_uid;

        //先查询es中有没有
        $indexName = config('common_es.indices.user.user_id_cards');

        $esUserIdCardObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid', $user_uid)->get()->first();
        //如果有就是修改
        if (isset($esUserIdCardObject->user_uid)) {
            //先修改身份证照片
            if (!empty($id_card_front_uid) && !empty($id_card_back_uid)) {

                $userIdCardObject = UserIdCard::queryByShard($user_uid)->where('user_uid', $user_uid)->first();

                if (!isset($userIdCardObject->biz_id)) {
                    throw new CommonException('ThatDataNotExistError');
                }

                $updateIdCardDataArray = [
                    'id_card_front_uid' => $id_card_front_uid,
                    'id_card_back_uid' => $id_card_back_uid,
                ];

                $updateIdCardResult = $userIdCardObject->updateWithShard($updateIdCardDataArray);

                if (!$updateIdCardResult) {
                    throw new CommonException('SetUserIdCardError');
                }

                 //先处理图片
                $albumPictureIndexName = config('common_es.indices.album.album_pictures');

                $esFrontPcitureObject = EsQueryFacade::index($albumPictureIndexName)->whereNull('deleted_at')->where('album_picture_uid', $id_card_front_uid)->get()->first();

                $esBackPcitureObject= EsQueryFacade::index($albumPictureIndexName)->whereNull('deleted_at')->where('album_picture_uid', $id_card_back_uid)->get()->first();

                //身份证正面
                $id_card_front_picture = '';
                //身份证背面
                $id_card_back_picture = '';

                if (isset($esFrontPcitureObject->album_picture_uid)) {
                
                    $ablum_pciture_type = $esFrontPcitureObject->picture_type;

                    //本地存储
                    if ($ablum_pciture_type == 10) {
                        $id_card_front_picture = asset('/storage'.$esFrontPcitureObject->picture_path.DIRECTORY_SEPARATOR.$esFrontPcitureObject->picture_file);
                    }
                    //云端存储
                    if ($ablum_pciture_type == 20) {
                        $id_card_front_picture = $esFrontPcitureObject->picture_url;
                    }
                }

                if (isset($esBackPcitureObject->album_picture_uid)) {
                  
                    $ablum_pciture_type = $esBackPcitureObject->picture_type;

                    //本地存储
                    if ($ablum_pciture_type == 10) {
                        $id_card_back_picture = asset('/storage'.$esBackPcitureObject->picture_path.DIRECTORY_SEPARATOR.$esBackPcitureObject->picture_file);
                    }
                    //云端存储
                    if ($ablum_pciture_type == 20) {
                        $id_card_back_picture = $esBackPcitureObject->picture_url;
                    }
                }

                $indexName = config('common_es.indices.user.user_id_cards');

                $updateDataArray = [
                    'id_card_front_picture' => $id_card_front_picture,
                    'id_card_back_picture' => $id_card_back_picture,
                    'id_card_front_uid' => $userIdCardObject->id_card_front_uid,
                    'id_card_back_uid' => $userIdCardObject->id_card_back_uid,
                    'updated_at' => $userIdCardObject->updated_at,
                    'updated_time' => $userIdCardObject->updated_time,
                ];

                $esResult = EsFacade::updateDoc($indexName, $userIdCardObject->biz_id, $updateDataArray);

                if (!isset($esResult['code']) || $esResult['code'] != 0) {
                    plog(['error' => 'es更新用户身份证失败','esResult' => $esResult,'$userIdCardObject' => $userIdCardObject,'adminObject' => $adminObject], 'AdminUserRealAuthFacadeService', 'setUserIdCardError');
                    throw new CommonException('EsSetUserIdCardError');
                }
            }
        } else {
            //没有就是添加

            //添加用户身份证
            if (!empty($id_card_front_uid) && !empty($id_card_back_uid)) {
                $insertDataArray = [
                    'user_uid' => $user_uid,
                    'sort' => $requestDTO->sort,
                    'id_card_front_uid' => $id_card_front_uid,
                    'id_card_back_uid' => $id_card_back_uid,
                ];

                $userIdCardObject = ShardHelperFacade::createWithShard(UserIdCard::class, $user_uid, $insertDataArray);

                if (!isset($userIdCardObject->biz_id)) {
                    throw new CommonException('SetUserIdCardError');
                }

                 //先处理图片
                 //先处理图片
                $albumPictureIndexName = config('common_es.indices.album.album_pictures');

                $esFrontPcitureObject = EsQueryFacade::index($albumPictureIndexName)->whereNull('deleted_at')->where('album_picture_uid', $id_card_front_uid)->get()->first();

                $esBackPcitureObject= EsQueryFacade::index($albumPictureIndexName)->whereNull('deleted_at')->where('album_picture_uid', $id_card_back_uid)->get()->first();

                //身份证正面
                $id_card_front_picture = '';
                //身份证背面
                $id_card_back_picture = '';

                if (isset($esFrontPcitureObject->album_picture_uid)) {
                
                    $ablum_pciture_type = $esFrontPcitureObject->picture_type;

                    //本地存储
                    if ($ablum_pciture_type == 10) {
                        $id_card_front_picture = asset('/storage'.$esFrontPcitureObject->picture_path.DIRECTORY_SEPARATOR.$esFrontPcitureObject->picture_file);
                    }
                    //云端存储
                    if ($ablum_pciture_type == 20) {
                        $id_card_front_picture = $esFrontPcitureObject->picture_url;
                    }
                }

                if (isset($esBackPcitureObject->album_picture_uid)) {
                  
                    $ablum_pciture_type = $esBackPcitureObject->picture_type;

                    //本地存储
                    if ($ablum_pciture_type == 10) {
                        $id_card_back_picture = asset('/storage'.$esBackPcitureObject->picture_path.DIRECTORY_SEPARATOR.$esBackPcitureObject->picture_file);
                    }
                    //云端存储
                    if ($ablum_pciture_type == 20) {
                        $id_card_back_picture = $esBackPcitureObject->picture_url;
                    }
                }

                $indexName = config('common_es.indices.user.user_id_cards');

                $configKey = get_shard_config_key();

                $insertDataArray = [
                    '_docId' => $userIdCardObject->biz_id,
                    'user_id_card_uid' => $userIdCardObject->biz_id,
                    'user_uid' => $userIdCardObject->user_uid,
                    'shard_key' => $userIdCardObject->shard_key,
                    'shard_db' => ShardFacade::getDbName($userIdCardObject->user_uid, $configKey),
                    'shard_table' => ShardFacade::getTableName($userIdCardObject->user_uid, 'user_id_cards', $configKey),
                    'id_card_front_uid' => $userIdCardObject->id_card_front_uid,
                    'id_card_back_uid' => $userIdCardObject->id_card_back_uid,
                    'id_card_front_picture' => $id_card_front_picture,
                    'id_card_back_picture' => $id_card_back_picture,
                    'sort' => $userIdCardObject->sort,
                    'created_at' => $userIdCardObject->created_at,
                    'created_time' => $userIdCardObject->created_time,
                    'updated_at' => $userIdCardObject->updated_at,
                    'updated_time' => $userIdCardObject->updated_time,
                    'deleted_at' => $userIdCardObject->deleted_at,
                ];

                $esResult = EsFacade::createDoc($indexName, $insertDataArray, $userIdCardObject->biz_id);

                if (!isset($esResult['code']) || $esResult['code'] != 0) {
                    plog(['error' => 'es添加用户身份证失败','esResult' => $esResult,'$userIdCardObject' => $userIdCardObject,'adminObject' => $adminObject], 'AdminUserRealAuthFacadeService', 'setUserIdCardError');
                    throw new CommonException('EsSetUserIdCardError');
                }
            }
        }

        //修改用户身份证号
        $updateDataArray = [
            'id_number' => $id_number,
        ];

        $userInfoObject = UserInfo::queryByShard($user_uid)->where('user_uid', $user_uid)->first();

        if (!isset($userInfoObject->biz_id)) {
            throw new CommonException('ThatDataNotExistError');
        }

        $updateResult = $userInfoObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('SetUserInfoIdNumberError');
        }

        $userInfoObject = $userInfoObject->fresh();

        SetUserIdCardEvent::dispatch($requestDTO, $userInfoObject, $adminObject);

        CommonEvent::dispatch($adminObject, $validated, 'SetUserIdCard');

        $result =  code(['code' => 0,'msg' => '设置用户身份证成功!']);

        return $result;
    }
}
