<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-19 17:22:40
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-30 11:11:58
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Common\V1\Es\Console\EsCreateIndexFacadeService.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 *
 * 命令:
 * php artisan create:es
 */

namespace App\Services\Facade\Common\V1\Es\Console;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\Facade\Common\V1\Es\Console\Traits\EsFacadeServiceBaseTrait;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

/**
 * @see \App\Facades\Common\V1\Es\Console\EsCreateIndexFacade
 */
class EsCreateIndexFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsCreateIndexFacadeService test";
    }


    public function __construct()
    {
    }

    /**
     * 创建Elasticsearch索引
     */
    public function createEsIndex(): void
    {
        //创建系统索引
        $this->createEsSystemIndex();
        //创建业务配置
        $this->createEsBusinessIndex();
        //创建用户索引
        $this->createEsUserIndex();
        //创建相册索引
        $this->createEsAlbumIndex();
        //创建文章索引
        $this->createdEsArticleIndex();
        //创建日志索引
        $this->createEsLogIndex();
        //创建关联索引
        $this->createEsUnionIndex();
    }
    //创建系统索引
    protected function createEsSystemIndex()
    {
        //创建系统配置表索引
        \App\Facades\LaravelFastApi\V1\Es\Index\System\EsCreateSystemIndexFacade::createSystemConfigIndex();
        //创建权限菜单
        \App\Facades\LaravelFastApi\V1\Es\Index\System\EsCreateSystemIndexFacade::createPermissionIndex();
        //创建系统提示音
        \App\Facades\LaravelFastApi\V1\Es\Index\System\EsCreateSystemIndexFacade::createSystemVoiceConfigIndex();
        //创建微信配置
        \App\Facades\LaravelFastApi\V1\Es\Index\System\EsCreateSystemIndexFacade::createSystemWeChatConfigIndex();
        //创建抖音配置
        \App\Facades\LaravelFastApi\V1\Es\Index\System\EsCreateSystemIndexFacade::createSystemDouYinConfigIndex();
        //创建角色
        \App\Facades\LaravelFastApi\V1\Es\Index\System\EsCreateSystemIndexFacade::createRoleIndex();
        //创建地区
        \App\Facades\LaravelFastApi\V1\Es\Index\System\EsCreateSystemIndexFacade::createRegionIndex();
        //创建银行
        \App\Facades\LaravelFastApi\V1\Es\Index\System\EsCreateSystemIndexFacade::createBankIndex();
    }

    protected function createEsBusinessIndex()
    {
        //创建提现配置索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Business\EsCreateBusiessIndexFacade::createSystemWithdrawConfigIndex();
        //创建商品分类索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Business\EsCreateBusiessIndexFacade::createGoodsClassIndex();
        //创建文章分类索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Business\EsCreateBusiessIndexFacade::createArticleCategoryIndex();
        //创建标签索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Business\EsCreateBusiessIndexFacade::createLabelIndex();
        //创建级别配置项索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Business\EsCreateBusiessIndexFacade::createLevelItemIndex();
        //创建用户级别配置项索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Business\EsCreateBusiessIndexFacade::createUserLevelIndex();
        //创建手机轮播图索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Business\EsCreateBusiessIndexFacade::createPhoneBannerIndex();
    }

    protected function createEsUserIndex()
    {
        //创建用户索引
        \App\Facades\LaravelFastApi\V1\Es\Index\User\EsCreateUserIndexFacade::createUsersIndex();
        //创建用户账户索引
        \App\Facades\LaravelFastApi\V1\Es\Index\User\EsCreateUserIndexFacade::createUserAmountsIndex();
        //创建管理员索引
        \App\Facades\LaravelFastApi\V1\Es\Index\User\EsCreateAdminIndexFacade::createAdminsIndex();
        //创建用授权索引
        \App\Facades\LaravelFastApi\V1\Es\Index\User\EsCreateYouhuAuthServiceIndexFacade::createYouhuAuthServiceIndex();
        //创建用户地址索引
        \App\Facades\LaravelFastApi\V1\Es\Index\User\EsCreateUserIndexFacade::createUserAddressIndex();
        //创建用户身份证索引
        \App\Facades\LaravelFastApi\V1\Es\Index\User\EsCreateUserIndexFacade::createUserIdCardsIndex();
        //创建用户银行卡索引
        \App\Facades\LaravelFastApi\V1\Es\Index\User\EsCreateUserIndexFacade::createUserBanksIndex();
        //创建用户图片索引
        \App\Facades\LaravelFastApi\V1\Es\Index\User\EsCreateUserIndexFacade::createUserPicturesIndex();
        //创建用户认证索引
        \App\Facades\LaravelFastApi\V1\Es\Index\User\EsCreateUserIndexFacade::createUserCertificationsIndex();
        //创建用户手机索引
        \App\Facades\LaravelFastApi\V1\Es\Index\User\EsCreateUserIndexFacade::createUserPhoneIndex();
    }

    /**
     * 创建相册索引
     */
    protected function createEsAlbumIndex()
    {
        //创建相册索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Album\EsCreateAlbumIndexFacade::createAlbumsIndex();
        //创建相册图片索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Album\EsCreateAlbumIndexFacade::createAlbumPicturesIndex();
    }

    /**
     * 创建文章索引
     */
    protected function createdEsArticleIndex()
    {
        \App\Facades\LaravelFastApi\V1\Es\Index\Article\EsCreateArticleIndexFacade::createArticlesIndex();
    }

    /**
     * 创建日志索引
     */
    protected function createEsLogIndex()
    {
        //创建微服务APi通信事件日志索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateApiLogIndexFacade::createApiEventLogsIndex();
        //创建用户事件日志索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateUserLogIndexFacade::createUserEventLogsIndex();
        //登录日志索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateUserLogIndexFacade::createUserLoginLogsIndex();
        //创建管理员事件日志索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateAdminLogIndexFacade::createAdminEventLogsIndex();
        //创建管理员登录日志索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateAdminLogIndexFacade::createAdminLoginLogsIndex();
        //创建用户金额日志索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateUserLogIndexFacade::createUserAmountLogsIndex();
        //创建用户系统币日志索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateUserLogIndexFacade::createUserCoinLogsIndex();
        //创建用户积分日志索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateUserLogIndexFacade::createUserScoreLogsIndex();
        //创还用户实名认证
        \App\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateUserLogIndexFacade::createUserRealAuthLogsIndex();
        //创建用户位置日志
        \App\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateUserLogIndexFacade::createUserLocationLogsIndex();
    }

    /**
     * 创建关联索引
     */
    protected function createEsUnionIndex()
    {
        //角色和权限关联索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade::createRolePermissionUnionsIndex();
        //用户和角色关联索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade::createUserRoleUnionsIndex();
        //用户父关联索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade::createUserSourceUnionsIndex();
		//用户和微信unionid关联索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade::createUserWechatUnionidIndex();
        //用户和抖音配置关联索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade::createUserSystemDouYinConfigUnionsIndex();
        //用户和微信配置关联索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade::createUserSystemWechatConfigUnionsIndex();
        //商品分类关联索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade::createGoodsClassUnionsIndex();
        //商品标签关联索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade::createGoodsLabelUnionsIndex();
        //文章分类关联索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade::createArticleCategoryUnionsIndex();
        //文章标签关联索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade::createArticleLabelUnionsIndex();
        //用户和系统级别配置索引
        \App\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade::createUserlevelItemlUnionsIndex();
    }
}
