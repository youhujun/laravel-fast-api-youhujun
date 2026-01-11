<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 18:51:17
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 09:42:06
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\File\DownloadController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Http\Controllers\LaravelFastApi\V1\Admin\File;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\Common\RuleException;


class DownloadController extends Controller
{
     protected static $basePath;

    protected $path;

    protected $downloadPath;

        /**
     * Class constructor.
     */
    public function __construct()
    {
       self::$basePath = 'excel'.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR;
    }

    /**
     * 设置路径
     *
     * @param string $name
     * @return void
     */
    protected function setPath(string $name)
    {
        $this->path = self::$basePath."{$name}".DIRECTORY_SEPARATOR.'template.xlsx';

        $this->downloadPath = 'storage'.DIRECTORY_SEPARATOR. $this->path;
    }

    /**
     * 下载银行模板
     *
     * @return void
     */
    public function downloadBank()
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $download = '';

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $result = code(config('admin_code.DownLoadError'));

            $this->setPath('bank');

            $exists = Storage::disk('public')->exists($this->path);

            if($exists)
            {
                $download = asset($this->downloadPath);

                $result = code(['code'=>0,'msg'=>'下载成功!'],['download' => $download]);
            }
        }

        return $result;
    }
}
