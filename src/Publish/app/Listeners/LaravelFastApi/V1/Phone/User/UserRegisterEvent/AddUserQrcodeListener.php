<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 16:57:21
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-21 03:21:06
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserQrcodeListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\DB;
use App\Exceptions\Phone\CommonException;

use App\Facade\Pub\V1\Qrcode\PubQrcodeFacade;

use App\Models\LaravelFastApi\V1\User\Info\UserQrcode;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\Picture\Album;

/**
 * @see \App\Events\LaravelFastApi\V1\Phone\User\UserRegisterEvent
 */
class AddUserQrcodeListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $user = $event->user;
        $validated = $event->validated;
        $isTransation = $event->isTransation;
		$isOpenQrcode = $event->isOpenQrcode;

		if($isOpenQrcode)
		{
			//第一步先生成二维码
			PublicQrcodeFacade::makeQrcdoeWithUser($user);
			//第二步 存入图片
			$albumPicture = new AlbumPicture;
			$albumPicture->user_id = $user->id;

			$where = [];

			$where[] = ['user_id','=',$user->id];
			$where[] = ['album_type','=',20];

			$album = Album::where($where)->first();

			if(!$album)
			{
				if($isTransation)
				{
					DB::rollBack();
				}
				throw new CommonException('FindUserAlbumError');
			}

			$albumPicture->album_id = $album->id;
			$albumPicture->user_id = $user->id;
			$albumPicture->created_at = time();
			$albumPicture->created_time = time();
			$albumPicture->picture_name = "{$user->id}_qrcode";
			$albumPicture->picture_path = "/user/album/{$user->id}";
			$albumPicture->picture_size = 30;
			$albumPicture->picture_spec = "300x300";
			$albumPicture->picture_file = "{$user->id}_qrcode.png";;
			$albumPictureResult = $albumPicture->save();

			if(!$albumPictureResult)
			{
				if($isTransation)
				{
					DB::rollBack();
				}

				throw new CommonException('SaveUserQrcodeError');
			}

			//第三步,存入二维码
			$userQrcode = new UserQrcode;
			$userQrcode->user_id = $user->id;
			$userQrcode->album_picture_id =  $albumPicture->id;
			$userQrcode->created_at =  time();
			$userQrcode->created_time =  time();
			$userQrcode->is_default =  1;

			$userQrcodeResult = $userQrcode->save();

			if(!$userQrcodeResult)
			{
				if($isTransation)
				{
					DB::rollBack();
				}

				throw new CommonException('AddUserQrcodeError');
			}
		}
    }
}
