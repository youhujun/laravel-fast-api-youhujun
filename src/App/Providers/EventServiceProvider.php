<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-05-30 23:14:35
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-10 23:25:16
 */

namespace YouHuJun\LaravelFastApi\App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
   
    protected $listener = [
        //=====================后台=======================
        
		 //======================手机======================
    ];

    protected $subscribe = [
      //=====================后台=======================
     
	 //======================手机======================

    ];


	//通用
	protected $publishCommonListener;

	//后台
	protected $publishAdminListener;
	//手机
	protected $publishPhoneListener;

	//默认
    protected $publishListener = [

    ];

    protected $publishSubscribe = [

    ];
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if(config('youhujun.runing'))
        {

			//先设置
			//通用设置
			$this->setCommonPublish();
			//后台发布
			$this->setAdminPublish();
			//手机发布
			$this->setPhonePublish();

            $this->runPublish();

			$this->runCommonPublish();
			//后台发布
			$this->runAdminPubish();
			//手机发布
			$this->runPhonePublish();
        }
        else
        {
            $this->run();
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * 组件包运行
     *
     * @return void
     */
    protected function run()
    {
        $events = $this->listener;

        foreach ($events as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                Event::listen($event, $listener);
            }
        }

        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }

    /**
     * 发布后的项目运行
     *
     * @return void
     */
    protected function runPublish()
    {
        $events = $this->publishListener;

        foreach ($events as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                Event::listen($event, $listener);
            }
        }

        foreach ($this->publishSubscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }

	/**
	 * 注册通用监听事件
	 */
	protected function runCommonPublish()
	{
		$events = $this->publishCommonListener;

        foreach ($events as $event => $listeners) 
		{
            foreach (array_unique($listeners) as $listener) 
			{
                Event::listen($event, $listener);
            }
        }
	}
	
	/**
	 * 注册后台监听事件
	 */
	protected function runAdminPubish()
	{
		$events = $this->publishAdminListener;

        foreach ($events as $event => $listeners) 
		{
            foreach (array_unique($listeners) as $listener) 
			{
                Event::listen($event, $listener);
            }
        }
	}

	/**
	 * 注册手机监听事件
	 */
	protected function runPhonePublish()
	{
		$events = $this->publishPhoneListener;

        foreach ($events as $event => $listeners) 
		{
            foreach (array_unique($listeners) as $listener) 
			{
                Event::listen($event, $listener);
            }
        }
	}


	/**
	 * 设置通用发布
	 */
	protected function setCommonPublish()
	{
		$publishCommonListener = [
			//通用的用户注册(添加用户)
			\App\Events\Common\V1\User\User\CommonUserRegisterEvent::class => [
				 \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserInfoListener::class,
				 \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAlbumListener::class,
				 \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAvatarListener::class,
				 \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserRoleListener::class,
				 \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAmountListener::class,
				 \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserQrcodeListener::class,
				 \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserSourceListener::class
			],
		];

		$this->publishCommonListener = $publishCommonListener;
	}

	/**
	 * 设置后台发布
	 */
	protected function setAdminPublish()
	{
		//公共事件日志
		$publishAdminListener = [
			\App\Events\Admin\CommonEvent::class => [
				\App\Listeners\Admin\CommonEvent\CommonEventListener::class
			], 
		];

		//后台事件监听
		$publishAdminSystemListener = [
			//管理员登录 
			\App\Events\LaravelFastApi\V1\Admin\Login\AdminLoginEvent::class =>[
				\App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent\AdminLoginLogListener::class,
				\App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent\CacheAdminRolesListener::class,
			],
			//管理员退出
			\App\Events\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent::class=>[
				\App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent\AdminLogoutLogLitener::class,
				\App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent\ClearAdminCacheListener::class
			],
			
			//添加开发者
			\App\Events\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent::class =>[
				//后台管理员
				\App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperAdminListener::class,
				//用户相册
				\App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperAdminAlbumListener::class,
				//用户地址
				\App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserAddressListener::class,
				//用户头像
				\App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserAvatarListener::class,
				//用户详情
				\App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserinfoListener::class,
				//用户二维码
				\App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserQrcodeListener::class,
				//用户角色
				\App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserRoleListener::class,
			],
			//|--后台系统
			//|--|--上传文件
			\App\Events\LaravelFastApi\V1\Admin\File\UploadFileEvent::class => [
				\App\Listeners\LaravelFastApi\V1\Admin\File\UploadFileEvent\UploadFileLogListener::class,
				\App\Listeners\LaravelFastApi\V1\Admin\File\UploadFileEvent\UploadFileImportListener::class
			],
		];

		//后台管理-用户事件
		$publishAdminUserListener = [
			//|--|--管理员管理
			//添加管理员
			\App\Events\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent::class=>[
				//添加管理员相册
				\App\Listeners\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent\AddAdministratorAlbumListener::class,
				//添加管理员角色
				\App\Listeners\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent\AddAdministratorRoleListener::class
			],
			//更新管理员
			\App\Events\LaravelFastApi\V1\Admin\User\Admin\UpdateAdministratorEvent::class=>[
				\App\Listeners\LaravelFastApi\V1\Admin\User\Admin\UpdateAdministratorEvent\UpdateAdministratorRoleListener::class
			],
			
			
			//后台设置用户身份证
			\App\Events\LaravelFastApi\V1\Admin\User\User\SetUserIdCardEvent::class => [
				//添加用户实名认证申请
				\App\Listeners\LaravelFastApi\V1\Admin\User\User\SetUserIdCardEvent\AddUserRealAuthApplyListener::class
			],
			
			//后台审核实名认证用户
			\App\Events\LaravelFastApi\V1\Admin\User\User\CheckUserRealAuthEvent::class => [
				//更新用户实名认证申请状态
				\App\Listeners\LaravelFastApi\V1\Admin\User\User\CheckUserRealAuthEvent\UpdateUserRealAuthApplyListener::class
			],

			//后台设置用户账户
			\App\Events\LaravelFastApi\V1\Admin\User\User\SetUserAccountEvent::class => [
				\App\Listeners\LaravelFastApi\V1\Admin\User\User\SetUserAccountEvent\AddUserAccountLogListener::class
			],

			//后台生成用户二维码
			\App\Events\LaravelFastApi\V1\Admin\User\User\MakeUserQrcodeEvent::class => [
				\App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserQrcodeListener::class
			]
		];

		//后台管理-文航管理
		$publisAdminArticleListener = [
			//|--文章管理
			//添加文章
			\App\Events\LaravelFastApi\V1\Admin\Article\AddArticleEvent::class => [
				\App\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent\AddArticleInfoListener::class,
				\App\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent\AddArticleCategoryUnionListener::class,
				\App\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent\AddArticleLabelUnionListener::class
			],
			//更新文章
			\App\Events\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent::class => [
				\App\Listeners\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent\UpdateArticleInfoListener::class,
				\App\Listeners\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent\UpdateArticleCategoryUnionListener::class,
				\App\Listeners\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent\UpdateArticleLabelUnionListener::class
			],
		];

		

		$this->publishAdminListener = array_merge($publishAdminListener,$publishAdminSystemListener,$publishAdminUserListener,$publisAdminArticleListener);
	}

	/**
	 * 设置手机发布
	 */
	protected function setPhonePublish()
	{
		//手机端
		$publishPhoneListener = [
			\App\Events\Phone\CommonEvent::class =>[
				\App\Listeners\Phone\CommonEvent\CommonEventListener::class
			],
		];
		//手机用户
		$publishPhoneUserListener = [
			//用户申请实名认证
			\App\Events\LaravelFastApi\V1\Phone\User\UserApplyRealAuthEvent::class => [
				//更新用户实名认证申请状态
				\App\Listeners\LaravelFastApi\V1\Phone\User\UserApplyRealAuthEvent\UpdateUserRealAuthStatusListener::class,
				//更新用户信息
				\App\Listeners\LaravelFastApi\V1\Phone\User\UserApplyRealAuthEvent\UpdateUserInfoListener::class,
				//添加用户身份证
				\App\Listeners\LaravelFastApi\V1\Phone\User\UserApplyRealAuthEvent\AddUserIdCardListener::class,
			],
		];

		//手机登录注册
		$publishLoginListener = [
			//手机登录
			\App\Events\LaravelFastApi\V1\Phone\User\UserLoginEvent::class=>[
				//添加手机登录日志
				\App\Listeners\LaravelFastApi\V1\Phone\User\UserLoginEvent\AddPhoneUserLogListener::class
			],

			//手机退出
			\App\Events\LaravelFastApi\V1\Phone\User\UserLogoutEvent::class => [
				//添加手机退出日志
				\App\Listeners\LaravelFastApi\V1\Phone\User\UserLogoutEvent\AddPhoneUserLogListener::class,
				//清除用户缓存
				\App\Listeners\LaravelFastApi\V1\Phone\User\UserLogoutEvent\ClearPhoneUserCacheListener::class,
			],

			//用户注册
			\App\Events\LaravelFastApi\V1\Phone\User\UserRegisterEvent::class =>[
				//用户详情 
				\App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserInfoListener::class,
				//用户相册
				\App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserAlbumListener::class,
				//用户头像
				\App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserAvatarListener::class,
				//用户角色
				\App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserRoleListener::class,
				//用户账户
				\App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserAmountListener::class,
				//用户二维码
				\App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserQrcodeListener::class,
				//用户推荐人
				\App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserSourceListener::class,

			],
		];

		//用户位置
		$publishPhoneUserLocationListener = [
			//用户位置记录
			\App\Events\LaravelFastApi\V1\Phone\User\Location\UserLocationLogEvent::class =>[
				//添加用户位置记录
				\App\Listeners\LaravelFastApi\V1\Phone\User\Location\UserLocationLogEvent\AddUserLocationLogListener::class
			]
		];

		$this->publishPhoneListener = array_merge($publishPhoneListener,$publishPhoneUserListener,$publishLoginListener,$publishPhoneUserLocationListener);
	}
}
