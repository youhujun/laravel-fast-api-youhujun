<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-19 20:54:11
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-20 14:03:46
 * @FilePath: \youhu-laravel-api-12\app\Console\Commands\LaravelFastApi\V1\SyncEsDataCommand.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Console\Commands\LaravelFastApi\V1;

use Illuminate\Console\Command;
use App\Facades\Common\V1\Es\Console\EsSyncDataFacade;

/**
 * @see \App\Services\Facade\Common\V1\Es\Console\EsSyncDataFacadeService
 */
class SyncEsDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:es {indexName?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '批量同步es数据';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $indexName = $this->argument('indexName');

        if (empty($indexName)) {
            $indexName = 'all';
        }

        EsSyncDataFacade::runSyncData($indexName);
    }
}
