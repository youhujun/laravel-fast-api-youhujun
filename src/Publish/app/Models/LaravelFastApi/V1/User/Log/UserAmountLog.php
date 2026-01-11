<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-04-23 11:14:02
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-03 10:08:53
 */

namespace App\Models\LaravelFastApi\V1\User\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\LaravelFastApi\V1\User\User;

class UserAmountLog extends Model
{
    use HasFactory,SoftDeletes;

    public $timestamps = false;
    protected $table = 'user_amount_log';
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

    protected function amount():Attribute
    {
        return new Attribute(

            get:fn($value) => number_format($value,2),
        );
    }

	protected function beforeAmount():Attribute
    {
        return new Attribute(

            get:fn($value) => number_format($value,2),
        );
    }

	protected function changeValue():Attribute
    {
        return new Attribute(

            get:fn($value) => number_format($value,2),
        );
    }

    /**
     * 定义 相对 一对多
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

}
