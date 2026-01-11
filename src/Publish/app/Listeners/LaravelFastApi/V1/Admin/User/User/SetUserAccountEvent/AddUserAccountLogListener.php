<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-26 14:53:58
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-03 15:42:30
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\User\User\SetUserAccountEvent\AddUserAccountLogListener.php
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\User\User\SetUserAccountEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\User\Log\UserAmountLog;
use App\Models\LaravelFastApi\V1\User\Log\UserCoinLog;
use App\Models\LaravelFastApi\V1\User\Log\UserScoreLog;
use App\Models\LaravelFastApi\V1\User\Info\UserAmount;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\User\User\SetUserAccountEvent
 */
class AddUserAccountLogListener
{
    protected $model = [
       10 => UserAmountLog::class,
       20 => UserCoinLog::class,
       30 => UserScoreLog::class,
   ];

   protected $note = [
       10 => [
            10 => '后台充值余额',
            20 => '后台充值系统币',
            30 => '后台增加积分'
       ],
       20 => [
            10 => '后台扣除余额',
            20 => '后台扣除系统币',
            30 => '后台扣除积分'
       ]
    ];

	protected $field = [
		10=>'amount',
		20=>'coin',
		30=>'score'
	];
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
        $admin = $event->admin;
        $validated = $event->validated;
        $isTransation = $event->isTransation;

		$userAmount = UserAmount::where('user_id',$validated['user_id'])->first();

        $amountLog =  new $this->model[$validated['account_type']];

		$amountLog->change_value = $validated['amount'];
        $amountLog->created_at = time();
        $amountLog->created_time = time();
        $amountLog->sort = 100;
        $amountLog->change_type = $validated['action_type'];
        $amountLog->user_id = $validated['user_id'];

		$beforeAmount = 0;
		//记录之前的金额
		$beforeAmountLog = $this->model[$validated['account_type']]::where('user_id',$validated['user_id'])->orderBy('created_time','desc')->first();

		if($beforeAmountLog){
			$beforeAmount = $beforeAmountLog->amount;
		}else{
			$beforeAmount = 0;
		}

		$amountLog->before_amount = $beforeAmount;
		//记录现在的金额
		$fieldName = $this->field[$validated['account_type']];
		$amountLog->amount = $userAmount->{$fieldName}; //
       
        $amountLog->note = $this->note[$validated['action_type']][$validated['account_type']];

        $amountLogResult = $amountLog -> save();	

        if(!$amountLogResult)
        {
            if($isTransation)
            {
                DB::rollBack();
            }

            throw new  CommonException('SetUserAccountLogError');
        }
    }
}
