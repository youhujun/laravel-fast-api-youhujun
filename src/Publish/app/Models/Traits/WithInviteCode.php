<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-21 07:12:59
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-27 16:01:58
 * @FilePath: \youhu-laravel-api-12\app\Models\Traits\WithInviteCode.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Models\Traits;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

trait WithInviteCode
{
    // 邀请码字符集（剔除0/O、1/I/l/L，避免混淆）
    public const INVITE_CHARS = '23456789abcdefghjkmnpqrstuvwxyz';
    // 邀请码长度规则
    public const INVITE_MIN_LENGTH = 4;  // 最小长度
    public const INVITE_MAX_LENGTH = 7;  // 最大长度
    public const INVITE_SHORT_LIMIT = 9999;  // 4位短码上限
    public const INVITE_MID_LIMIT = 9999999; // 7位纯数字中码上限
    // Redis全局自增序列Key
    public const REDIS_INVITE_KEY = 'invite_code_global_sequence';

    /**
     * 启动邀请码自动生成逻辑
     */
    protected static function bootWithInviteCode()
    {
        // 创建模型时自动生成邀请码（未手动设置时）
        static::creating(function ($model) {
            if (empty($model->invite_code)) {
                $sequenceId = $model->getGlobalSequenceId();
                $model->invite_code = $model->idToInviteCode($sequenceId);
            }
        });
    }

    /**
     * 自增ID转邀请码（核心算法）
     * @param int $id 全局唯一自增ID
     * @return string 4-7位邀请码
     */
    private function idToInviteCode(int $id): string
    {
        $chars = self::INVITE_CHARS;
        $charLength = strlen($chars);

        // 1. 短码阶段（1-9999）：4位，不足补0
        if ($id <= self::INVITE_SHORT_LIMIT) {
            return str_pad((string)$id, self::INVITE_MIN_LENGTH, '0', STR_PAD_LEFT);
        }

        // 2. 中码阶段（10000-9999999）：5-7位纯数字
        if ($id <= self::INVITE_MID_LIMIT) {
            return (string)$id;
        }

        // 3. 长码阶段（≥10000000）：7位混合字符（进制转换）
        $code = '';
        $convertedId = $id - self::INVITE_MID_LIMIT; // 从0开始转换，避免码过长
        while ($convertedId > 0) {
            $code = $chars[$convertedId % $charLength] . $code;
            $convertedId = floor($convertedId / $charLength);
        }
        // 补前缀到7位，强制小写
        return strtolower(str_pad($code, self::INVITE_MAX_LENGTH, $chars[0], STR_PAD_LEFT));
    }

    /**
     * 获取全局唯一自增ID（Redis为主，MySQL兜底）
     * @return int 全局唯一序列ID
     */
    private function getGlobalSequenceId(): int
    {
        try {
            // Redis自增（高性能，优先使用）
            $id = Redis::incr(self::REDIS_INVITE_KEY);
            // 初始化：确保从1开始（避免0开头的邀请码）
            if ($id == 1) {
                Redis::set(self::REDIS_INVITE_KEY, 1);
            }
            return $id;
        } catch (\Exception $e) {
            // Redis异常时，用MySQL事务兜底

            $shardConfig = Config::get('youhujun.shard');
            $dbConnection = $shardConfig['default_db']; //

            return DB::connection($dbConnection)->transaction(function () use ($dbConnection) {
                $id = DB::connection($dbConnection)->table('invite_code_sequences')->insertGetId([]);
                return $id;
            });
        }
    }
}
