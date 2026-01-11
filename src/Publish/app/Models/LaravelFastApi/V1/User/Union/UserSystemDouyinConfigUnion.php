<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-25 15:35:51
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-12-25 15:36:50
 * @FilePath: \app\Models\User\Union\UserSystemDouyinConfigUnion.php
 */

namespace App\Models\LaravelFastApi\V1\User\Union;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UserSystemDouyinConfigUnion extends Model
{
    use HasFactory,Notifiable,SoftDeletes;

	//链接
	protected $connection = 'mysql';
	//表名
    protected $table = 'user_system_douyin_config_union';
	//主键
	protected $primaryKey = 'id';
	//是否自动维护时间戳
	public $timestamps = false;
	//时间戳格式
	protected $dateFormat = 'Y-m-d H:i:s';

    protected $guarded = [''];
    protected $attributes =
    [
    ];

    protected function createdAt():Attribute
    {
        return new Attribute(

            set:fn($time) => date('Y-m-d H:i:s',$time),
        );
    }

    protected function updatedAt():Attribute
    {
        return new Attribute(

            set:fn($time) => date('Y-m-d H:i:s',$time),
        );
    }

}
