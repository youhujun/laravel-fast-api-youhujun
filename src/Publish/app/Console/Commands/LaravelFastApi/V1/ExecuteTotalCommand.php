<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-02-06 09:23:24
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-14 13:23:43
 * @FilePath: \app\Console\Commands\LaravelFastApi\V1\ExecuteTotalCommand.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Console\Commands\LaravelFastApi\V1;

use Illuminate\Console\Command;

use App\Facades\Common\V1\Total\TotalAllDataFacade;

class ExecuteTotalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:total';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'implement data statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //执行所有统计
        TotalAllDataFacade::doAllTotal();
    }
}
