<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-17 11:33:57
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-21 12:52:58
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
use App\Models\LaravelFastApi\V1\System\Region\Region;
use Illuminate\Database\Eloquent\Builder;

class UserAddress extends Model
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
        return 'user_addresses';
    }

    protected function getShardBusinessKey(): string
    {
        return 'user_uid';
    }

    /**
     * 批量赋值字段（明确可赋值字段，杜绝安全风险）
     */
    protected $fillable = [
        'user_address_uid', 'user_uid', 'revision', 'address_type', 'is_default',
        'is_top', 'address_info', 'user_name', 'phone', 'country_id', 'province_id', 'region_id', 'city_id', 'towns_id', 'village_id',
        'created_at', 'created_time', 'updated_at', 'updated_time', 'deleted_at'
    ];

    /**
     * 隐藏敏感/无用字段
     */
    protected $hidden = ['id', 'revision'];

    // 表名
    protected $table = 'user_addresses';
    // 主键
    protected $primaryKey = 'user_address_uid';
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
     * 对应 用户地址对用户 多对一 主要找到默认的,类型是家庭和公司的
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'user_uid');
    }

    /**
     * 对应 地址国家 对 地区 多对一
     */
    public function country()
    {
        return $this->belongsTo(Region::class, 'country_id', 'id');
    }

    /**
     * 对应 地址省份 对 地区 多对一
     */
    public function province()
    {
        return $this->belongsTo(Region::class, 'province_id', 'id');
    }

    /**
     * 对应 地址区域 对 地区 多对一
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    /**
     * 对应 地址城市 对 地区 多对一
     */
    public function city()
    {
        return $this->belongsTo(Region::class, 'city_id', 'id');
    }

    /**
     * 对应 地址城镇 对 地区 多对一
     */
    public function towns()
    {
        return $this->belongsTo(Region::class, 'towns_id', 'id');
    }

    /**
     * 对应 地址乡村街道 对 地区 多对一
     */
    public function village()
    {
        return $this->belongsTo(Region::class, 'village_id', 'id');
    }

}
