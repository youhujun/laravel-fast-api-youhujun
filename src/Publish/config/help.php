<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-02-08 22:55:47
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-15 01:35:31
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use YouHuJun\Tool\App\Facades\V1\Utils\Snowflake\SnowflakeFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use Illuminate\Support\Facades\Redis;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Exceptions\Common\CommonException;

if (!function_exists('p')) {
    /**
     * @description: 打印函数
     * @param {mixed}
     * @return: void
     */
    function p($param): void
    {
        echo "<pre>";
        print_r($param);
        echo "</pre>";
    }
}

//生成短信验证码
if (!function_exists('init_number_code')) {
    function init_number_code(): string
    {
        $code = '';

        for ($i = 0; $i < 4 ; $i++) {
            $code .= \mt_rand(0, 9);
        }

        return $code;
    }
}

// 定义全局微服务配置辅助方法
if (!function_exists('get_shard_config_key')) {
    /**
     * 获取当前微服务配置键（全局助手函数风格：snake_case）
     * @return string youhu|youhushop|xuehu|youhujun
     */
    function get_shard_config_key(): string
    {
        if (config('youhu.is_ms')) {
            return 'youhu';
        } elseif (config('xuehu.is_ms')) {
            return 'xuehu';
        } elseif (config('youhushop.is_ms')) {
            return 'youhushop';
        }
        return 'youhujun';
    }
}

if (!function_exists('get_machine_id')) {
    /**
     * 获取机器ID
     *
     * @return string
     */
    function get_machine_id(): string
    {
        $machineId = config('youhujun.snowflake_machine_id');

        if (config('youhu.is_ms')) {
            $machineId = config('youhu.snowflake_machine_id');
        } elseif (config('youhushop.is_ms')) {
            $machineId = config('youhushop.snowflake_machine_id');
        } elseif (config('xuehu.is_ms')) {
            $machineId = config('xuehu.snowflake_machine_id');
        }

        return $machineId;
    }
}

if (!function_exists('get_snow_flake_id')) {
    function get_snow_flake_id(): int
    {
        $machineId = get_machine_id();
        $uid  = SnowflakeFacade::id($machineId);
        return $uid;
    }
}

if (!function_exists('get_shard_table_count')) {
    function get_shard_table_count(): int
    {
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        if (config('youhu.is_ms')) {
            $tableCount = Config::get('youhu.shard.table_count', 1);
        } elseif (config('youhushop.is_ms')) {
            $tableCount = Config::get('youhushop.shard.table_count', 1);
        } elseif (config('xuehu.is_ms')) {
            $tableCount = Config::get('xuehu.shard.table_count', 1);
        }

        return $tableCount;
    }
}

if (!function_exists('get_shard_cache_db')) {
    function get_shard_cache_db(): int
    {
        $db = Config::get('youhujun.shard.cache.db', 3);

        if (config('youhu.is_ms')) {
            $db = Config::get('youhu.shard.cache.db', 3);
        } elseif (config('youhushop.is_ms')) {
            $db = Config::get('youhushop.shard.cache.db', 3);
        } elseif (config('xuehu.is_ms')) {
            $db = Config::get('xuehu.shard.cache.db', 3);
        }

        return $db;
    }
}

if (!function_exists('get_shard_cache_prefix')) {
    function get_shard_cache_prefix(): string
    {
        $prefix = Config::get('youhujun.shard.cache.prefix', 'youhujun_');

        if (config('youhu.is_ms')) {
            $prefix = Config::get('youhu.shard.cache.prefix', 'youhu_');
        } elseif (config('youhushop.is_ms')) {
            $prefix = Config::get('youhushop.shard.cache.prefix', 'youhushop_');
        } elseif (config('xuehu.is_ms')) {
            $prefix = Config::get('xuehu.shard.cache.prefix', 'xuehu_');
        }

        return $prefix;
    }
}

if (!function_exists('get_shard_cache_enable')) {
    function get_shard_cache_enable(): bool
    {
        $globalEnable = Config::get('youhujun.shard.cache.enable', false);

        if (config('youhu.is_ms')) {
            $globalEnable = Config::get('youhu.shard.cache.enable', false);
        } elseif (config('youhushop.is_ms')) {
            $globalEnable = Config::get('youhushop.shard.cache.enable', false);
        } elseif (config('xuehu.is_ms')) {
            $globalEnable = Config::get('xuehu.shard.cache.enable', false);
        }

        return $globalEnable;
    }
}

