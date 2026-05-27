<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-26 08:50:55
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-28 17:29:57
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\User\Union\UserSourceUnion.php
 */

namespace App\Models\LaravelFastApi\V1\User\Union;

use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithShardRouting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\LaravelFastApi\V1\User\User;

class UserSourceUnion extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;
    use WithShardRouting;

    protected $fillable = ['user_source_union_uid','shard_key','user_uid', 'first_uid', 'second_uid', 'revision', 'sort', 'created_at', 'created_time', 'updated_at', 'updated_time','deleted_at'];
    protected $hidden = ['id', 'revision'];
    protected $table = 'user_source_unions';
    protected $primaryKey = 'user_source_union_uid';
    public $incrementing = false;
    protected $keyType = 'string';
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

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * 获取基础表名
     * @return string
     */
    public function getBaseTable(): string
    {
        return 'user_source_unions';
    }

    public function getShardBusinessKey(): string
    {
        return 'user_uid';
    }


    public function user()
    {
        $table = User::getShardTableName($this->user_uid);
        return $this->belongsTo(User::class, 'user_uid', 'user_uid')->from($table);
    }
}
