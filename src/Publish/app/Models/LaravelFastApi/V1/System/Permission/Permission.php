<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-22 14:34:42
 * @LastEditors: YouHuJun
 * @LastEditTime: 2022-12-01 17:58:21
 */

namespace App\Models\LaravelFastApi\V1\System\Permission;

use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\LaravelFastApi\V1\System\Role\Role;

class Permission extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithCustomConnection;

    protected $fillable = ['revision', 'parent_id', 'permission_name', 'permission_code', 'permission_type', 'always_show', 'hidden', 'component', 'redirect', 'path', 'name', 'title', 'icon', 'no_cache', 'breadcrumb', 'affix', 'sort', 'created_time', 'updated_time'];
    protected $hidden = ['revision'];
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

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
     *  对应 权限相对角色  多对多
     *
     * @return void
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission_unions', 'permission_id', 'role_id');
    }
}
