<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-12 22:27:34
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 16:38:31
 * @FilePath: \App\Facades\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\AdminPhoneBannerDetailsFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\AdminPhoneBannerDetailsFacadeService
 */
class AdminPhoneBannerDetailsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminPhoneBannerDetailsFacade";
    }
}
