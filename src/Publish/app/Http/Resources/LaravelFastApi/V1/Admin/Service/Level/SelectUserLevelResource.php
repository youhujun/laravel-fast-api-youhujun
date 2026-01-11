<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-14 08:38:19
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-24 19:52:39
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\Service\Level\SelectUserLevelResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\Service\Level;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\LaravelFastApi\V1\System\Level\Union\UserLevelItemUnion;
use App\Models\LaravelFastApi\V1\System\Level\LevelItem;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

class SelectUserLevelResource extends JsonResource
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

        $response = [
            'id'=>$this->resource->id,
            'sort'=>$this->resource->sort,
            'created_at'=>$this->resource->created_at,
            'updated_at'=>$this->resource->updated_at,
            'switch'=>$this->resource->switch,
            'level_name'=>$this->resource->level_name,
            'level_code'=>$this->resource->level_code,
            'amount'=>$this->resource->amount,
            'background_id'=>$this->resource->background_id,
            'note'=>$this->resource->note
        ];

		/* $userLevelItemUnionCollection = UserLevelItemUnion::where('user_level_id',$this->resource->id)->get();

		$levelItemArray = [];

		foreach ($userLevelItemUnionCollection as $key => $userLevelItemUnionObject) 
		{
			$levelItemObject = LevelItem::find($userLevelItemUnionObject->level_item_id);

			$itemArray = [];

			$itemArray['user_level_item_unon_id'] = $userLevelItemUnionObject->id;
			$itemArray['level_item_id'] = $levelItemObject->id;
			$itemArray['item_name'] = $levelItemObject->item_name;
			$itemArray['item_code'] = $levelItemObject->item_code;
			$itemArray['description'] = $levelItemObject->description;
			$itemArray['value_type'] =  $userLevelItemUnionObject->value_type;
			$itemArray['value'] =  $userLevelItemUnionObject->value;

			$levelItemArray[] = $itemArray;
		}

		$response['level_item'] = $levelItemArray; */

		if($this->resource->background_id)
		{
			$albumPictureObject = AlbumPicture::find($this->resource->background_id);

			$response['background'] = asset('storage'.$albumPictureObject->picture_path.DIRECTORY_SEPARATOR.$albumPictureObject->picture_file);
		}

        return $response;
    }


}
