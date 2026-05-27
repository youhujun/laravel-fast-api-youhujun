<?php

namespace App\Models\LaravelFastApi\V1\System\Union;

use App\Models\Traits\WithCustomConnection;
use App\Models\Traits\WithTimeStampFields;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class RolePermissionUnion extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithCustomConnection;

    protected $fillable = ['revision', 'role_id', 'permission_id', 'created_time', 'updated_time'];
    protected $hidden = ['revision'];
    protected $table = 'role_permission_unions';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
