<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-01 22:38:55
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-04 00:31:45
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\Picture\AlbumController.php
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

use App\Rules\LaravelFastApi\V1\Admin\Common\sortType;

use App\Facades\LaravelFastApi\V1\Admin\Picture\AdminAlbumFacade;

class AlbumController extends Controller
{
    /**
     * 获取默认相册
     *
     * @param Request $request
     * @return void
     */
    public function getDefaultAlbum(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'album_type'=>['bail',new Required,new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['albumId'])) {
                throw new RuleException('RuleRequiredError', 'albumId');
            }

            if (!isset($validated['sortType'])) {
                throw new RuleException('RuleRequiredError', 'sortType');
            }

            //p($validated);die;
            $result =  AdminAlbumFacade::getDefaultAlbum(f($validated),$admin);
        }

        return $result;
    }


    /**
     * 查找相册
     *
     * @param Request $request
     * @return void
     */
    public function findAlbum(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'find' => ['bail','nullable',new CheckString],
                    'album_type'=>['bail',new Required,new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['albumId'])) {
                throw new RuleException('RuleRequiredError', 'albumId');
            }

            if (!isset($validated['sortType'])) {
                throw new RuleException('RuleRequiredError', 'sortType');
            }

            //p($validated);die;

            $result =  AdminAlbumFacade::findAlbum(f($validated),$admin);
        }

        return $result;
    }
    /**
     * 获取相册
     *
     * @param AlbumRequest $request
     * @return void
     */
    public function getAlbum(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        if (Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
               [
                   'album_type'=>['bail','nullable',new Numeric],
                   'sortType'=>['bail','nullable',new Numeric,new sortType],
                   'currentPage'=>['bail','nullable',new Numeric],
                   'pageSize'=>['bail','nullable',new Numeric],

               ],
               []
           );

           $validated = $validator->validated();
           //p($validated);die;
           $result = AdminAlbumFacade::getAlbum(f($validated),$admin);
        }

        return $result;
    }

    /**
     * 添加相册
     *
     * @param Request $request
     * @return void
     */
    public function addAlbum(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.apiAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'album_type'=>['bail',new Required,new Numeric],
                    'album_name' => ['bail',new Required,new CheckString,new CheckBetween(1,30),new ChineseCodeNumberLine],
                    'album_description' => ['bail','nullable',new CheckBetween(0,50)],
                    'sort'=>['bail',new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['album_type'])) {
                throw new RuleException('RuleRequiredError', 'album_type');
            }

            if (!isset($validated['album_name'])) {
                throw new RuleException('RuleRequiredError', 'album_name');
            }

            if (!isset($validated['sort'])) {
                throw new RuleException('RuleRequiredError', 'sort');
            }

            $result = AdminAlbumFacade::addAlbum(f($validated),$admin);
        }

        return $result;
    }

    /**
     * 更新相册
     *
     * @param Request $request
     * @return void
     */
    public function updateAlbum(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.apiAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'album_type'=>['bail',new Required,new Numeric],
                    'album_name' => ['bail',new Required,new CheckString,new CheckBetween(1,30),new ChineseCodeNumberLine],
                    'album_description' => ['bail','nullable',new CheckBetween(0,50)],
                    'sort'=>['bail',new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['album_type'])) {
                throw new RuleException('RuleRequiredError', 'album_type');
            }

            if (!isset($validated['album_name'])) {
                throw new RuleException('RuleRequiredError', 'album_name');
            }

            if (!isset($validated['sort'])) {
                throw new RuleException('RuleRequiredError', 'sort');
            }

            /**
             * @see \App\Policies\Admin\Picture\AlbumPolicy
             */
            if(Gate::forUser($admin)->allows('update-album',[$validated]))
            {
                $result = AdminAlbumFacade::updateAlbum($validated,$admin);
            }
        }
        return $result;
    }

    /**
     * 删除相册
     *
     * @param Request $request
     * @return void
     */
    public function deleteAlbum(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.apiAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['albumId'])) {
                throw new RuleException('RuleRequiredError', 'albumId');
            }

            if (!isset($validated['sortType'])) {
                throw new RuleException('RuleRequiredError', 'sortType');
            }

            //p($validated);die;

            /**
             * @see \App\Policies\Admin\PictureAlbumPolicy
             */
            if(Gate::forUser($admin)->allows('delete-album',[$validated]))
            {
                $result = AdminAlbumFacade::deleteAlbum($validated,$admin);
            }
        }
        return $result;
    }

    /**
     * 查询相册
     *
     * @param Request $request
     * @return void
     */
    public function getAlbumPicture(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(config('admin_code.apiAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'albumId'=> ['bail',new Required,new Numeric],
                    'sortType'=>['bail',new Required,new Numeric],
                    'currentPage'=>['bail','nullable',new Numeric],
                    'pageSize'=>['bail','nullable',new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['albumId'])) {
                throw new RuleException('RuleRequiredError', 'albumId');
            }

            if (!isset($validated['sortType'])) {
                throw new RuleException('RuleRequiredError', 'sortType');
            }

            //p($validated);die;

            /**
             * @see \App\Policies\Admin\Picture\AlbumPolicy
             */
            if(Gate::forUser($admin)->allows('get-album-picture',[$validated]))
            {
                $result = AdminAlbumFacade::getAlbumPicture($validated,$admin);
            }

        }

        return $result;
    }
}
