<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-02-01 09:19:06
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-21 11:38:52
 * @FilePath: d:\wwwroot\PHP\Components\Laravel\youhujun\laravel-fast-api\src\App\Providers\PolicyServiceProvider.php
 */

namespace YouHuJun\LaravelFastApi\App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

class PolicyServiceProvider extends ServiceProvider
{
	//未发布
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ]; 

	//已发布
    protected $publishPolicies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Album::class => \App\Policies\LaravelFastApi\V1\Admin\Picture\AlbumPolicy::class,
        AlbumPicture::class => \App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy::class
    ]; 
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Request $request): void
    {
        //注册授权
		$this->registerGate();
		//注册策略
        $this->registerPolicies();
    }

	/**
	 * 注册授权
	 *
	 * @return void
	 */
	public function registerGate()
    {
        $this->registerGatePublish();
    }

	
	/**
	 * 注册策略
	 *
	 * @return void
	 */
	public function registerPolicies()
    {
        if(\config('youhujun.runing'))
        {
            foreach ($this->publishPolicies as $key => $value) 
            {
                Gate::policy($key, $value);
            } 
        }
        else
        {
            foreach ($this->policies as $key => $value) 
            {
                Gate::policy($key, $value);
            } 
        }
    }

	/**
     * 注册发布角色
     *
     * @return void
     */
    protected function registerGatePublish()
    {
		//通过角色发布授权
		$this->registerGatePublishByRole();

		//通过策略发布授权
		$this->registerGatePublishByPolicy();

    }				

	/**
	 * 通过角色发布授权
	 *
	 * @return void
	 */
	protected function registerGatePublishByRole()
	{
		//后台管理员
		$this->registerAdminGatePublishByRole();
		
		//前台用户
        $this->registerUserGatePublishByRole();
	}

	/**
	 * 后台管理员通过角色发布授权
	 */
	protected function registerAdminGatePublishByRole()
	{
		Gate::define('develop-role',function(Admin $admin)
		{
           return isDevelop($admin);
        });

        //是否是开发者或者超级管理员
        Gate::define('super-role',function(Admin $admin)
		{
           return isSuper($admin);
        });

        //是否是后台管理员
        Gate::define('admin-role',function(Admin $admin)
		{
        	return isAdmin($admin);
        });

		//是否是用户
        Gate::define('user-role',function(Admin $admin)
		{
        	return isUser($admin);
        });
	}

	/**
	 * 前台用户通过角色发布授权
	 */
	protected function registerUserGatePublishByRole()
	{
		Gate::define('phone-user-role',function(User $user)
		{
			//p(isPhoneUser($user));die;
        	return isPhoneUser($user);
        });

	}



	/**
	 * 通过策略发布授权
	 *
	 * @return void
	 */
	protected function registerGatePublishByPolicy()
	{
		//注册相册模块授权 
		$this->registerGatePublishAlbum();
		//注册相册图片授权
		$this->registerGatePublishAlbumPicture();
	}

	/**
	 * 注册相册模块授权
	 *
	 * @return void
	 */
	protected function registerGatePublishAlbum()
	{
		//更新相册
		Gate::define('update-album', [\App\Policies\LaravelFastApi\V1\Admin\Picture\AlbumPolicy::class, 'update']);
		//删除相册
		Gate::define('delete-album', [\App\Policies\LaravelFastApi\V1\Admin\Picture\AlbumPolicy::class, 'delete']);
		//查看相册图片
		Gate::define('get-album-picture', [\App\Policies\LaravelFastApi\V1\Admin\Picture\AlbumPolicy::class, 'getAlbumPicture']);
	}

	/**
	 * 注册相册图片授权
	 *
	 * @return void
	 */
	protected function registerGatePublishAlbumPicture()
	{
		//替换上传图片
		Gate::define('reset-picture',[\App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy::class,'resetUpload']);

		//设置封面
		Gate::define('set-cover',[\App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy::class,'setCover']);

		//转移相册
		Gate::define('move-album',[\App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy::class,'moveAlbum']);

		//批量转移相册
		Gate::define('move-multiple-album',[\App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy::class,'moveMultipleAlbum']);

		//删除图片
		Gate::define('delete-picture',[\App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy::class,'deletePicture']);

		//批量删除图片
		Gate::define('delete-multiple-picture',[\App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy::class,'deleteMultiplePicture']);

		//修改图片名称
		Gate::define('update-picture-name',[\App\Policies\LaravelFastApi\V1\Admin\Picture\PicturePlolicy::class,'updatePictureName']);
	}
}
