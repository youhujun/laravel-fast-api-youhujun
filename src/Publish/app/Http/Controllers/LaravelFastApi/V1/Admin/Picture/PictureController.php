<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-01 22:38:55
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-05 22:43:37
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\Picture\PictureController.php
 */


namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Picture;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckArray;
use App\Rules\Pub\FormatTime;
use App\Rules\Pub\CheckUnique;
use App\Rules\Pub\ChineseCodeNumberLine;
use App\Exceptions\Common\RuleException;

use App\Facade\LaravelFastApi\V1\Admin\Picture\AdminPictureFacade;

class PictureController extends Controller
{
    /**
     * 设为封面
     *
     * @param Request $request
     * @return void
     */
    public function setCover(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
               [
                  'picture_id'=>['bail',new Required,new Numeric],
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['picture_id'])) {
               throw new RuleException('RuleRequiredError', 'picture_id');
           }

            if(isDevelop($admin)|| isSuperAdmin($admin))
            {
                $result = AdminPictureFacade::setCover( $validated ,$admin);
            }
            else
            {
                if(Gate::forUser($admin)->allows('set-cover',$validated['id']))
                {
                    $result = AdminPictureFacade::setCover( $validated ,$admin);
                }
            }
        }
        return $result;
    }

     /**
     * 转移相册
     *
     * @param Request $request
     * @return void
     */
    public function moveAlbum(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
               [
                  'album_id'=>['bail',new Required,new Numeric],
                  'picture_id'=>['bail',new Required,new Numeric],
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['album_id'])) {
               throw new RuleException('RuleRequiredError', 'album_id');
           }

           if (!isset($validated['pictureId'])) {
               throw new RuleException('RuleRequiredError', 'pictureId');
           }

             if(isDevelop($admin)|| isSuperAdmin($admin))
            {
                $result = AdminPictureFacade::moveAlbum( $validated ,$admin);
            }
            else
            {
                if(Gate::forUser($admin)->allows('move-album',[$validated['album_id'],$validated['picture_id']]))
                {
                    $result = AdminPictureFacade::moveAlbum( $validated ,$admin);
                }
            }
        }
        return $result;
    }

    /**
     * 批量转移相册
     *
     * @param Request $request
     * @return void
     */
    public function moveMultipleAlbum(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
               [
                  'album_id'=>['bail',new Required,new Numeric],
                  'pictureId'=>['bail',new Required,new CheckArray]
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['album_id'])) {
               throw new RuleException('RuleRequiredError', 'album_id');
           }

           if (!isset($validated['pictureId'])) {
               throw new RuleException('RuleRequiredError', 'pictureId');
           }

             if(isDevelop($admin)|| isSuperAdmin($admin))
            {
                $result = AdminPictureFacade::moveMultipleAlbum( $validated ,$admin);
            }
            else
            {
                if(Gate::forUser($admin)->allows('move-multiple-album',[$validated['album_id'],$validated['pictureId']]))
                {
                    $result = AdminPictureFacade::moveMultipleAlbum( $validated ,$admin);
                }
            }
        }
        return $result;
    }



    /**
     * 删除图片
     *
     * @param Request $request
     * @return void
     */
    public function deletePicture(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
               [
                  'picture_id'=>['bail',new Required,new Numeric],
               ],
               []
           );

           $validated = $validator->validated();

             if(isDevelop($admin)|| isSuper($admin))
            {
                $result = AdminPictureFacade::deletePicture( $validated ,$admin);
            }
            else
            {
                if(Gate::forUser($admin)->allows('delete-picture',$validated['picture_id']))
                {
                    $result = AdminPictureFacade::deletePicture( $validated ,$admin);
                }
            }
        }

        return $result;
    }


    /**
     * 批量删除图片
     *
     * @param Request $request
     * @return void
     */
    public function deleteMultiplePicture(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
               [
                'pictureId'=>['bail',new Required,new CheckArray]
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['album_id'])) {
               throw new RuleException('RuleRequiredError', 'album_id');
           }

           if (!isset($validated['pictureId'])) {
               throw new RuleException('RuleRequiredError', 'pictureId');
           }

             if(isDevelop($admin)|| isSuperAdmin($admin))
            {
                $result = AdminPictureFacade::deleteMultiplePicture( $validated ,$admin);
            }
            else
            {
                if(Gate::forUser($admin)->allows('delete-multiple-picture',$validated['pictureId']))
                {
                    $result = AdminPictureFacade::deleteMultiplePicture( $validated ,$admin);
                }
            }
        }

        return $result;
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function updatePictureName(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
               [
                  'picture_id'=>['bail',new Required,new Numeric],
                  'picture_name'=>['bail',new Required,new CheckString]
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['picture_id'])) {
               throw new RuleException('RuleRequiredError', 'picture_id');
           }

            if(isDevelop($admin)|| isSuperAdmin($admin))
            {
                $result = AdminPictureFacade::updatePictureName( $validated ,$admin);
            }
            else
            {
                if(Gate::forUser($admin)->allows('update-picture-name',$validated['picture_id']))
                {
                    $result = AdminPictureFacade::updatePictureName( $validated ,$admin);
                }
            }
        }

        return $result;
    }
}
