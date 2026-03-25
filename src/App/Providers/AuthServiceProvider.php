<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-05-30 23:14:35
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-26 01:20:58
 */

namespace YouHuJun\LaravelFastApi\App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
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
    public function boot(Request $request): void
    {
        //初始化ES
        $this->initEs();

        //注册管理员守卫
        $this->registerAdminTokenGuard();
        //注册用户守卫
        $this->registerPhoneTokenGuard();
    }

    /**
     * 初始化ES
     */
    protected function initEs(): void
    {
        EsFacade::init(
            config('common_es.host'),
            config('common_es.user'),
            config('common_es.password')
        );
    }

    /**
     * 注册管理员守卫
     */
    protected function registerAdminTokenGuard(): void
    {
        //自定义闭包guard 对应 api_token 后台
        Auth::viaRequest('Admin-Token', function ($request) {
            $api_token = $request->header('X-Token');

            $adminObject = null;

            if (! $api_token) {
                return null;
            }

            //先从redis 缓存获取
            $adminObject = $this->getRedisAdminUserByToken($api_token)
            ?? $this->getEsAdminUserByToken($api_token)
            ?? $this->getAdminUserByToken($api_token);

            return $adminObject;
        });
    }

    /**
     * 通过token 获取redis后台用户
     *
     * @param [string] $remember_token
     * @return void
     */
    protected function getRedisAdminUserByToken(string $remember_token): ?Admin
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
     * 通过Es获取管理员用户
     *
     * @param  [string]     $remember_token
     * @return Admin|null
     */
    protected function getEsAdminUserByToken(string $remember_token): ?Admin
    {
        $adminObject = null;

        $indexName = config('common_es.indices.admins');

        $queryArray = [
            'match' => ['remember_token' => $remember_token]
        ];

        $result = EsFacade::searchDoc($indexName, $queryArray);

        //p($result);

        if (isset($result) && $result['code'] == 0) {
            $adminArray = $result['data']['hits']['hits'][0]['_source'];
            //p($adminArray);
            $adminModelArray = [
                'admin_uid' => $adminArray['admin_uid'],
                'user_uid' => $adminArray['user_uid'],
                'remember_token' => $adminArray['remember_token'],
                'account_name' => $adminArray['account_name'],
                'phone' => $adminArray['phone'],
                'account_status' => $adminArray['account_status'],
            ];

            $adminObject = new Admin();
            $adminObject->fill($adminModelArray);
        }

        return $adminObject;
    }

    /**
     * 从属据库获取用户
     *
     * @param  [string]     $remember_token
     * @return Admin|null
     */
    protected function getAdminUserByToken(string $remember_token): ?Admin
    {
        $adminObject = null;

        $adminObject = ShardHelperFacade::queryAllShards(
            Admin::class,
            function ($query) use ($remember_token) {
                $query->where('remember_token', $remember_token);
            },
            'remember_token',
            [$remember_token]
        )->first();

        return $adminObject;
    }

    /**
     * 注册用户守卫
     */
    protected function registerPhoneTokenGuard(): void
    {
        //自定义闭包guard  phone_token 手机端
        Auth::viaRequest('Phone-Token', function ($request) {
            $api_token = $request->header('X-Token');

            $userObject = null;

            if (!$api_token) {
                return null;
            }
            //先从redis 缓存获取 用户
            $userObject = $this->getRedisPhoneUserByToken($api_token)
            ?? $this->getEsPhoneUserByToken($api_token)
            ?? $this->getPhoneUserByToken($api_token);

            return $userObject;
        });
    }

    /**
     * 通过token 获取redis手机用户
     *
     * @param [string] $remember_token
     * @return void
     */
    protected function getRedisPhoneUserByToken(string $remember_token): ?User
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

    /**
     * 通过es获取用户
     *
     * @param  [string]     $remember_token
     * @return User|null
     */
    protected function getEsPhoneUserByToken(string $remember_token): ?User
    {
        $userObject = null;

        $indexName = config('common_es.indices.users');

        $queryArray = [
            'match' => ['remember_token' => $remember_token]
        ];

        $result = EsFacade::searchDoc($indexName, $queryArray);

        //p($result);

        if (isset($result) && $result['code'] == 0) {
            $userArray = $result['data']['hits']['hits'][0]['_source'];
            //p($adminArray);
            $userModelArray = [
                'user_uid' => $userArray['user_uid'],
                'remember_token' => $userArray['remember_token'],
                'account_name' => $userArray['account_name'],
                'phone' => $userArray['phone'],
                'account_status' => $userArray['account_status'],
            ];

            $userObject = new User();
            $userObject->fill($userModelArray);
        }

        return $userObject;
    }

    /**
     * 从属据库获取用户
     *
     * @param  [string]    $remember_token
     * @return User|null
     */
    protected function getPhoneUserByToken(string $remember_token): ?User
    {
        $userObject = null;

        //如果缓存不存在 表示用户已经退出了,重新从数据库获取
        $userObject = ShardHelperFacade::queryAllShards(
            User::class,
            function ($query) use ($remember_token) {
                $query->where('remember_token', $remember_token);
            },
            'remember_token',
            [$remember_token]
        )->first();

        return $userObject;
    }
}
