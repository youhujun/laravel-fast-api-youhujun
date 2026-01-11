<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-05-23 15:35:15
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-20 19:38:35
 */

namespace App\Models\LaravelFastApi\V1\User;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Redis;

/**
 * @see \App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion
 * @see \App\Models\LaravelFastApi\V1\System\Role\Role
 */

use App\Models\LaravelFastApi\V1\Admin\Admin;

use App\Models\LaravelFastApi\V1\System\Role\Role;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;

use App\Models\LaravelFastApi\V1\System\Level\UserLevel;
use App\Models\LaravelFastApi\V1\User\Info\UserIdCard;
use App\Models\LaravelFastApi\V1\User\Info\UserBank;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\User\Info\UserQrcode;
use App\Models\LaravelFastApi\V1\User\Info\UserAddress;
use App\Models\LaravelFastApi\V1\User\Log\UserEventLog;
use App\Models\LaravelFastApi\V1\User\Log\UserLoginLog;
use App\Models\LaravelFastApi\V1\User\Log\UserAmountLog;
use App\Models\LaravelFastApi\V1\User\Log\UserCoinLog;
use App\Models\LaravelFastApi\V1\User\Log\UserScoreLog;
use App\Models\LaravelFastApi\V1\User\Log\UserLocationLog;
use App\Models\LaravelFastApi\V1\User\Log\UserRealAuthLog;

use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

use App\Models\LaravelFastApi\V1\Article\Article;

class User extends Authenticatable
{
    use HasFactory, Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   /*  protected $fillable = [
        'name',
        'email',
        'password',
    ]; */

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    /* protected $hidden = [
        'password',
        'remember_token',
    ]; */

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
   /*  protected $casts = [
        'email_verified_at' => 'datetime',
    ]; */

    //链接
	protected $connection = 'mysql';
	//表名
    protected $table = 'users';
	//主键
	protected $primaryKey = 'id';
	//是否自动维护时间戳
	public $timestamps = false;
	//时间戳格式
	protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = [''];

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

   /**
    * 定义 用户和管理员 一对一
    *
    * @return void
    */
   public function admin()
   {
       return $this->hasOne(Admin::class,'user_id','id');
   }

    /**
    * 定义 用户和身份证
    *
    * @return void
    */
   public function idCard()
   {
       return $this->hasOne(UserIdCard::class,'user_id','id');
   }

   /**
    * 定义 用户和用户信息 一对一
    *
    * @return void
    */
   public function userInfo()
   {
       return $this->hasOne(UserInfo::class,'user_id','id');
   }

   /**
    * 定义 用户和用户头像 一对多 注意要找默认的头像一个
    *
    * @return void
    */
   public function userAvatar()
   {
       return $this->hasMany(UserAvatar::class,'user_id','id');
   }

   /**
    * 定义 用户和用户二维码  一对多 注意要找默认的头像一个
    *
    * @return void
    */
   public function userQrcode()
   {
       return $this->hasMany(UserQrcode::class,'user_id','id');
   }



   /**
    * 定义 用户对用户地址 一对多 , 主要找到默认的,并且类型是 公司,家庭等
    *
    * @return void
    */
   public function userAddress()
   {
        return $this->hasMany(UserAddress::class,'user_id','id');
   }


   /**
    * 定义 用户和用户事件日志 一对多
    *
    * @return void
    */
    public function userEventLog()
    {
        return $this->hasMany(UserEventLog::class,'user_id','id');
    }

    /**
     * 定义 用户和用户登录日志 一对多
     *
     * @return void
     */
    public function userLoginLog()
    {
        return $this->hasMany(UserLoginLog::class,'user_id','id');
    }

    /**
     * 定义 用户和角色 多对多
     *
     * @return void
     */
    public function role()
    {
        //return $this->belongsToMany(Role::class,'user_role_union','user_id','role_id')->wherePivot('deleted_at', null);
        return $this->belongsToMany(Role::class,'user_role_union','user_id','role_id');
    }

    /**
     * 定义 用户和用户相册 一对多
     *
     * @return void
     */
    public function album()
    {
        return $this->hasMany(Album::class,'user_id','id');
    }

     /**
     * 定义 用户和用户相册让图片 一对多
     *
     * @return void
     */
    public function picture()
    {
        return $this->hasMany(AlbumPicture::class,'user_id','id');
    }

       /**
    * 定义 用户和文章 一对多
    *
    * @return void
    */
   public function article()
   {
       return $this->hasMany(Article::class,'publisher_id','id');
   }

   /**
    * 定义 用户和银行卡 一对多
    *
    * @return void
    */
   public function userBank()
   {
        return $this->hasMany(UserBank::class,'user_id','id');

   }


   /**
    * 定义 一对对多 用户对用户余额日志
    *
    * @return void
    */
   public function amountLog()
   {
       return $this->hasMany(UserAmountLog::class,'user_id','id');
   }

   /**
    * 定义 一对对多 用户对用户余额日志
    *
    * @return void
    */
   public function coinLog()
   {
       return $this->hasMany(UserCoinLog::class,'user_id','id');
   }

   /**
    * 定义 一对对多 用户对用户余额日志
    *
    * @return void
    */
   public function scoreLog()
   {
       return $this->hasMany(UserScoreLog::class,'user_id','id');
   }

   //定义 一对多 用户对用户位置日志
   public function locationLog()
   {
	  return $this->hasMany(UserLocationLog::class,'user_id','id');
   }




    //================================================相对分割线===========================================================

    /**
     * 对应 用户对用户级别  多对一
     *
     * @return void
     */
    public function userLevel()
    {
        return $this->belongsTo(UserLevel::class,'level_id','id');
    }


    /**
     * 定义 一对多 用户对用户实名认证申请
     *
     * @return void
     */
    public function userRealAuthLog()
    {
        return $this->hasMany(UserRealAuthLog::class, 'user_id', 'id');
    }

}
