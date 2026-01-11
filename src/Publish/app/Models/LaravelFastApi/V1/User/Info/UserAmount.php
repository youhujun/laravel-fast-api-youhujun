<?php

namespace App\Models\LaravelFastApi\V1\User\Info;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UserAmount extends Model
{
    use HasFactory,Notifiable,SoftDeletes;

	//链接
	protected $connection = 'mysql';
	//表名
    protected $table = 'user_amount';
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

	protected function amount():Attribute
    {
        return new Attribute(

            get:fn($value) => number_format($value,2),
        );
    }

	protected function coin():Attribute
    {
        return new Attribute(

            get:fn($value) => number_format($value,2),
        );
    }

	protected function score():Attribute
    {
        return new Attribute(

            get:fn($value) => number_format($value,2),
        );
    }

	protected function bonus():Attribute
    {
        return new Attribute(

            get:fn($value) => number_format($value,2),
        );
    }

	protected function prepareBonus():Attribute
    {
        return new Attribute(

            get:fn($value) => number_format($value,2),
        );
    }

}
