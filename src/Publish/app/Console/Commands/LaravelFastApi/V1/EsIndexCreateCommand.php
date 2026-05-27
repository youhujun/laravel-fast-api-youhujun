<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-19 16:06:31
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-20 14:04:12
 * @FilePath: \youhu-laravel-api-12\app\Console\Commands\LaravelFastApi\V1\EsIndexCreateCommand.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Console\Commands\LaravelFastApi\V1;

use Illuminate\Console\Command;
use App\Facades\Common\V1\Es\Console\EsCreateIndexFacade;
/**
 * @see \App\Services\Facade\Common\V1\Es\Console\EsCreateIndexFacadeService
 */
class EsIndexCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:es';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create elsticsearch index';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('开始创建es索引');

        EsCreateIndexFacade::createEsIndex();

        $this->line('创建es索引结束');
    }
}
