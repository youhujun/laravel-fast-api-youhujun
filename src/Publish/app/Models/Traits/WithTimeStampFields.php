<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-21 07:02:26
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-21 07:03:40
 * @FilePath: app\Models\Traits\WithTimeStampFields.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;

trait WithTimeStampFields
{
    protected static function bootWithTimeStampFields()
    {
        static::creating(function (Model $model) {
            if (empty($model->created_time)) {
                $model->created_time = $model->created_at ? strtotime($model->created_at) : time();
            }
        });

        static::updating(function (Model $model) {
            $model->updated_time = time();
            if (empty($model->updated_at)) {
                $model->updated_at = date('Y-m-d H:i:s');
            }
        });
    }
}
