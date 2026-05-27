<?php

/*
 * @Description: 支付行为主表
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-21 00:00:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-01 23:10:03
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\Pay\Payment.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\Pay;

use App\Models\Traits\WithShardRouting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithTimeStampFields;
use App\Models\LaravelFastApi\V1\User\User;
use Illuminate\Database\Eloquent\Builder;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;
    use WithShardRouting;

    // 实现抽象方法（替代原来的属性定义）
    public function getBaseTable(): string
    {
        return 'payments';
    }

    protected function getShardBusinessKey(): string
    {
        return 'payer_uid';
    }

    /**
     * 批量赋值字段(明确可赋值字段,杜绝安全风险)
     */
    protected $fillable = ['payment_uid', 'payer_uid', 'order_uid', 'refund_uid', 'operator_uid', 'payment_no', 'out_trade_no', 'total_amount', 'actual_paid_amount', 'fee_amount', 'pay_channel', 'pay_sub_channel', 'revision', 'status', 'fail_type', 'fail_reason', 'data_source', 'callback_data', 'extend_params', 'created_at', 'created_time', 'updated_at', 'updated_time', 'deleted_at', 'paid_at', 'closed_at'];

    /**
     * 隐藏敏感/无用字段
     */
    protected $hidden = ['id', 'revision'];

    /**
     * 表名
     */
    protected $table = 'payments';

    /**
     * 主键
     */
    protected $primaryKey = 'payment_uid';

    /**
     * 雪花ID非自增
     */
    public $incrementing = false;

    /**
     * 雪花ID是字符串类型
     */
    protected $keyType = 'string';

    /**
     * 开启自动时间戳
     */
    public $timestamps = true;

    /**
     * 时间戳格式
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * 类型转换
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
            'paid_at' => 'datetime:Y-m-d H:i:s',
            'closed_at' => 'datetime:Y-m-d H:i:s',
            'total_amount' => 'decimal:2',
            'actual_paid_amount' => 'decimal:2',
            'fee_amount' => 'decimal:2',
            'callback_data' => 'array',
            'extend_params' => 'array',
        ];
    }

    /**
     * 关联支付人
     */
    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_uid', 'user_uid');
    }

}
