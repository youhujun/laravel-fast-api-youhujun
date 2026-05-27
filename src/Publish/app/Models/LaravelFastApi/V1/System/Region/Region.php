<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-02-21 14:59:26
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-04-06 20:59:36
 */

namespace App\Models\LaravelFastApi\V1\System\Region;

use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\LaravelFastApi\V1\User\Info\UserAddress;

class Region extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithCustomConnection;

    protected $fillable = ['revision', 'parent_id', 'deep', 'region_name', 'region_area', 'latitude','longitude', 'sort', 'created_time', 'updated_time','created_at','updated_at','deleted_at'];
    protected $hidden = ['revision'];
    protected $table = 'regions';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
	/**
     * 时间戳格式
     */
    protected $dateFormat = 'Y-m-d H:i:s';    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * 定义 地区国家对 用户地址 一对多
     *
     * @return void
     */
    public function countries()
    {
        return $this->hasMany(UserAddress::class, 'country_id', 'id');
    }

    /**
    * 定义 地区省份对 用户地址 一对多
    *
    * @return void
    */
    public function provinces()
    {
        return $this->hasMany(UserAddress::class, 'province_id', 'id');
    }

    /**
    * 定义 地区区域对 用户地址 一对多
    *
    * @return void
    */
    public function regions()
    {
        return $this->hasMany(UserAddress::class, 'region_id', 'id');
    }

    /**
     *定义 地区城市对 用户地址 一对多
     * @return void
     */
    public function cities()
    {
        return $this->hasMany(UserAddress::class, 'city_id', 'id');
    }

    /**
     * 定义 地区城镇对 用户地址 一对多
     *
     * @return void
     */
    public function towns()
    {
        return $this->hasMany(UserAddress::class, 'towns_id', 'id');
    }

    /**
    * 定义 地区乡村街道 用户地址 一对多
    *
    * @return void
    */
    public function villages()
    {
        return $this->hasMany(UserAddress::class, 'village_id', 'id');
    }
}
