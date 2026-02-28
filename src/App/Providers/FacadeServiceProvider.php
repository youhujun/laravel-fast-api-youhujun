<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-05-30 23:14:35
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-02-28 16:03:23
 */

namespace YouHuJun\LaravelFastApi\App\Providers;

use Illuminate\Support\ServiceProvider;

class FacadeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if (\config('youhujun.runing')) {
            //公共第三方门面
            $this->publishPubFacade();

            //通用公共门面
            $this->publishCommonFacade();

            //Api门面
            $this->publishApiFacade();
            //后台门面
            $this->publishAdminFacade();
            //手机门面
            $this->publishPhoneFacade();
            //模版
            $this->publishFacade();
        } else {
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * 公共的门面 第三方跟业务无关
     */
    protected function publishPubFacade()
    {
        //二维码
        $this->app->bind('PubQrcodeFacade', \App\Services\Facade\Pub\V1\Qrcode\PubQrcodeFacadeService::class);

        //短信
        $this->app->bind('SmsFacade', \App\Services\Facade\Pub\V1\Sms\SmsFacadeService::class);
        //腾讯云短信
        $this->app->bind('TencentSmsFacade', \App\Services\Facade\Pub\V1\Sms\TencentSmsFacadeService::class);

        //七牛云
        $this->app->bind('QiNiuFacade', \App\Services\Facade\Pub\V1\Store\QiNiuFacadeService::class);

        //抖音
        $this->app->bind('DouYinFacade', \App\Services\Facade\Pub\V1\DouYin\DouYinFacadeService::class);

        //平台 腾讯和微信
        $this->publishPubTencentWechatFacade();

        //公共门面-支付管理
        $this->publishPubPayFacade();

        //公共门面-地图位置
        $this->publishPubMapFacade();
    }

    /**
     * 腾讯和微信门面管理
     */
    protected function publishPubTencentWechatFacade()
    {
        //地图位置
        $this->app->bind('TencentMapFacade', \App\Services\Facade\Pub\V1\Map\TencentMapFacadeService::class);

        //微信H5的js支付
        $this->app->bind('WechatJsPayFacade', \App\Services\Facade\Pub\V1\Wechat\Pay\WechatJsPayFacadeService::class);

        //微信H5支付回调解密
        $this->app->bind('WechatJsPayDecryptFacade', \App\Services\Facade\Pub\V1\Wechat\Pay\WechatJsPayDecryptFacadeService::class);

        //微信公众号
        $this->app->bind('WechatOfficialFacade', \App\Services\Facade\Pub\V1\Wechat\WechatOfficialFacadeService::class);

        //微信登录
        $this->app->bind('WechatLoginFacade', \App\Services\Facade\Pub\V1\Wechat\WechatLoginFacadeService::class);
    }

    /**
     * 公共门面-支付管理
     */
    protected function publishPubPayFacade()
    {
    }

    /**
     * 公共门面-地图位置
     */
    protected function publishPubMapFacade()
    {
    }

    /**
     * 通用公共门面
     */
    protected function publishCommonFacade()
    {
        //分库分表工具门面
        $this->app->bind('ShardHelperFacade', \App\Services\Facade\Common\V1\Shard\ShardHelperFacadeService::class);
        $this->app->bind('ShardMapHelperFacade', \App\Services\Facade\Common\V1\Shard\Map\ShardMapHelperFacadeService::class);
        // API验签鉴权
        $this->app->bind('ApiAuthFacade', \App\Services\Facade\Common\V1\Api\Auth\ApiAuthFacadeService::class);

        //通用的登录门面
        $this->app->bind('CommonUserFacade', \App\Services\Facade\Common\V1\User\User\CommonUserFacadeService::class);
        //通用的用户门面
        $this->app->bind('CommonLoginFacade', \App\Services\Facade\Common\V1\Login\CommonLoginFacadeService::class);
        //公共统计
        $this->app->bind('TotalAllDataFacade', \App\Services\Facade\Common\V1\Total\TotalAllDataFacadeService::class);
        //用户之间计算距离
        $this->app->bind('CalculateUserDistanceFacade', \App\Services\Facade\Common\V1\Location\CalculateUserDistanceFacadeService::class);
    }

    /**
     * Api门面
     */
    public function publishApiFacade()
    {
        //后台登录辅助门面
        $this->app->bind('YouHuBaseAuthFacade', \App\Services\Facade\LaravelFastApi\V1\Api\YouHuAuthService\YouHuBaseAuthFacadeService::class);

        //api记录日志
        $this->app->bind('ApiLogEventFacade', \App\Services\Facade\LaravelFastApi\V1\Log\ApiLogEventFacadeService::class);
    }


    /**
     * 绑定发布的后台门面
     */
    protected function publishAdminFacade()
    {
        //后台登录辅助门面
        $this->app->bind('AdminBackgroundLoginFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Login\Common\AdminBackgroundLoginFacadeService::class);
        //后台登录管理门面
        $this->app->bind('AdminLoginFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Login\AdminLoginFacadeService::class);

        //后台路由权限
        $this->app->bind('AdminPermissionFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\System\Permission\AdminPermissionFacadeService::class);

        //后台开发者
        $this->app->bind('DeveloperFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Develop\DeveloperFacadeService::class);
        //后台个人中心
        $this->app->bind('AdminPersonalFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\User\Admin\AdminPersonalFacadeService::class);

        //|--后台管理
        //|--|--文件处理
        //|--|--|--|文件上传
        $this->app->bind('AdminUploadFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\File\AdminUploadFacadeService::class);

        //文章
        $this->app->bind('AdminArticleFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Article\AdminArticleFacadeService::class);

        //后台管理-系统设置
        $this->publishAdminSystemFacade();
        //后台管理-业务设置
        $this->publishAdminServiceFacade();
        //后台管理-用户管理
        $this->publishAdminUserFacade();
        //后台管理-图片空间
        $this->publishAdminPictureFacade();
        //后台管理-日志管理
        $this->publishAdminLogFacade();
    }

    /**
     * 发布后台管理系统设置门面
     */
    protected function publishAdminSystemFacade()
    {
        //|--|--系统设置
        //|--|--|--系统配置
        //|--|--|--|--参数配置
        $this->app->bind('AdminSystemConfigFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminSystemConfigFacadeService::class);
        //|--|--|--|--提示配置
        $this->app->bind('AdminVoiceConfigFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminVoiceConfigFacadeService::class);
        //|--|--|--|--三方平台
        //|--|--|--|--|--|--微信平台
        $this->app->bind('SystemWechatConfigFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigFacadeService::class);
        //|--|--|--|--|--|--抖音平台
        $this->app->bind('SystemDouyinConfigFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemDouyinConfigFacadeService::class);
        //|--|--|--平台配置
        //|--|--|--|--缓存设置
        $this->app->bind('AdminCacheConfigFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\System\Platform\AdminCacheConfigFacadeService::class);

        //|--|--|--|--角色管理
        $this->app->bind('AdminRoleFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\System\Role\AdminRoleFacadeService::class);

        //|--|--|--|--地区管理
        $this->app->bind('AdminRegionFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\System\Region\AdminRegionFacadeService::class);
        //|--|--|--|--银行管理
        $this->app->bind('AdminBankFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\System\Bank\AdminBankFacadeService::class);
    }

    /**
     * 发布后台管理系统业务设置门面
     */
    protected function publishAdminServiceFacade()
    {
        //|--|--业务设置
        $this->app->bind('AdminWithdrawConfigFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminWithdrawConfigFacadeService::class);

        //|--|--|--|--分类管理
        //|--|--|--|--|产品分类
        $this->app->bind('AdminGoodsClassFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminGoodsClassFacadeService::class);
        //|--|--|--|--|文章分类
        $this->app->bind('AdminCategoryFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminCategoryFacadeService::class);
        //|--|--|--|--|标签管理
        $this->app->bind('AdminLabelFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminLabelFacadeService::class);

        //|--|--|--|--级别管理
        //|--|--|--|--|--级别条件
        $this->app->bind('AdminLevelItemFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Level\AdminLevelItemFacadeService::class);
        //|--|--|--|--|--用户级别
        $this->app->bind('AdminUserLevelFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Level\AdminUserLevelFacadeService::class);

        //|--|--|--|--首页轮播
        $this->app->bind('AdminPhoneBannerFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Platform\AdminPhoneBannerFacadeService::class);
        //|--|--|----|--轮播图详情
        $this->app->bind('AdminPhoneBannerDetailsFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\AdminPhoneBannerDetailsFacadeService::class);
    }

    /**
     * 发布后台管理用户管理门面
     */
    protected function publishAdminUserFacade()
    {
        //|--|--用户管理
        //|--|--|--管理员管理
        $this->app->bind('AdministratorFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\User\Admin\AdministratorFacadeService::class);
        //|--|--|--用户管理
        $this->app->bind('AdminUserFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserFacadeService::class);
        //|--|--|--|++用户三级关系
        $this->app->bind('AdminUserSourceFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserSourceFacadeService::class);
        //|--|--|--|++用户选项
        $this->app->bind('AdminUserItemFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserItemFacadeService::class);

        //|--|--|--|++用户详情
        $this->app->bind('AdminUserDetailsFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserDetailsFacadeService::class);
        //|--|--|--|++用户地址
        $this->app->bind('AdminUserAddressFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserAddressFacadeService::class);
        //|--|--|--|++用户银行卡
        $this->app->bind('AdminUserBankFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserBankFacadeService::class);
        //|--|--|--|++用户团队
        $this->app->bind('AdminUserTeamFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserTeamFacadeService::class);
        //|--|--|--|++用户账户
        $this->app->bind('AdminUserAccountFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserAccountFacadeService::class);
        //|--|--|--|++用户实名认证
        $this->app->bind('AdminUserRealAuthFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserRealAuthFacadeService::class);
    }


    /**
     * 发布后台管理图片空间门面
     */
    protected function publishAdminPictureFacade()
    {
        //|--|--图片空间
        //|--|--|--我的相册
        $this->app->bind('AdminAlbumFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Picture\AdminAlbumFacadeService::class);

        //|--|--|--|--图片处理
        $this->app->bind('AdminPictureFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Picture\AdminPictureFacadeService::class);
    }

    /**
     * 发后台管理日志门面
     */
    protected function publishAdminLogFacade()
    {
        //后台管理
        //后台管理-日志管理
        //后台管理-日志管理-后台日志
        $this->app->bind('AdminLogFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Log\AdminLogFacadeService::class);
        //后台管理-日志管理-用户日志
        $this->app->bind('AdminUserLogFacade', \App\Services\Facade\LaravelFastApi\V1\Admin\Log\AdminUserLogFacadeService::class);
    }


    /**
     * 绑定手机端发布的门面
     */
    protected function publishPhoneFacade()
    {
        //手机端登录注册门面
        $this->publishPhoneLoginFacade();
        //手机端-websocket
        $this->publishPhoneSocketFacade();
        //手机端-系统门面
        $this->publishPhoneSystemFacade();
        //手机端-文件管理
        $this->publishPhoneFileFacade();
        //手机端-支付管理
        $this->publishPhonePayFacade();
        //手机端-用户管理
        $this->publishPhoneUserFacade();
    }

    /**
     * 手机端登录注册门面
     */
    protected function publishPhoneLoginFacade()
    {
        //手机端登录
        $this->app->bind('PhoneLoginFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\Login\PhoneLoginFacadeService::class);

        //手机端抖音登录
        $this->app->bind('PhoneLoginDouYinFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\Login\DouYin\PhoneLoginDouYinFacadeService::class);

        //手机端微信登录
        $this->app->bind('PhoneLoginWechatFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\Login\Wechat\PhoneLoginWechatFacadeService::class);

        //手机端注册
        $this->app->bind('PhoneRegisterFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\Login\PhoneRegisterFacadeService::class);
    }

    /**
     * 手机端-websocket门面
     */
    protected function publishPhoneSocketFacade()
    {
        //手机端websocket门面
        $this->app->bind('PhoneSocketFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\Websocket\PhoneSocketFacadeService::class);

        //手机端-websocket-用户门面
        $this->app->bind('PhoneSocketUserFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\Websocket\User\PhoneSocketUserFacadeService::class);
    }

    /**
     * 手机端-系统门面
     */
    protected function publishPhoneSystemFacade()
    {
        //手机地图门面
        $this->app->bind('PhoneMapFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\System\PhoneMapFacadeService::class);

        //手机地区门面
        $this->app->bind('PhoneRegionFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\System\Region\PhoneRegionFacadeService::class);

        //手机产品分类门面
        $this->app->bind('PhoneGoodsClassFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\System\GoodsClass\PhoneGoodsClassFacadeService::class);
    }

    /**
     * 手机端-文件管理
     */
    protected function publishPhoneFileFacade()
    {
        //手机端-图片处理
        $this->app->bind('PhonePictureFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\File\PhonePictureFacadeService::class);
    }

    /**
     * 手机端-支付管理
     */
    protected function publishPhonePayFacade()
    {
        //手机端-支付管理
        $this->app->bind('PhonePayFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\Pay\PhonePayFacadeService::class);

        //手机端-支付管理-微信支付
        $this->app->bind('WechatPayFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\Pay\WechatPayFacadeService::class);

        //手机端-支付回调
        $this->app->bind('PhonePayNotifyFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\Notify\PhonePayNotifyFacadeService::class);
    }

    /**
     * 手机端-用户管理
     */
    protected function publishPhoneUserFacade()
    {
        //手机用户Source门面
        $this->app->bind('PhoneUserSourceFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\User\PhoneUserSourceFacadeService::class);
        //手机端-用户门面
        $this->app->bind('PhoneUserFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\User\User\PhoneUserFacadeService::class);
        //手机端-用户配置门面
        $this->app->bind('PhoneUserConfigFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\User\User\PhoneUserConfigFacadeService::class);

        //手机端-用户-用户信息
        $this->app->bind('PhoneUserInfoFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\User\User\UserInfo\PhoneUserInfoFacadeService::class);

        // 手机端-用户-我的地址
        $this->app->bind('PhoneUserAddressFacade', \App\Services\Facade\LaravelFastApi\V1\Phone\User\User\Address\PhoneUserAddressFacadeService::class);
    }

    /**
     * 绑定发布的门面代理
     *
     * @return void
     */
    public function publishFacade()
    {
        //模板替换
        //$this->app->bind('Replace', \App\Services\Facade\ReplaceService::class);
    }


    /**
     * 绑定未发布的门面代理
     *
     * @return void
     */
    public function unPublishFacade()
    {
    }
}
