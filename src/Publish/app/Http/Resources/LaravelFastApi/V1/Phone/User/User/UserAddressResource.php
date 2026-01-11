<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-02-25 09:33:24
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-25 09:42:34
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Phone\User\User\UserAddressResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Phone\User\User;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\LaravelFastApi\V1\Phone\User\System\RegionResource;

use App\Models\LaravelFastApi\V1\System\Region\Region;

class UserAddressResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;


	public static $isAutoShowRegion;

    public static function setAtuoShowRegion($isAutoShowRegion = 10)
    {
        self::$isAutoShowRegion = $isAutoShowRegion;
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

		

        if($this->resource->relationLoaded('country'))
        {
            if(!is_null($this->country))
            {
                $response['country'] = new RegionResource($this->country);
                $response['country_name'] = $this->country->region_name;
            }
        }


        if($this->resource->relationLoaded('province'))
        {
            if(!is_null($this->province))
            {
                $response['province'] = new RegionResource($this->province);
                $response['province_name'] = $this->province->region_name;
            }
        }

        if($this->resource->relationLoaded('region'))
        {
            if(!is_null($this->region))
            {
                $response['region'] = new RegionResource($this->region);
                $response['region_name'] = $this->region->region_name;
            }
        }

        if($this->resource->relationLoaded('city'))
        {
            if(!is_null($this->region))
            {
                $response['city'] = new RegionResource($this->city);
                $response['city_name'] = $this->city->region_name;
            }
        }

		if(self::$isAutoShowRegion == 20)
		{
			$provinceRegion = Region::find($this->province_id);

			if($provinceRegion)
			{
				$response['province_name'] = $provinceRegion->region_name;
			}

			$region = Region::find($this->region_id);
			
			if($region)
			{
				$response['region_name'] = $region->region_name;
			}

			$cityRegion =  Region::find($this->city_id);

			if($cityRegion)
			{
				$response['city_name'] = $cityRegion->region_name;
			}
		}
		

        return $response;
    }


}