if (!function_exists('get_system_user_account_name')) {
    function get_system_user_account_name(): array
    {
        return ['develop','super','admin','user'];
    }
}

if (!function_exists('get_cover_album_picture_uid')) {
    /**
     * 获取封面相册图片UID
     */
    function get_cover_album_picture_uid(): string
    {
        $redisKey = config('common_redis.default_cover_uid.key');

        $cover_album_picture_uid = Redis::get($redisKey);

        if (!$cover_album_picture_uid) {
            $indexName = config('common_es.indices.album.album_pictures');

            $esAlbumObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('picture_tag', 'cover')->get()->first();

            if (isset($esAlbumObject->album_picture_uid)) {
                $cover_album_picture_uid = $esAlbumObject->album_picture_uid;

                Redis::set($redisKey, $cover_album_picture_uid);
            }
        }

        return $cover_album_picture_uid;
    }
}

if (!function_exists('get_avatar_album_picture_uid')) {
    /**
     * 获取头像相册图片UID
     *
     * @return string
     */
    function get_avatar_album_picture_uid(): string
    {
        $redisKey = config('common_redis.default_avatar_uid.key');

        $avatar_album_picture_uid = Redis::get($redisKey);

        if (!$avatar_album_picture_uid) {
            $indexName = config('common_es.indices.album.album_pictures');

            $esAlbumObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('picture_tag', 'avatar')->get()->first();

            if (isset($esAlbumObject->album_picture_uid)) {
                $avatar_album_picture_uid = $esAlbumObject->album_picture_uid;

                Redis::set($redisKey, $avatar_album_picture_uid);
            }
        }

        return $avatar_album_picture_uid;
    }
}

if (!function_exists('get_system_album_uid')) {
    //获取系统默认相册
    function get_system_album_uid(): string
    {
        $redisKey = config('common_redis.system_album_uid.key');
        $redisField = config('common_redis.system_album_uid.field');

        $album_uid = Redis::hget($redisKey, $redisField.'config');

        if (!$album_uid) {
            $indexName = config('common_es.indices.album.albums');

            $esAlbumObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('album_type', 0)->where('is_default', 1)->where('album_name', 'config')->where('is_system', 1)->get()->first();

            if (!isset($esAlbumObject->album_uid)) {
                throw new CommonException('FindSystemDefaultAlbumError');
            }

            $album_uid = $esAlbumObject->album_uid;

            Redis::hset($redisKey, $redisField.'config', $album_uid);
        }

        return $album_uid;
    }
}

if (!function_exists('get_admin_album_uid')) {
    //获取管理员默认相册
    function get_admin_album_uid(string|int $admin_uid): string
    {
        $redisKey = config('common_redis.admin_album_uid.key');
        $redisField = config('common_redis.admin_album_uid.field');

        $album_uid = Redis::hget($redisKey, $redisField.$admin_uid);

        if (!$album_uid) {
            $indexName = config('common_es.indices.album.albums');

            $esAlbumObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('album_type', 10)->where('is_default', 1)->where('admin_uid', $admin_uid)->get()->first();

            if (!isset($esAlbumObject->album_uid)) {
                throw new CommonException('ThisUserHasNoDefaultAlbumError');
            }

            $album_uid = $esAlbumObject->album_uid;

            Redis::hset($redisKey, $redisField.$admin_uid, $album_uid);
        }

        return $album_uid;
    }
}

if (!function_exists('get_user_album_uid')) {
    //获取用户默认相册
    function get_user_album_uid(string|int $user_uid): string
    {
        $redisKey = config('common_redis.user_album_uid.key');
        $redisField = config('common_redis.user_album_uid.field');

        $album_uid = Redis::hget($redisKey, $redisField.$user_uid);

        if (!$album_uid) {
            $indexName = config('common_es.indices.album.albums');

            $esAlbumObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('album_type', 20)->where('is_default', 1)->where('user_uid', $user_uid)->get()->first();

            if (!isset($esAlbumObject->album_uid)) {
                throw new CommonException('ThisUserHasNoDefaultAlbumError');
            }

            $album_uid = $esAlbumObject->album_uid;

            Redis::hset($redisKey, $redisField.$user_uid, $album_uid);
        }

        return $album_uid;
    }
}

