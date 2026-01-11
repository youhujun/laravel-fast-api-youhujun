<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-31 17:21:47
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-12-31 17:26:20
 * @FilePath: \app\Http\Resources\System\SystemConfig\OtherPlatform\SystemDouyinConfigResource.php
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform;

use Illuminate\Http\Resources\Json\JsonResource;

class SystemDouyinConfigResource extends JsonResource
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

        if(\is_array($this->resource))
        {
             $response = [
                'id'=>$this->resource['id'],
                'name'=>$this->resource['name'],
                'type'=>$this->resource['type'],
                'appid'=>$this->resource['appid'],
                'appsecret'=>$this->resource['appsecret'],
                'note'=>$this->resource['note'],
                'created_at'=>$this->resource['created_at'],
                'sort'=>$this->resource['sort'],
            ];
        }

        if(\is_object($this->resource))
        {
             $response = [
                'id'=>$this->resource->id,
                'name'=>$this->resource->name,
                'type'=>$this->resource->type,
                'appid'=>$this->resource->appid,
                'appsecret'=>$this->resource->appsecret,
                'note'=>$this->resource->note,
                'created_at'=>$this->resource->created_at,
                'sort'=>$this->resource->sort
            ];
        }

        return $response;
    }


}
