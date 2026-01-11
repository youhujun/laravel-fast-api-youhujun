<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-04-07 12:00:42
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-15 17:19:23
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\User\UserRealAuthLogResource.php
 */


namespace App\Http\Resources\LaravelFastApi\V1\Admin\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRealAuthLogResource extends JsonResource
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
        // return parent::toArray($request);

        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'sort'=>$this->sort,
            'status'=>$this->status,
            'auth_apply_at'=>$this->auth_apply_at,
            'auth_at'=>$this->auth_at,
            'refuse_info'=>$this->refuse_info
        ];
    }


}