if (!function_exists('get_user_openid')) {
    function get_user_openid(string|int $user_uid): string
    {
        $redisKey = config('common_redis.user_openid.key');
        $redisField = config('common_redis.user_openid.field');

        $openid = Redis::hget($redisKey, $redisField.$user_uid);

        if (!$openid) {
            $indexName = config('common_es.indices.union.user_system_wechat_config_unions');

            $esUserSystemWechatConfigUnionObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid', $user_uid)->get()->first();

            if (!isset($esUserSystemWechatConfigUnionObject->openid)) {
                throw new CommonException('ThisUserHasNotOpendidError');
            }

            $openid = $esUserSystemWechatConfigUnionObject->openid;

            Redis::hset($redisKey, $redisField.$user_uid, $openid);
        }

        return $openid;
    }
}

if (!function_exists('get_user_unionid')) {
    function get_user_unionid(string|int $user_uid): string
    {
        $redisKey = config('common_redis.user_unionid.key');
        $redisField = config('common_redis.user_unionid.field');

        $unionid = Redis::hget($redisKey, $redisField.$user_uid);

        if (!$unionid) {
            $indexName = config('common_es.indices.union.user_wechat_unionids');

            $esUserWechatUnionidObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid', $user_uid)->get()->first();

            if (!isset($esUserWechatUnionidObject->unionid)) {
                throw new CommonException('ThisUserHasNotUnionidError');
            }

            $unionid = $esUserWechatUnionidObject->unionid;

            Redis::hset($redisKey, $redisField.$user_uid, $unionid);
        }

        return $unionid;
    }
}

//获取用户级别
if (!function_exists('get_user_level')){
    function get_user_level(string|int $user_uid):string
    {
        $redisKey = config('common_redis.user_level.key');
        $redisField = config('common_redis.user_level.field');

        $user_level = Redis::hget($redisKey, $redisField.$user_uid);

        if(!$user_level){
            $indexName = config('common_es.indices.user.users');

            $esUserObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid', $user_uid)->get()->first();

            if (!isset($esUserObject->user_uid)) {
                throw new CommonException('EsUserFindError');
            }

            $level_id = $esUserObject->level_id;

            $userLevelIndexName = config('common_es.indices.business.user_levels');

            $esUserLevelObject = EsQueryFacade::index($userLevelIndexName)->whereNull('deleted_at')->where('id', $level_id)->get()->first();

            $user_level = $esUserLevelObject?->level_name;

            if(!$user_level){
                $user_level = '暂无级别';
            }

            Redis::hset($redisKey, $redisField.$user_uid, $user_level);

        }

        return $user_level;
    }

}

//获取用户角色
if (!function_exists('get_es_user_roles')){
    function get_es_user_roles(string|int $user_uid,int $type = 0):array
    {
        $redisKey = config('common_redis.user_level.key');
        $redisField = config('common_redis.user_level.field');

        $rolesArray = [];

        $indexName = config('common_es.indices.union.user_role_unions');

        $esUserRoleUnionCollection = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('user_uid', $user_uid)->get();

        $role_id_array = $esUserRoleUnionCollection->pluck('role_id')->toArray();

        $roleIndexName = config('common_es.indices.system.roles');

        $esRoleQeury= EsQueryFacade::index($roleIndexName)->whereNull('deleted_at')->whereIn('id', $role_id_array);

        if(!$type){
            $esRoleColleciton = $esRoleQeury->get();
        }else{
            $esRoleColleciton = $esRoleQeury->where('type', $type)->get();
        }
        
        if($esRoleColleciton->count()){
            $rolesArray = $esRoleColleciton->pluck('role_name')->toArray();
        }

        return $rolesArray;
    }

}

return [
    'youhujun_custom' => env('YOUHUJUN_IS_CUSTOM', false),
    'shard_map_custom' => env('SHARD_MAP_IS_CUSTOM', false),
    'youhu_custom' => env('YOUHU_IS_CUSTOM', false),
    'youhushop_custom' => env('YOUHUSHOP_IS_CUSTOM', false),
    'xuehu_custom' => env('XUEHU_IS_CUSTOM', false),
];
