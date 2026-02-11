<?php

/*
 * @Descripttion: 支付行为主表（支撑全系统支付/退款/对账核心逻辑）
 * @version: 1.0
 * @Author: YouHuJun
 * @Date: 2026-01-19 10:00:00
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-10 23:13:31
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'payments';
    protected $hasSnowflake = true;
    // 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
    protected $shardKeyAnchor = 'payer_uid';
    protected $tableComment = '支付行为主表（记录每笔支付的全量信息，支撑合并支付/分次支付/退款/对账等场景）';

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i;
            if (!Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->create($tableName, function (Blueprint $table) use ($i) {
                    // 1. 物理自增主键（仅数据库层面使用，业务代码只操作payment_uid）
                    $table->id()->comment('物理主键（自增）');

                    // 2. 雪花ID核心业务字段（和user_uid命名/类型统一，适配分库分表）
                    $table->unsignedBigInteger('payment_uid')->default(0)->comment('支付全局唯一ID,雪花ID,业务核心ID');

                    // 3. 关联字段（统一用uid后缀，和系统其他表对齐）
                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:payer_uid%table_count(工具包自动计算)');
                    $table->unsignedBigInteger('payer_uid')->default(0)->comment('支付主体UID（用户/商户/机构，关联对应表的*_uid）');
                    $table->unsignedBigInteger('order_uid')->nullable()->comment('主订单UID（单支付对应单主订单时用）');
                    $table->unsignedBigInteger('refund_uid')->nullable()->default(0)->comment('关联退款UID（退款场景用）');
                    $table->unsignedBigInteger('operator_uid')->nullable()->default(0)->comment('操作人UID（后台人工干预时记录）');

                    // 4. 支付核心业务字段
                    $table->string('payment_no', 64)->comment('系统内部支付单号（唯一，用于对账/展示）');
                    $table->string('out_trade_no', 64)->nullable()->comment('第三方支付流水号（微信/支付宝/银行等）');
                    $table->decimal('total_amount', 16, 2)->comment('支付总金额（单位：元，精确到分）');
                    $table->decimal('actual_paid_amount', 16, 2)->nullable()->comment('实际支付金额（可能有优惠/减免）');
                    $table->decimal('fee_amount', 16, 2)->nullable()->default(0.00)->comment('支付手续费金额');
                    $table->string('pay_channel', 32)->comment('支付渠道：wechat/ali_pay/bank_card/digital_cny等');
                    $table->string('pay_sub_channel', 64)->nullable()->comment('支付子渠道：wechat_app/wechat_h5/ali_pay_wap等');

                    // 5. 状态字段（枚举值明确，和系统状态设计统一）
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->unsignedTinyInteger('status')->default(0)->comment('支付状态：0-待支付 1-支付中 2-支付成功 3-支付失败 4-退款中 5-已退款 6-已关闭');
                    $table->unsignedTinyInteger('fail_type')->nullable()->comment('失败类型：1-用户取消 2-超时 3-渠道异常 4-风控拦截 5-其他');
                    $table->string('fail_reason', 255)->nullable()->comment('支付失败原因（详细描述）');
                    $table->unsignedTinyInteger('data_source')->default(1)->comment('数据来源：1-前端支付 2-后台操作 3-第三方回调 4-系统自动');

                    // 6. 时间字段（统一双时间格式：datetime + timestamp，适配不同查询场景）
                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');
                    $table->dateTime('paid_at')->nullable()->comment('实际支付成功时间');
                    $table->dateTime('closed_at')->nullable()->comment('支付关闭时间');

                    // 7. 扩展字段（预留+原始数据，保障可溯源）
                    $table->text('callback_data')->nullable()->comment('第三方支付回调原始数据（JSON格式，完整留存）');
                    $table->text('extend_params')->nullable()->comment('扩展参数（JSON格式，存储渠道特殊参数）');

                    // 8. 索引设计（核心优化：复合唯一+业务索引，适配软删除）
                    $table->unique('payment_uid', 'uni_payments_payment_uid_' . $i);
                    $table->unique(['payment_no', 'deleted_at'], 'uni_payments_payno_del_' . $i); // 支付单号+软删除 复合唯一
                    $table->unique(['out_trade_no', 'pay_channel', 'deleted_at'], 'uni_payments_outno_ch_del_' . $i); // 第三方流水+渠道 复合唯一（避免重复回调）
                    $table->index('payer_uid', 'idx_payments_payer_uid_' . $i);
                    $table->index('order_uid', 'idx_payments_order_uid_' . $i);
                    $table->index('refund_uid', 'idx_payments_refund_uid_' . $i);
                    $table->index('status', 'idx_payments_status_' . $i);
                    $table->index('pay_channel', 'idx_payments_pay_channel_' . $i);
                    $table->index('paid_at', 'idx_payments_paid_at_' . $i);
                    $table->index('created_time', 'idx_payments_cre_time_' . $i); // 时间戳索引（高效范围查询）
                });

                $prefix = config('database.connections.'.$dbConnection.'.prefix');

                DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$tableName}` comment '{$this->tableComment}-分表{$i}'");
            }
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i;
            if (Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->dropIfExists($tableName);
            }
        }
    }
};
