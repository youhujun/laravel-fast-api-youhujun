<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-15 12:08:13
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-17 11:16:02
 * @FilePath: \src\database\migrations\Create\User\2026_01_15_120813_create_user_certifications_table.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up()
    {
        $db_connection = config('youhujun.db_connection');
        if (!Schema::connection($db_connection)->hasTable('user_certifications')) {
            Schema::connection($db_connection)->create('user_certifications', function (Blueprint $table) {
                // 物理主键
                $table->id()->comment('物理主键（自增）');

                $table->char('user_certification_uid', 20)->default('')->comment('用户认证UID');

                // 核心关联字段（和用户表一致的雪花ID）
                $table->char('user_uid', 20)->default('')->comment('关联用户表user_uid');
                // 认证类型（扩展关键：新增类型只需加枚举值，无需改表）
                $table->unsignedTinyInteger('cert_type')->comment('认证类型 10手机 20邮箱 30身份证 40人脸 50企业');
                // 认证状态（和主表的real_auth_status互补，更细粒度）
                $table->unsignedTinyInteger('cert_status')->default(10)->comment('认证状态 10未认证 20认证中 30未通过 40通过');
                // 认证时间（datetime+时间戳双存储，和主表保持一致）
                $table->dateTime('certified_at')->nullable()->comment('认证通过时间');
                $table->unsignedInteger('certified_time')->default(0)->comment('认证通过时间戳');
                // 扩展字段
                $table->string('auditor_uid', 20)->nullable()->comment('审核人admin_uid');
                $table->string('cert_remark', 255)->nullable()->comment('认证备注/失败原因');
                $table->dateTime('expired_at')->nullable()->comment('认证有效期');

                // 基础时间字段
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');

                // 索引
                $table->unique('user_certification_uid', 'uni_user_certifications_uid');
                $table->unique(['user_uid', 'cert_type'], 'uni_user_certifications_uid_type');
                $table->index('user_uid', 'idx_user_certifications_user_uid');
                $table->index('cert_type', 'idx_user_certifications_cert_type');
                $table->index('cert_status', 'idx_user_certifications_cert_status');
                $table->index('certified_time', 'idx_user_certifications_certified');
                $table->index('created_time', 'idx_user_certifications_created');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');
            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_certifications` comment '用户认证信息表'");
        }
    }

    public function down()
    {
        $db_connection = config('youhujun.db_connection');
        if (Schema::connection($db_connection)->hasTable('user_certifications')) {
            Schema::connection($db_connection)->dropIfExists('user_certifications');
        }
    }
};
