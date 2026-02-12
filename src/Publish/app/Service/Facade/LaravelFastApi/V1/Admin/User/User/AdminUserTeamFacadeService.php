<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-09 19:49:00
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-09 14:07:42
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserTeamFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Exceptions\Admin\CommonException;

use App\Events\Admin\CommonEvent;

use App\Services\Facade\Traits\V1\QueryService;

//用户
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Union\UserSourceUnion;

//响应资源
use App\Http\Resources\LaravelFastApi\V1\Admin\User\UserResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\User\UserCollection;

/**
 * @see \App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserTeamFacade
 */
class AdminUserTeamFacadeService
{
   public function test()
   {
       echo "AdminUserTeamFacadeService test";
   }

      use QueryService;

   protected static $sort = [
      1 => ['created_time','asc'],
      2 => ['created_time','desc'],
    ];


    /**
     * 获取用户的上级用户(推荐用户)
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function getUserSource($validated,$admin)
    {
        $result = code(config('admin_code.GetUserRecommendError'));

        $data = [];

        if($validated['source_id'])
        {
             $sourceUser = User::where('id',$validated['source_id'])->with(['userInfo','userAvatar','userAvatar.albumPicture'])->first();

			 //p($sourceUser);die;

            if(optional($sourceUser))
            {
                $data['data'] = new UserResource($sourceUser);
            }
        }

        $result = code(['code'=>0,'msg'=>'获取推荐用户成功!'],$data);

        return $result;
    }

     /**
     * 获取用户下级团队
     */
    public function getUserLowerTeam($validated,$admin)
    {
        $result = code(config('admin_code.GetUserLowerTeamError'));

        $this->setQueryOptions($validated);

        $where = [];
        $orWhere = [];

        if($validated['lower_type'] == 0)
        {
            $where[] = ['first_id','=',$validated['user_id']];
            $orWhere[] = ['second_id','=',$validated['user_id']];
        }

        if($validated['lower_type'] == 10)
        {
            $where[] = ['first_id','=',$validated['user_id']];
        }

         if($validated['lower_type'] == 20)
        {
            $where[] = ['second_id','=',$validated['user_id']];
        }

        $userIdArrayObject = UserSourceUnion::where($where)->orWhere($orWhere)->get();

        $userIdArray = [];

        if($userIdArrayObject->count())
        {
            foreach($userIdArrayObject as $key=>$value)
            {
                $userIdArray[] = $value->user_id;
            }

            $query = User::with(['userInfo','userAvatar'=>function($query){
             $query->orderBy('created_time','desc');
       		},'userAvatar.albumPicture'])->whereIn('id',$userIdArray);

            //查询用户
            if(isset($validated['sortType']))
            {
                $sortType = $validated['sortType'];

                $query->orderBy(self::$sort[$sortType][0],self::$sort[$sortType][1]);
            }

            $userList = $query->paginate($this->perPage,$this->columns,$this->pageName,$this->page);

            if(\optional($userList))
            {
                $result = new UserCollection($userList,['code'=>0,'msg'=>'获取推用户团队成功!'],null,null);

                //$result = code(config('admin_code.userSuccess'),['data'=> $userList ,'applyNumber'=> $userApplayRealAuthNumber,'download' => $download]);
            }
        }
        else
        {
             $result = code(['code'=>0,'msg'=>'获取推用户团队成功!']);
        }

        return  $result;

    }
}
