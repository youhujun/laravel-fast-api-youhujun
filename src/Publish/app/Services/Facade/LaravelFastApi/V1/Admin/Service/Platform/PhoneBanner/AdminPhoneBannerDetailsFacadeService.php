<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-12 22:27:34
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-10 16:00:58
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\AdminPhoneBannerDetailsFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Services\Facade\Traits\V1\Es\EsAlwaysService;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\Details\UpdatePhoneBannerBakInfoDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\Details\UpdatePhoneBannerPictureDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\Details\UpdatePhoneBannerUrlDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\Details\UpdatePhoneBannerSortDTO;
use App\Models\LaravelFastApi\V1\System\Phone\PhoneBanner;
use App\Models\LaravelFastApi\V1\Admin\Admin;

/**
 * @see \App\Http\Controllers\Admin\Service\Platform\PhoneBanner\PhoneBannerDetailsController
 * @see \App\Facades\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\AdminPhoneBannerDetailsFacade
 */
class AdminPhoneBannerDetailsFacadeService
{
    public function test()
    {
        echo "AdminPhoneBannerDetailsFacadeService test";
    }

    protected $responseMessage = [
        'Picture' => '更新轮播图成功!',
        'Url' => '更新图片链接成功!',
        'Sort' => '更新图片排序成功!',
        'RemarkInfo' => '更新备注信息成功!'
    ];

    /**
     * 公共的更新轮播图
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    protected function updatePhoneBannerCommon($validated, Admin $adminObject, $errorKeyName)
    {
        $result = code(config("code.UpdatePhoneBanner{$errorKeyName}Error"));

        $phoneBannerObject = PhoneBanner::find($validated['id']);

        if (empty($phoneBannerObject)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateDataArray = [];

        $where[] = ['revision','=',$phoneBannerObject ->revision];

        $phoneBannerObject->admin_id = $adminObject->biz_id;

        foreach ($validated as $key => $value) {
            if ($key === 'id') {
                $where[] = ['id','=',$value];
                continue;
            }

            $updateDataArray[$key] = $value;
        }

        $updateDataArray['revision'] = $phoneBannerObject ->revision + 1;

        $updateDataArray['updated_at']  = date('Y-m-d H:i:s', time());

        $updateDataArray['updated_time'] = time();

        $phoneBannerResult = PhoneBanner::where($where)->update($updateDataArray);

        if (!$phoneBannerResult) {
            throw new CommonException("updatePhoneBanner{$errorKeyName}Error");
        }

        $phoneBannerObject = $phoneBannerObject->fresh();

        $indexName = config('common_es.indices.business.phone_banners');

        $updateDataArray = [
            '_docId' => $phoneBannerObject->id,
            'id' => $phoneBannerObject->id,
            'album_picture_uid' => $phoneBannerObject->album_picture_uid,
            'redirect_url' => $phoneBannerObject->redirect_url,
            'note' => $phoneBannerObject->note,
            'sort' => $phoneBannerObject->sort,
            'created_time' => $phoneBannerObject->created_time,
            'updated_time' => $phoneBannerObject->updated_time,
            'created_at' => $phoneBannerObject->created_at,
            'updated_at' => $phoneBannerObject->updated_at,
            'deleted_at' => $phoneBannerObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($indexName, $phoneBannerObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新手机轮播图失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$phoneBannerObject' => $phoneBannerObject,'$adminObject' => $adminObject], 'AdminPhoneBannerDetailsFacadeService', 'updatePhoneBannerCommonError');
            throw new CommonException("EsUpdatePhoneBanner{$errorKeyName}Error");
        }

        CommonEvent::dispatch($adminObject, $phoneBannerObject, "UpdatePhoneBanner{$errorKeyName}");

        $result = code(['code' => 0,'msg' => $this->responseMessage[$errorKeyName]]);

        return $result;
    }

    /**
     * 更新图片相册
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updatePhoneBannerPicture(UpdatePhoneBannerPictureDTO $requestDTO, Admin $adminObject)
    {
        $validated = $requestDTO->toArray();
        $result = $this->updatePhoneBannerCommon($validated, $adminObject, 'Picture');

        return $result;
    }

    /**
     * 修改轮播图跳转
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updatePhoneBannerUrl(UpdatePhoneBannerUrlDTO $requestDTO, Admin $adminObject)
    {
        $validated = $requestDTO->toArray();
        $result = $this->updatePhoneBannerCommon($validated, $adminObject, 'Url');

        return $result;
    }

    /**
     * 修改轮播图排序
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updatePhoneBannerSort(UpdatePhoneBannerSortDTO 	$requestDTO, Admin $adminObject)
    {
        $validated = $requestDTO->toArray();
        $result = $this->updatePhoneBannerCommon($validated, $adminObject, 'Sort');

        return $result;
    }

    /**
     * 修改轮播图备注
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updatePhoneBannerBakInfo(UpdatePhoneBannerBakInfoDTO $requestDTO, Admin $adminObject)
    {
        $validated = $requestDTO->toArray();

        $result = $this->updatePhoneBannerCommon($validated, $adminObject, 'RemarkInfo');

        return $result;
    }
}
