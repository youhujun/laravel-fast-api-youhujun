<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-22 14:34:42
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-06 12:56:25
 */

namespace App\Models\LaravelFastApi\V1\System\Role;

use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\System\Permission\Permission;

class Role extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithCustomConnection;

    protected $fillable = ['revision','parent_id','deep','switch','is_system','type','role_name', 'logic_name', 'sort', 'created_time', 'updated_time','created_at','updated_at','deleted_at'];
    protected $hidden = ['revision'];
    protected $table = 'roles';
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


    //================================================相对分割线===========================================================

    /**
     * 定义 角色和权限  多对多
     *
     * @return void
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission_unions', 'role_id', 'permission_id')->wherePivot('deleted_at', null);
    }

    /**
     * 对应 角色和用户 多对多
     *
     * @return void
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role_unions', 'role_id', 'user_uid');
    }

    /**
     * 定义角色和管理员相对的多对多关联
     *
     * @return void
     */
    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_role_unions', 'role_id', 'admin_uid');
    }
}
