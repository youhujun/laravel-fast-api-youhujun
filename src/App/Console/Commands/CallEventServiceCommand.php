<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-02-09 13:42:27
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-16 11:01:05
 */

namespace YouHuJun\LaravelFastApi\App\Console\Commands;

use Illuminate\Console\Command;

class CallEventServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call:event {eventName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "call create event and it's listener command ";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //php artisan make:event LaravelFastApi\V1\Admin\System\Permission\UpdateMenuEvent
        $eventName = $this->argument('eventName');

        //php artisan make:listen LaravelFastApi\V1\Admin\System\Permission\UpdateMenuEvent\UpdateMenuLisener

        // 1. 找到最后一个 \ 的位置
        $lastSlashPos = strrpos($eventName, '\\');

        // 2. 截取最后一段: UpdateMenuEvent
        $lastPart = substr($eventName, $lastSlashPos + 1);

        // 3. 原生函数替换: Event → Listener
        $listenerLastPart = str_replace('Event', 'Listener', $lastPart);

        // 4. 拼接最终路径
        $listenerName = $eventName . '\\' . $listenerLastPart;


        $this->call('make:event', [
            'name' => $eventName
        ]);

        $this->call('make:listen', [
            'name' => $listenerName
        ]);
    }
}
