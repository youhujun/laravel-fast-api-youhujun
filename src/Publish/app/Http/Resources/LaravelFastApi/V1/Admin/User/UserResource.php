<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-04-05 14:53:13
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-03 16:11:35
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\User;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\LaravelFastApi\V1\Admin\User\UserInfoResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\User\UserAvatarResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\User\UserAddressResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\System\RoleResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\User\UserApplyRealAuthResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\User\UserIdCardResource;
use App\Models\LaravelFastApi\V1\User\Info\UserAmount;

use App\Models\LaravelFastApi\V1\System\Level\UserLevel;

class UserResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);

		$userAmount = UserAmount::where('user_id',$this->resource->id)->first();

        $response = [
            'id'=>$this->resource->id,
			'userId'=>$this->resource->userId,
            'created_at'=>$this->resource->created_at,
            'switch'=>$this->resource->switch ,
            'level_id'=>$this->resource->level_id,
            'parent_id'=>$this->resource->parent_id,
            'account_name'=>$this->resource->account_name,
            'invite_code'=>$this->resource->invite_code,
            'phone'=>$this->resource->phone ,
            'real_auth_status'=>$this->resource->real_auth_status,
            'source_user_id'=>$this->resource->source_user_id,
            'amount'=>$userAmount->amount,
            'coin'=>$userAmount->coin,
            'score'=>$userAmount->score
        ];

        if($this->resource->relationLoaded('userInfo'))
        {
            if(!is_null($this->resource->userInfo))
            {
                //$response['user_info'] = new UserInfoResource($this->resource->userInfo);
                $userInfo = $this->resource->userInfo;

                $response['nick_name'] = $userInfo->nick_name;
                $response['real_name'] = $userInfo->real_name;
                $response['solar_birthday_at'] = $userInfo->solar_birthday_at;
                $response['chinese_birthday_at'] = $userInfo->chinese_birthday_at;
                $response['sex'] = $userInfo->sex;
                $response['id_number'] = $userInfo->id_number;
                $response['introduction'] = $userInfo->introduction;
            }
        }

        if($this->resource->relationLoaded('userAvatar'))
        {
            if(!is_null($this->resource->userAvatar))
            {
               // $response['user_avatar'] = UserAvatarResource::collection($this->resource->userAvatar);

                $userAvatar = $this->resource->userAvatar->firstWhere('is_default',1);

                if($userAvatar && $userAvatar->relationLoaded('albumPicture'))
                {
                    $albumPicture = $userAvatar->albumPicture;

                    if($albumPicture->picture_type == 0 || $albumPicture->picture_type == 10)
                    {
                        $response['avatar'] = asset('storage'.$albumPicture->picture_path.DIRECTORY_SEPARATOR.$albumPicture->picture_file);
                    }
                    else
                    {
                        $response['avatar'] = $albumPicture->picture_url;
                    }

                }
            }
        }

         if($this->resource->relationLoaded('userLevel'))
         {
            if(!is_null($this->resource->userLevel))
            {
                $userLevel = $this->resource->userLevel;

                $response['user_level'] = $userLevel->level_name;
            }
         }
         else
         {
            $userLevel = UserLevel::find($this->resource->level_id);

            if($userLevel)
            {
                 $response['user_level'] = $userLevel->level_name;
            }
         }

        if($this->resource->relationLoaded('userAddress'))
        {
            if(!is_null($this->resource->userAddress))
            {
                $response['user_address'] = UserAddressResource::collection($this->resource->userAddress);
            }
        }

        if($this->resource->relationLoaded('role'))
        {
            if(!is_null($this->resource->role))
            {
                $response['role'] = RoleResource::collection($this->resource->role);
            }
        }

        if($this->resource->relationLoaded('idCard'))
        {
            if(!is_null($this->resource->idCard))
            {
                $response['id_card'] = new UserIdCardResource($this->resource->idCard);
            }
        }

        if($this->resource->relationLoaded('userApplyRealAuth'))
        {
            if(!is_null($this->resource->userApplyRealAuth))
            {
                $response['user_apply_real_auth'] = UserApplyRealAuthResource::collection($this->resource->userApplyRealAuth);
            }
        }

        return $response;
    }
}
