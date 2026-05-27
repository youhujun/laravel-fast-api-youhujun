<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-23 15:07:06
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 17:31:33
 * @FilePath: \youhu-laravel-api-12\app\DTOs\Contracts\V1\User\User\AddUserHandlerContractDTO.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\DTOs\Contracts\V1\User\User;

use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;

class AddUserHandlerContractDTO
{
    public User $userObject;
	public ?Admin $adminObject = null;
    // 字段定义（赋默认值）
    public ?string $invite_id = '';
    //邀请码
    public ?string $invite_code = '';

    //邀请者的上级id
    public ?string $source_user_uid = '';
}