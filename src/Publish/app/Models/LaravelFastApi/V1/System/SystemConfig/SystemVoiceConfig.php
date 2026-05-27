<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-28 00:00:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-05 12:17:53
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\System\SystemConfig\SystemVoiceConfig.php
 */

namespace App\Models\LaravelFastApi\V1\System\SystemConfig;

use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemVoiceConfig extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;

    protected $fillable = ['revision','voice_title', 'channle_name', 'channle_event', 'note', 'voice_save_type', 'voice_url','voice_path','voice_file', 'sort', 'created_time', 'updated_time','created_at','updated_at','deleted_at'];
    protected $table = 'system_voice_configs';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

	/**
     * 时间戳格式
     */
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
