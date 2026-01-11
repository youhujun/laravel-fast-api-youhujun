<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-19 13:01:10
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-18 20:02:19
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\System\SystemConfig\Admin\SystemVoiceConfigResouce.php
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\System\SystemConfig\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class SystemVoiceConfigResouce extends JsonResource
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
                'id'=>$this->resource->id,
                'voice_title'=>$this->resource->voice_title,
                'channle_name'=>$this->resource->channle_name,
                'channle_event'=>$this->resource->channle_event,
				'voice_save_type'=>$this->resource->voice_save_type,
				'voice_url'=>$this->resource->voice_url,
				'voice_path'=>$this->resource->voice_path,
				'voice_file'=>$this->resource->voice_file,
                'note'=>$this->resource->note,
                'created_at'=>$this->resource->created_at,
                'sort'=>$this->resource->sort,
            ];

            //本地存储
            if($this->resource->voice_save_type == 10)
            {
				$response['voice_use_url'] = asset('/storage'.DIRECTORY_SEPARATOR.$this->resource->voice_path);
				
				if($this->resource->voirce_file){
					$response['voice_use_url'] = asset('/storage'.DIRECTORY_SEPARATOR.$this->resource->voice_path.DIRECTORY_SEPARATOR.$this->resource->voirce_file);
				}
                 
            }

            //存储桶存储
            if($this->resource->voice_save_type == 20)
            {
                $response['voice_use_url'] = $this->resource->voice_url;
            }

        }

        return $response;
    }


}
