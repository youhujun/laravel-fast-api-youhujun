<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-28 00:00:00
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-28 00:00:00
 * @FilePath: \app\Models\LaravelFastApi\V1\System\SystemConfig\WithdrawConfig.php
 */

namespace App\Models\LaravelFastApi\V1\System\SystemConfig;

use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WithdrawConfig extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithCustomConnection;

    protected $fillable = ['item_name', 'item_value', 'value_type','note', 'sort', 'created_time', 'updated_time','created_at','updated_at','deleted_at'];
    protected $table = 'system_withdraw_configs';
    protected $primaryKey = 'id';
    public $incrementing = true;
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
}
