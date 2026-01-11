<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-10-02 11:33:31
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-25 09:37:44
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Phone\User\User\UserDefaultAddressResource.php
 */

namespace App\Http\Resources\LaravelFastApi\V1\Phone\User\User;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\LaravelFastApi\V1\System\Region\Region;

class UserDefaultAddressResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

    public static $replaceType;

    public static function setReplaceType($replaceType = 10)
    {
        self::$replaceType = $replaceType;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

       $response = [];

        if(\is_object($this->resource))
        {
			$response = [
				'id'=>$this->id,
				'created_at'=>$this->created_at,
				'is_default'=>$this->is_default,
				'is_top'=>$this->is_top,
				'user_name'=>$this->user_name,
				'phone'=>$this->phone,
				'country_id'=>$this->country_id,
				'province_id'=>$this->province_id,
				'region_id'=>$this->region_id,
				'city_id'=>$this->city_id,
				'address_info'=>$this->address_info,
				'address_type'=>$this->address_type
			];

			$provinceObjet = Region::where('id',$this->resource->province_id)->first();

			if($provinceObjet)
			{
				$response['province_name'] = $provinceObjet->region_name;
			}

			$regionObject = Region::where('id',$this->resource->region_id)->first();

			if($regionObject)
			{
				$response['region_name'] = $regionObject->region_name;
			}

			$cityObject = Region::where('id',$this->resource->city_id)->first();

			if($cityObject)
			{
				$response['city_name'] = $cityObject->region_name;
			}


          
        }

        return $response;
    }


}
