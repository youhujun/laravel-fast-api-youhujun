<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-03 09:46:04
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-17 04:44:26
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Phone\User\PhoneUserSourceFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\LaravelFastApi\V1\User\User;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;

/**
 * @see \App\Facades\LaravelFastApi\V1\Phone\User\PhoneUserSourceFacade
 */
class PhoneUserSourceFacadeService
{
    public function test()
    {
        echo "PhoneUserSourceFacadeService test";
    }

    protected $userUnionProperty = ['first_uid','second_uid'];

    //分销级别也就是深度
    protected $deep;

    public function __construct()
    {
        $this->deep = count($this->userUnionProperty);
    }

    /**
     * 获取需要添加用户的UserSourceUnion的id数组
     *
     * @param  User    $userObject
     * @param  integer $number 表示几级分销
     */
    public function getInsertUserSourceUnionUid(User $userObject): array
    {
        $indexName = config('common_es.indices.user.users');

        $deep =  $this->deep;

        $userSourceUnionUidArray = [];

        $esSourceUserObject = null;

        $source_user_uid = 0;

        for ($i = 0; $i < $deep ; $i++) {
            if ($i === 0) {

                $user_source_uid = $userObject->source_user_uid;
                $userSourceUnionUidArray[] = $user_source_uid;

            } else {
               
                $esSourceUserObject = EsQueryFacade::index($indexName)->where('user_uid', $userSourceUnionUidArray[$i-1])->get()->first();

                //这里需要判断用户的$source_user_uid是否存在
                if(isset($esSourceUserObject->source_user_uid)){
                    $user_source_uid = $esSourceUserObject->source_user_uid;

                    $userSourceUnionUidArray[] = $user_source_uid;

                }else{
                    break;
                }
            }
           
        }

        return $userSourceUnionUidArray;
    }

    /**
     * 获取需要添加的关联数据
     *
     * @param  User    $userObject
     * @param  integer $number
     */
    public function getInsertUserSourceUnionData(User $userObject)
    {
        //获取需要插入的id
        $userSourceUnionUidArray =  $this-> getInsertUserSourceUnionUid($userObject);

        $UserSourceUnionData = [];

        foreach ($userSourceUnionUidArray as $key => $value) {
            $UserSourceUnionData[$this->userUnionProperty[$key]] = $value;
        }

        return $UserSourceUnionData;
    }
}
