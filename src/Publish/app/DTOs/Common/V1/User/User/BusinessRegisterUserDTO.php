<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-24 15:36:24
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 17:31:58
 * @FilePath: \youhu-laravel-api-12\app\DTOs\Common\V1\User\User\BusinessRegisterUserDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\DTOs\Common\V1\User\User;

use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;

class BusinessRegisterUserDTO
{
    public User $userObject;
	public ?Admin $adminObject = null;
    //注册的手机号
    public ?string $phone = '';

    public ?string $password = '';
    // 字段定义（赋默认值）
    public ?string $invite_id = '';
    //邀请码
    public ?string $invite_code = '';
    //邀请者的上级id
    public ?string $source_user_uid = '';
    //后台添加的昵称
    public ?string $nick_name = '';
    //后台添加的性别
    public ?int $sex = 0;
    //添加注册来源 0后台 10 H5 20抖音 30微信公众号
    public ?int $source = 0;
   
}