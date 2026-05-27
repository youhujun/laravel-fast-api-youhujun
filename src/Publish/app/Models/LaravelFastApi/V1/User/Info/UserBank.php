<?php

/*
 * @Description:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-04-20 17:09:03
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-21 14:13:44
 * @FilePath: \app\Models\LaravelFastApi\V1\User\Info\UserBank.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Models\LaravelFastApi\V1\User\Info;

use App\Models\Traits\WithShardRouting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithCustomConnection;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\System\Module\Bank;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use Illuminate\Database\Eloquent\Builder;

class UserBank extends Model
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
        return 'user_banks';
    }

    protected function getShardBusinessKey(): string
    {
        return 'user_uid';
    }

    /**
     * 批量赋值字段（明确可赋值字段，杜绝安全风险）
     */
    protected $fillable = [
        'user_bank_uid', 'user_uid', 'bank_id', 'bank_front_uid', 'bank_back_uid', 'revision', 'is_default',
        'bank_number', 'bank_account', 'bank_address', 'sort',
        'created_at', 'created_time', 'updated_at', 'updated_time', 'deleted_at'
    ];

    /**
     * 隐藏敏感/无用字段
     */
    protected $hidden = ['id', 'revision'];

    // 表名
    protected $table = 'user_banks';
    // 主键
    protected $primaryKey = 'user_bank_uid';
    // 雪花ID非自增
    public $incrementing = false;
    // 雪花ID是字符串类型
    protected $keyType = 'string';
    // 开启自动时间戳（Laravel自动维护created_at/updated_at）
    public $timestamps = true;

    // 时间戳格式
    protected $dateFormat = 'Y-m-d H:i:s';

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    //================================================相对分割线===========================================================

    /**
     * 定义 相对 一对多 用户对银行卡
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'user_uid');
    }

    /**
     * 定义 相对 一对多 银行对用户银行卡
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }

    /**
     * 定义相对 一对一 银行卡正面
     */
    public function bankFront()
    {
        return $this->belongsTo(AlbumPicture::class, 'bank_front_uid', 'album_picture_uid');
    }

    /**
     * 定义相对 一对一 银行卡背面
     */
    public function bankBack()
    {
        return $this->belongsTo(AlbumPicture::class, 'bank_back_uid', 'album_picture_uid');
    }

}
