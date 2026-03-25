<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-05-30 23:14:35
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-25 02:19:32
 */

namespace YouHuJun\LaravelFastApi\App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Facades\Common\V1\Shard\ShardHelperFacade;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //dd($this->app['config']);die;

        $this->app->make('config')->set('auth.providers.user.model', User::class);
        $this->app->make('config')->set('auth.providers.admin.model', Admin::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        //自定义闭包guard 对应 api_token 后台
        Auth::viaRequest('Admin-Token', function ($request) {
            $api_token = $request->header('X-Token');

            $adminObject = null;

            if ($api_token) {
                //先从redis 缓存获取
                $adminObject = $this->getAdminUserByToken($api_token);

                if (empty($adminObject) || !isset($adminObject)) {
                    //如果缓存不存在 表示用户已经退出了,重新从数据库获取
                    $adminObject = ShardHelperFacade::queryAllShards(
                        Admin::class,
                        function ($query) {
                            $query->where('remember_token', $api_token);
                        },
                        'remember_token',
                        [$api_token]
                    )->first();
                }
            }

            return $adminObject;
        });

        //自定义闭包guard  phone_token 手机端
        Auth::viaRequest('Phone-Token', function ($request) {
            $api_token = $request->header('X-Token');

            $userObject = null;

            if ($api_token) {
                //先从redis 缓存获取 用户
                $userObject = $this->getPhoneUserByToken($api_token);

                if (empty($userObject) || !isset($userObject)) {
                    //如果缓存不存在 表示用户已经退出了,重新从数据库获取
                    $userObject = ShardHelperFacade::queryAllShards(
                        User::class,
                        function ($query) {
                            $query->where('remember_token', $api_token);
                        },
                        'remember_token',
                        [$api_token]
                    )->first();
                }
            }

            return $userObject;
        });
    }

    /**
     * 通过token 获取redis后台用户
     *
     * @param [type] $remember_token
     * @return void
     */
    public function getAdminUserByToken($remember_token): ?Admin
    {
        $adminObject = null;

        $redisAdminTokenKey = config('common_redis.admin_token.key');
        $redisAdminKey = config('common_redis.admin.key');
        $redisAdminField = config('common_redis.admin.field');

        $existsResult = Redis::exists($redisAdminTokenKey.$remember_token);

        if ($existsResult) {
            $admin_uid = Redis::get($redisAdminTokenKey.$remember_token);

            if ($admin_uid  && \is_numeric($admin_uid)) {
                $redisAdminJsonString = Redis::hget($redisAdminKey, $redisAdminField.$admin_uid);

                if (isset($redisAdminJsonString) && !empty($redisAdminJsonString)) {
                    $adminObject = \unserialize($redisAdminJsonString);
                }
            }
        }

        return $adminObject;
    }

    /**
     * 通过token 获取redis手机用户
     *
     * @param [type] $remember_token
     * @return void
     */
    public function getPhoneUserByToken($remember_token): ?User
    {
        $userObject = null;

        $redisUserTokenKey = config('common_redis.user_token.key');
        $redisUserKey = config('common_redis.user.key');
        $redisUserField = config('common_redis.user.field');


        $existsResult = Redis::exists($redisUserTokenKey.$remember_token);

        if ($existsResult) {
            $user_uid = Redis::get($redisUserTokenKey.$remember_token);

            if ($user_uid  && \is_numeric($user_uid)) {
                $redisUserJsonString = Redis::hget($redisUserKey, $redisUserField.$user_uid);

                if (isset($redisUserJsonString) && !empty($redisUserJsonString)) {
                    $userObject = \unserialize($redisUserJsonString);
                }
            }
        }

        return $userObject;
    }
}
